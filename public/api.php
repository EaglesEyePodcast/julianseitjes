<?php
session_start();
require_once dirname(__DIR__) . '/config/config.php';

header('Content-Type: application/json');
$action = isset($_GET['action']) ? $_GET['action'] : '';
$db = getDB();

if ($action !== 'register' && !isset($_SESSION['julian_in'])) {
    http_response_code(401);
    echo json_encode(array('error' => 'Niet ingelogd'));
    exit;
}

if ($action !== 'register') {
    ensureKlantRouteColumn($db);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if ($action === 'week') {
        $week = isset($_GET['week']) ? (int)$_GET['week'] : (int)date('W');
        $jaar = isset($_GET['jaar']) ? (int)$_GET['jaar'] : (int)date('Y');
        $klanten = $db->query("SELECT * FROM klanten WHERE actief = 1 ORDER BY " . routeOrderSql())->fetchAll();
        $result = array();
        foreach ($klanten as $k) {
            if (!isAanDeBeurt($k['frequentie'], $k['startweek'], $week)) continue;
            $stmt = $db->prepare("SELECT * FROM leveringen WHERE klant_id = ? AND weeknummer = ? AND jaar = ?");
            $stmt->execute(array($k['id'], $week, $jaar));
            $lev = $stmt->fetch();
            $result[] = array(
                'id'           => $k['id'],
                'naam'         => $k['naam'],
                'straat'       => $k['straat'],
                'huisnummer'   => $k['huisnummer'],
                'telefoon'     => $k['telefoon'],
                'aantal_dozen' => $k['aantal_dozen'],
                'frequentie'   => $k['frequentie'],
                'bezorg_volgorde' => (int)$k['bezorg_volgorde'],
                'bezorgd'      => $lev ? (bool)$lev['bezorgd'] : false,
                'tikkie'       => $lev ? (bool)$lev['tikkie_verstuurd'] : false,
                'betaald'      => $lev ? (bool)$lev['betaald'] : false
            );
        }
        echo json_encode($result);
        exit;
    }

    if ($action === 'klanten') {
        $klanten = $db->query("SELECT * FROM klanten WHERE actief = 1 ORDER BY " . routeOrderSql())->fetchAll();
        echo json_encode($klanten);
        exit;
    }

    if ($action === 'stats') {
        $week = isset($_GET['week']) ? (int)$_GET['week'] : (int)date('W');
        $jaar = isset($_GET['jaar']) ? (int)$_GET['jaar'] : (int)date('Y');
        $klanten = $db->query("SELECT * FROM klanten WHERE actief = 1")->fetchAll();
        $totaal_dozen = 0; $betaald_dozen = 0; $open_tikkies = 0; $niet_bezorgd = 0;
        foreach ($klanten as $k) {
            if (!isAanDeBeurt($k['frequentie'], $k['startweek'], $week)) continue;
            $totaal_dozen += $k['aantal_dozen'];
            $stmt = $db->prepare("SELECT * FROM leveringen WHERE klant_id = ? AND weeknummer = ? AND jaar = ?");
            $stmt->execute(array($k['id'], $week, $jaar));
            $lev = $stmt->fetch();
            if ($lev && $lev['betaald'])            $betaald_dozen += $k['aantal_dozen'];
            if (!$lev || !$lev['tikkie_verstuurd']) $open_tikkies++;
            if (!$lev || !$lev['bezorgd'])          $niet_bezorgd++;
        }
        $overgebleven_dozen = getOvergeblevenDozen($db, $week, $jaar);
        $reserve_dozen = 3;
        $te_bestellen_dozen = max(0, $totaal_dozen + $reserve_dozen - $overgebleven_dozen);
        echo json_encode(array(
            'totaal_dozen'       => $totaal_dozen,
            'betaald_dozen'      => $betaald_dozen,
            'open_tikkies'       => $open_tikkies,
            'niet_bezorgd'       => $niet_bezorgd,
            'overgebleven_dozen' => $overgebleven_dozen,
            'reserve_dozen'      => $reserve_dozen,
            'te_bestellen_dozen' => $te_bestellen_dozen,
            'trays_nodig'        => (int)ceil($te_bestellen_dozen / 3),
            'omzet'              => round($totaal_dozen * PRIJS_VERKOOP, 2),
            'winst'              => round($totaal_dozen * (PRIJS_VERKOOP - PRIJS_INKOOP), 2)
        ));
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) $data = $_POST;

    if ($action === 'toggle_bezorgd' || $action === 'toggle_tikkie' || $action === 'toggle_betaald') {
        $klant_id = (int)$data['klant_id'];
        $week     = (int)$data['week'];
        $jaar     = (int)$data['jaar'];
        $waarde   = isset($data['waarde']) && $data['waarde'] ? 1 : 0;

        $kolom = 'bezorgd';
        if ($action === 'toggle_tikkie')  $kolom = 'tikkie_verstuurd';
        if ($action === 'toggle_betaald') $kolom = 'betaald';

        $stmt = $db->prepare("INSERT INTO leveringen (klant_id, weeknummer, jaar, aantal_dozen, " . $kolom . ") VALUES (?, ?, ?, (SELECT aantal_dozen FROM klanten WHERE id = ?), ?) ON DUPLICATE KEY UPDATE " . $kolom . " = ?");
        $stmt->execute(array($klant_id, $week, $jaar, $klant_id, $waarde, $waarde));
        echo json_encode(array('ok' => true, 'waarde' => $waarde));
        exit;
    }

    if ($action === 'klant_opslaan') {
        $naam    = trim($data['naam']);
        $straat  = trim($data['straat']);
        $huisnr  = trim($data['huisnummer']);
        $tel     = trim(isset($data['telefoon']) ? $data['telefoon'] : '');
        $dozen   = (int)$data['aantal_dozen'];
        $freq    = $data['frequentie'];
        $start   = (int)$data['startweek'];
        $route   = isset($data['bezorg_volgorde']) ? max(0, (int)$data['bezorg_volgorde']) : 0;
        $notitie = trim(isset($data['notitie'])  ? $data['notitie']  : '');
        if (!$naam || !$straat || !$huisnr || $dozen < 1) {
            echo json_encode(array('error' => 'Vul alle verplichte velden in'));
            exit;
        }
        $stmt = $db->prepare("INSERT INTO klanten (naam, straat, huisnummer, telefoon, aantal_dozen, frequentie, startweek, bezorg_volgorde, notitie) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(array($naam, $straat, $huisnr, $tel, $dozen, $freq, $start, $route, $notitie));
        echo json_encode(array('ok' => true, 'id' => $db->lastInsertId()));
        exit;
    }

    if ($action === 'klant_updaten') {
        $id      = (int)$data['id'];
        $naam    = trim($data['naam']);
        $straat  = trim($data['straat']);
        $huisnr  = trim($data['huisnummer']);
        $tel     = trim(isset($data['telefoon'])  ? $data['telefoon']  : '');
        $dozen   = (int)$data['aantal_dozen'];
        $freq    = $data['frequentie'];
        $start   = (int)$data['startweek'];
        $route   = isset($data['bezorg_volgorde']) ? max(0, (int)$data['bezorg_volgorde']) : 0;
        $notitie = trim(isset($data['notitie'])   ? $data['notitie']   : '');

        if (!$id || !$naam || !$straat || !$huisnr || $dozen < 1) {
            echo json_encode(array('error' => 'Vul alle verplichte velden in'));
            exit;
        }
        $stmt = $db->prepare("UPDATE klanten SET naam=?, straat=?, huisnummer=?, telefoon=?, aantal_dozen=?, frequentie=?, startweek=?, bezorg_volgorde=?, notitie=? WHERE id=?");
        $stmt->execute(array($naam, $straat, $huisnr, $tel, $dozen, $freq, $start, $route, $notitie, $id));
        echo json_encode(array('ok' => true));
        exit;
    }

    if ($action === 'klant_deactiveren') {
        $id = (int)$data['id'];
        $stmt = $db->prepare("UPDATE klanten SET actief = 0 WHERE id = ?");
        $stmt->execute(array($id));
        echo json_encode(array('ok' => true));
        exit;
    }

    if ($action === 'voorraad_opslaan') {
        $week = isset($data['week']) ? (int)$data['week'] : (int)date('W');
        $jaar = isset($data['jaar']) ? (int)$data['jaar'] : (int)date('Y');
        $overgebleven = isset($data['overgebleven_dozen']) ? (int)$data['overgebleven_dozen'] : 0;

        if ($week < 1 || $week > 53 || $jaar < 2020 || $overgebleven < 0) {
            echo json_encode(array('error' => 'Vul een geldig aantal overgebleven dozen in'));
            exit;
        }

        ensureWeekVoorradenTable($db);
        $stmt = $db->prepare("INSERT INTO week_voorraden (weeknummer, jaar, overgebleven_dozen) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE overgebleven_dozen = VALUES(overgebleven_dozen)");
        $stmt->execute(array($week, $jaar, $overgebleven));
        echo json_encode(array('ok' => true));
        exit;
    }

    if ($action === 'register') {
        $voornaam   = trim($data['voornaam'] ?? '');
        $achternaam = trim($data['achternaam'] ?? '');
        $naam       = trim($voornaam . ' ' . $achternaam);
        $straat     = trim($data['straat'] ?? '');
        $huisnr     = trim($data['huisnummer'] ?? '');
        $tel        = trim($data['telefoon'] ?? '');
        $email      = trim($data['email'] ?? '');
        $dozen      = (int)($data['aantal_dozen'] ?? 1);
        $freq       = $data['frequentie'] ?? 'wekelijks';
        $startweek  = (int)date('W');
        
        // Validatie
        if (!$voornaam || !$achternaam || !$straat || !$huisnr || !$tel || $dozen < 1) {
            echo json_encode(array('error' => 'Vul alle verplichte velden in'));
            exit;
        }
        if (!in_array($freq, array('wekelijks', '2wekelijks', '3wekelijks', 'eenmalig'), true)) {
            echo json_encode(array('error' => 'Kies een geldige frequentie'));
            exit;
        }
        
        $geldigeStraat = geldigeStraatNaam($db, $straat);
        if (!$geldigeStraat) {
            echo json_encode(array('error' => 'Deze straat is helaas niet in ons leveringsgebied'));
            exit;
        }
        $straat = $geldigeStraat;
        
        ensureKlantRouteColumn($db);
        $notitie = $email ? 'Ingeschreven via website. Email: ' . $email : 'Ingeschreven via website.';
        $stmt = $db->prepare("INSERT INTO klanten (naam, straat, huisnummer, telefoon, aantal_dozen, frequentie, startweek, bezorg_volgorde, actief, notitie) VALUES (?, ?, ?, ?, ?, ?, ?, 0, 1, ?)");
        $stmt->execute(array($naam, $straat, $huisnr, $tel, $dozen, $freq, $startweek, $notitie));
        $klantId = $db->lastInsertId();
        
        // Email notification naar Julian
        $bericht = "Nieuwe inschrijving voor Julian's Verse Eitjes:\n\n" .
                   "Naam: $naam\n" .
                   "Straat: $straat $huisnr\n" .
                   "Telefoon: $tel\n" .
                   "Email: $email\n" .
                   "Dozen: $dozen\n" .
                   "Frequentie: $freq\n" .
                   "Startweek: $startweek\n\n" .
                   "De klant staat direct in het klantenoverzicht:\n" .
                   "https://eitjes.kunkeler.net/public/administratie.php";
        
        sendEmailNotification('Nieuwe klant via website', $bericht);

        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $klantBericht = "Beste $voornaam,\n\n" .
                            "Bedankt voor je inschrijving bij Julian's Verse Eitjes.\n\n" .
                            "We hebben deze gegevens ontvangen:\n" .
                            "Naam: $naam\n" .
                            "Adres: $straat $huisnr\n" .
                            "Aantal dozen: $dozen\n" .
                            "Frequentie: " . frequentieLabel($freq) . "\n\n" .
                            "Julian neemt contact met je op als er nog iets afgestemd moet worden.\n\n" .
                            "Groeten,\n" .
                            "Julian's Verse Eitjes";
            sendCustomerEmail($email, 'Bevestiging inschrijving', $klantBericht);
        }
        
        echo json_encode(array('ok' => true, 'id' => $klantId));
        exit;
    }
}

echo json_encode(array('error' => 'Onbekende actie'));

function sendEmailNotification($subject, $bericht) {
    if (!defined('JULIAN_EMAIL') || !JULIAN_EMAIL) {
        return false;
    }

    $subject = EMAIL_SUBJECT_PREFIX . $subject;
    $headers = [];
    $headers[] = 'From: ' . EMAIL_FROM;
    $headers[] = 'Reply-To: ' . EMAIL_FROM;
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';

    return mail(JULIAN_EMAIL, $subject, $bericht, implode("\r\n", $headers));
}

function sendCustomerEmail($to, $subject, $bericht) {
    $subject = EMAIL_SUBJECT_PREFIX . $subject;
    $headers = array();
    $headers[] = 'From: ' . EMAIL_FROM;
    $headers[] = 'Reply-To: ' . EMAIL_FROM;
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';

    return mail($to, $subject, $bericht, implode("\r\n", $headers));
}

function frequentieLabel($frequentie) {
    if ($frequentie === 'wekelijks') return 'Elke week';
    if ($frequentie === '2wekelijks') return 'Elke 2 weken';
    if ($frequentie === '3wekelijks') return 'Elke 3 weken';
    if ($frequentie === 'eenmalig') return 'Eenmalig';
    return $frequentie;
}

function geldigeStraatNaam($db, $straat) {
    $zoek = normaliseerStraat($straat);

    foreach (standaardStraten() as $geldigeStraat) {
        if (normaliseerStraat($geldigeStraat) === $zoek) {
            return $geldigeStraat;
        }
    }

    try {
        $stmt = $db->query("SHOW TABLES LIKE 'straten'");
        if (!$stmt->fetch()) {
            return false;
        }

        $stmt = $db->prepare("SELECT naam FROM straten WHERE actief = 1");
        $stmt->execute();
        foreach ($stmt->fetchAll() as $row) {
            if (normaliseerStraat($row['naam']) === $zoek) {
                return $row['naam'];
            }
        }
    } catch (Exception $e) {
        return false;
    }

    return false;
}

function standaardStraten() {
    return array(
        '1e Weerdsweg',
        '2e Weerdsweg',
        'Anna Reynvaanstraat',
        'Borgerlerstraat',
        'Hallensstraat',
        'Jacob van Bredastraat',
        'J.P. Sweelinckstraat',
        'Johannes Sinthenstraat',
        'Kromme Kerkstraat',
        'Radstakeweg',
        'Reinckenstraat',
        'Richard Paffraedstraat',
        'Sint Jurrienstraat',
        'Zwolseweg'
    );
}

function normaliseerStraat($straat) {
    $straat = trim(strtolower($straat));
    $straat = str_replace(array('ë', 'é', 'è'), 'e', $straat);
    $straat = preg_replace('/\s+/', ' ', $straat);
    $straat = str_replace('weerdswef', 'weerdsweg', $straat);
    return $straat;
}

function getOvergeblevenDozen($db, $week, $jaar) {
    ensureWeekVoorradenTable($db);
    $stmt = $db->prepare("SELECT overgebleven_dozen FROM week_voorraden WHERE weeknummer = ? AND jaar = ?");
    $stmt->execute(array($week, $jaar));
    $row = $stmt->fetch();
    return $row ? (int)$row['overgebleven_dozen'] : 0;
}

function ensureWeekVoorradenTable($db) {
    static $checked = false;
    if ($checked) return;

    $db->exec("CREATE TABLE IF NOT EXISTS week_voorraden (
        id int(11) NOT NULL AUTO_INCREMENT,
        weeknummer int(11) NOT NULL,
        jaar int(11) NOT NULL,
        overgebleven_dozen int(11) NOT NULL DEFAULT 0,
        bijgewerkt timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (id),
        UNIQUE KEY week_jaar (weeknummer, jaar)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

    $checked = true;
}

function ensureRegistratiesTable($db) {
    static $checked = false;
    if ($checked) return;

    $db->exec("CREATE TABLE IF NOT EXISTS registraties (
        id int(11) NOT NULL AUTO_INCREMENT,
        naam varchar(100) NOT NULL,
        straat varchar(100) NOT NULL,
        huisnummer varchar(10) NOT NULL,
        telefoon varchar(20) DEFAULT '',
        email varchar(100) DEFAULT '',
        aantal_dozen int(11) NOT NULL DEFAULT 1,
        frequentie enum('wekelijks','2wekelijks','3wekelijks') NOT NULL DEFAULT 'wekelijks',
        startweek int(11) NOT NULL DEFAULT 1,
        status enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
        aangemaakt timestamp NULL DEFAULT current_timestamp(),
        goedgekeurd_op timestamp NULL,
        notitie text DEFAULT NULL,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

    $checked = true;
}

function ensureKlantRouteColumn($db) {
    static $checked = false;
    if ($checked) return;

    $stmt = $db->query("SHOW COLUMNS FROM klanten LIKE 'bezorg_volgorde'");
    if (!$stmt->fetch()) {
        $db->exec("ALTER TABLE klanten ADD bezorg_volgorde int(11) NOT NULL DEFAULT 0 AFTER startweek");
    }

    $checked = true;
}

function routeOrderSql() {
    return "(bezorg_volgorde = 0), bezorg_volgorde, straat, CAST(huisnummer AS UNSIGNED), huisnummer, naam";
}

function isAanDeBeurt($frequentie, $startweek, $weeknummer) {
    if ($frequentie === 'eenmalig')   return false;
    if ($frequentie === 'wekelijks')  return true;
    if ($frequentie === '2wekelijks') return (($weeknummer - $startweek) % 2) === 0;
    if ($frequentie === '3wekelijks') return (($weeknummer - $startweek) % 3) === 0;
    return false;
}
?>
