#!/bin/bash

# Server setup script voor Julian's Eitjes
# Voer dit uit op de server met: bash server-setup.sh

set -e

echo "=== Julian's Eitjes - Server Setup ==="
echo ""

# Variabelen
REPO_URL="https://github.com/EaglesEyePodcast/julianseitjes.git"
WEB_ROOT="/var/www/vhosts/hakze.com/html/julianseitjes"
DB_NAME="julian_eieren"
DB_USER="wouter"
DB_PASS="Julian1704!"
DB_HOST="localhost"
APP_PASSWORD="julian2025"

# Stap 1: Map aanmaken
echo "[1/4] Directories setup..."
mkdir -p "$WEB_ROOT"
mkdir -p "$WEB_ROOT/config"
cd "$WEB_ROOT"

# Stap 2: Code van GitHub halen
echo "[2/4] Cloning repository..."
git clone "$REPO_URL" . 2>/dev/null || (git fetch && git reset --hard origin/main)

# Stap 3: Database aanmaken en importeren
echo "[3/4] Setting up database..."
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" << EOF
DROP DATABASE IF EXISTS $DB_NAME;
CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE $DB_NAME;
EOF

mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < database/julian_eieren.sql

echo "Database imported successfully"

# Stap 4: Config-bestand aanmaken
echo "[4/4] Creating config file..."
cat > config/config.php << 'CONFIGEOF'
<?php
// ============================================
// config.php - ALLEEN OP SERVER, NIET PUBLIEK
// ============================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'julian_eieren');
define('DB_USER', 'wouter');
define('DB_PASS', 'Julian1704!');
define('DB_CHARSET', 'utf8mb4');

define('APP_WACHTWOORD', 'julian2025');    // <-- aanpassen, login wachtwoord voor de app

define('PRIJS_VERKOOP', 3.30);
define('PRIJS_INKOOP', 2.30);

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
CONFIGEOF

# Permissions instellen
chmod 644 config/config.php
chmod 755 public
chmod 755 database

echo ""
echo "=== Setup voltooid! ==="
echo "Website beschikbaar op: eitjes.kunkeler.net"
echo "Login wachtwoord: julian2025"
echo ""
echo "Check: http://eitjes.kunkeler.net/public/index.php"
