<?php
session_start();
require_once dirname(__DIR__) . '/config/config.php';

if (!isset($_SESSION['julian_in'])) {
    http_response_code(401);
    echo json_encode(array('error' => 'Niet ingelogd'));
    exit;
}

header('Content-Type: application/json');
$action = isset($_GET['action']) ? $_GET['action'] : '';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if ($action === 'week') {
        $week = isset($_GET['week']) ? (int)$_GET['week'] : (int)date('W');
        $jaar = isset($_GET['jaar']) ? (int)$_GET['jaar'] : (int)date('Y');
        $klanten = $db->query("SELECT * FROM klanten WHERE actief = 1 ORDER BY straat, huisnummer")->fetchAll();
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
                'bezorgd'      => $lev ? (bool)$lev['bezorgd'] : false,
                'tikkie'       => $lev ? (bool)$lev['tikkie_verstuurd'] : false,
                'betaald'      => $lev ? (bool)$lev['betaald'] : false
            );
        }
        echo json_encode($result);
        exit;
    }

    if ($action === 'klanten') {
        $klanten = $db->query("SELECT * FROM klanten WHERE actief = 1 ORDER BY straat, huisnummer")->fetchAll();
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
        echo json_encode(array(
            'totaal_dozen'  => $totaal_dozen,
            'betaald_dozen' => $betaald_dozen,
            'open_tikkies'  => $open_tikkies,
            'niet_bezorgd'  => $niet_bezorgd,
            'trays_nodig'   => ceil(($totaal_dozen * 10) / 30),
            'omzet'         => round($totaal_dozen * PRIJS_VERKOOP, 2),
            'winst'         => round($totaal_dozen * (PRIJS_VERKOOP - PRIJS_INKOOP), 2)
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
        $notitie = trim(isset($data['notitie'])  ? $data['notitie']  : '');
        if (!$naam || !$straat || !$huisnr || $dozen < 1) {
            echo json_encode(array('error' => 'Vul alle verplichte velden in'));
            exit;
        }
        $stmt = $db->prepare("INSERT INTO klanten (naam, straat, huisnummer, telefoon, aantal_dozen, frequentie, startweek, notitie) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(array($naam, $straat, $huisnr, $tel, $dozen, $freq, $start, $notitie));
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
        $notitie = trim(isset($data['notitie'])   ? $data['notitie']   : '');

        if (!$id || !$naam || !$straat || !$huisnr || $dozen < 1) {
            echo json_encode(array('error' => 'Vul alle verplichte velden in'));
            exit;
        }
        $stmt = $db->prepare("UPDATE klanten SET naam=?, straat=?, huisnummer=?, telefoon=?, aantal_dozen=?, frequentie=?, startweek=?, notitie=? WHERE id=?");
        $stmt->execute(array($naam, $straat, $huisnr, $tel, $dozen, $freq, $start, $notitie, $id));
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

    if ($action === 'register') {
        $naam    = trim($data['naam'] ?? '');
        $straat  = trim($data['straat'] ?? '');
        $huisnr  = trim($data['huisnummer'] ?? '');
        $tel     = trim($data['telefoon'] ?? '');
        $email   = trim($data['email'] ?? '');
        $dozen   = (int)($data['aantal_dozen'] ?? 1);
        $freq    = $data['frequentie'] ?? 'wekelijks';
        
        // Validatie
        if (!$naam || !$straat || !$huisnr || !$tel || $dozen < 1) {
            echo json_encode(array('error' => 'Vul alle verplichte velden in'));
            exit;
        }
        
        // Check of straat geldig is
        $stmtStraat = $db->prepare("SELECT id FROM straten WHERE naam = ? AND actief = 1");
        $stmtStraat->execute(array($straat));
        if (!$stmtStraat->fetch()) {
            echo json_encode(array('error' => 'Deze straat is helaas niet in ons leveringsgebied'));
            exit;
        }
        
        // Registratie opslaan
        $stmt = $db->prepare("INSERT INTO registraties (naam, straat, huisnummer, telefoon, email, aantal_dozen, frequentie, startweek, status) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute(array($naam, $straat, $huisnr, $tel, $email, $dozen, $freq, (int)date('W')));
        $registratieId = $db->lastInsertId();
        
        // WhatsApp notification naar Julian
        $bericht = "🔔 Nieuwe inschrijving!\n\n" .
                   "Naam: $naam\n" .
                   "Straat: $straat $huisnr\n" .
                   "Telefoon: $tel\n" .
                   "Dozen: $dozen\n" .
                   "Frequentie: $freq\n\n" .
                   "Inloggen om goed te keuren →\n" .
                   "https://eitjes.kunkeler.net/public/index.php";
        
        sendWhatsAppNotification($bericht);
        
        echo json_encode(array('ok' => true, 'id' => $registratieId));
        exit;
    }
}

echo json_encode(array('error' => 'Onbekende actie'));

function sendWhatsAppNotification($bericht) {
    if (!TWILIO_ACCOUNT_SID || !TWILIO_AUTH_TOKEN || !TWILIO_WHATSAPP_FROM) {
        return false; // Twilio niet geconfigureerd
    }
    
    $url = 'https://api.twilio.com/2010-04-01/Accounts/' . TWILIO_ACCOUNT_SID . '/Messages.json';
    
    $postData = array(
        'From' => 'whatsapp:' . TWILIO_WHATSAPP_FROM,
        'To' => 'whatsapp:' . JULIAN_WHATSAPP,
        'Body' => $bericht
    );
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_USERPWD, TWILIO_ACCOUNT_SID . ':' . TWILIO_AUTH_TOKEN);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response ? true : false;
}

function isAanDeBeurt($frequentie, $startweek, $weeknummer) {
    if ($frequentie === 'eenmalig')   return false;
    if ($frequentie === 'wekelijks')  return true;
    if ($frequentie === '2wekelijks') return (($weeknummer - $startweek) % 2) === 0;
    if ($frequentie === '3wekelijks') return (($weeknummer - $startweek) % 3) === 0;
    return false;
}
?>
