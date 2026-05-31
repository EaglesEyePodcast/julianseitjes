<?php
// ============================================
// config.example.php - kopieer naar config.php op de server
// ============================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'julian_eieren');
define('DB_USER', 'wouter');
define('DB_PASS', 'Julian1704!');
define('DB_CHARSET', 'utf8mb4');

define('APP_WACHTWOORD', 'julian2025');    // <-- aanpassen, login wachtwoord voor de app

define('PRIJS_VERKOOP', 3.30);
define('PRIJS_INKOOP', 2.30);

// Email notificatie configuratie
define('JULIAN_EMAIL', 'julian@example.com'); // Vul in: echt emailadres van Julian
define('EMAIL_FROM', 'no-reply@eitjes.kunkeler.net');
define('EMAIL_SUBJECT_PREFIX', '[Julian\'s Eitjes] ');

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ));
        } catch (PDOException $e) {
            die(json_encode(array('error' => 'Database verbinding mislukt')));
        }
    }
    return $pdo;
}
?>