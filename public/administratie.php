<?php
session_start();
require_once dirname(__DIR__) . '/config/config.php';

// Uitloggen
if (isset($_GET['uitloggen'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['julian_in'])) {
    header('Location: index.php');
    exit;
}

$ingelogd = true;
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Julian's Verse Eitjes</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; -webkit-tap-highlight-color: transparent; }
body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f5f0; min-height: 100vh; }

/* ---- PUBLIEKE INSCHRIJVING ---- */
.public-header { width: 100%; padding: 14px 16px; display: flex; justify-content: flex-end; position: sticky; top: 0; z-index: 10; background: rgba(245,245,240,0.94); backdrop-filter: blur(8px); }
.top-login { display: flex; align-items: center; gap: 8px; }
.top-login input { width: 160px; padding: 9px 11px; border: 1.5px solid #e0e0e0; border-radius: 9px; font-size: 14px; outline: none; background: #fff; }
.top-login input:focus { border-color: #F5C200; }
.top-login button { padding: 9px 13px; background: #1a1a1a; color: #F5C200; border: none; border-radius: 9px; font-size: 13px; font-weight: 700; cursor: pointer; }
.top-login .login-fout { color: #c0392b; font-size: 12px; font-weight: 600; white-space: nowrap; }
.register-wrap { padding: 1rem 1rem 2.5rem; }
.register-card { max-width: 500px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 2rem; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
.register-head { text-align: center; margin-bottom: 2rem; }
.register-head img { width: 100px; margin-bottom: 1rem; }
.register-head h1 { font-size: 22px; font-weight: 700; color: #1a1a1a; margin-bottom: 0.5rem; }
.register-head p { color: #666; font-size: 14px; }
.register-info { font-size: 12px; color: #666; background: #f9f9f9; padding: 12px; border-radius: 8px; margin-top: 1.5rem; line-height: 1.45; }
.required { color: #c0392b; }
.message { font-size: 13px; padding: 12px; border-radius: 8px; margin-bottom: 1rem; border-left: 3px solid transparent; }
.message.error { color: #c0392b; background: #fdecea; border-left-color: #c0392b; }
.message.success { color: #1b5e20; background: #e8f5e9; border-left-color: #1b5e20; }

@media (max-width: 560px) {
    .public-header { position: static; justify-content: stretch; padding: 12px; }
    .top-login { width: 100%; }
    .top-login input { flex: 1; min-width: 0; width: auto; }
    .top-login .login-fout { position: absolute; top: 48px; right: 14px; }
    .register-wrap { padding-top: 0.5rem; }
    .register-card { padding: 1.25rem; }
    .form-row-2 { grid-template-columns: 1fr; }
}

/* ---- APP SHELL ---- */
.app-header { background: #F5C200; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100; }
.app-header-left { display: flex; align-items: center; gap: 10px; }
.app-header img { width: 38px; height: 38px; object-fit: contain; }
.app-header-title { font-size: 16px; font-weight: 700; color: #1a1a1a; line-height: 1.2; }
.app-header-sub { font-size: 11px; color: #5a4a00; }
.uitlog-btn { font-size: 11px; color: #5a4a00; background: rgba(0,0,0,0.1); border: none; border-radius: 6px; padding: 5px 10px; cursor: pointer; }

.main { padding: 1rem; max-width: 540px; margin: 0 auto; padding-bottom: 80px; }

/* ---- TAB NAV ---- */
.tab-nav { display: flex; gap: 6px; margin-bottom: 1.25rem; }
.tab-btn { flex: 1; padding: 10px 6px; background: #fff; border: 1.5px solid #e8e8e8; border-radius: 10px; font-size: 13px; font-weight: 500; color: #888; cursor: pointer; text-align: center; }
.tab-btn.active { background: #1a1a1a; color: #F5C200; border-color: #1a1a1a; }

/* ---- WEEK NAV ---- */
.week-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
.week-nav-title { text-align: center; }
.week-nav-title h2 { font-size: 18px; font-weight: 700; color: #1a1a1a; }
.week-nav-title span { font-size: 12px; color: #888; }
.nav-btn { width: 36px; height: 36px; background: #fff; border: 1.5px solid #e8e8e8; border-radius: 8px; font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #1a1a1a; }

/* ---- STATS ---- */
.stats-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; margin-bottom: 1.25rem; }
.stat-card { background: #fff; border-radius: 10px; padding: 10px 8px; text-align: center; border: 1.5px solid #f0f0f0; }
.stat-label { font-size: 10px; color: #888; margin-bottom: 3px; text-transform: uppercase; letter-spacing: 0.3px; }
.stat-val { font-size: 22px; font-weight: 700; color: #1a1a1a; }
.stat-val.geld { color: #2d7a3a; }
.stat-val.rood { color: #c0392b; }
.voorraad-card { background: #fff; border-radius: 12px; padding: 14px; margin-bottom: 1.25rem; border: 1.5px solid #f0f0f0; display: grid; grid-template-columns: 1fr auto; gap: 10px; align-items: end; }
.voorraad-card label { display: block; font-size: 12px; font-weight: 700; color: #555; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.3px; }
.voorraad-card input { width: 100%; padding: 11px 13px; border: 1.5px solid #e8e8e8; border-radius: 9px; font-size: 15px; color: #1a1a1a; background: #fff; outline: none; }
.voorraad-card input:focus { border-color: #F5C200; }
.voorraad-card button { min-height: 42px; padding: 0 16px; background: #1a1a1a; color: #F5C200; border: none; border-radius: 9px; font-size: 13px; font-weight: 700; cursor: pointer; }
.voorraad-help { grid-column: 1 / -1; font-size: 12px; color: #777; line-height: 1.35; }

/* ---- KLANT KAARTJE ---- */
.klant-card { background: #fff; border-radius: 12px; padding: 14px; margin-bottom: 10px; border: 1.5px solid #f0f0f0; }
.klant-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; }
.klant-naam { font-size: 15px; font-weight: 600; color: #1a1a1a; }
.klant-adres { font-size: 12px; color: #888; margin-top: 1px; }
.status-badge { font-size: 11px; padding: 3px 9px; border-radius: 20px; font-weight: 600; white-space: nowrap; }
.status-open   { background: #fdecea; color: #b71c1c; }
.status-tikkie { background: #fff8e1; color: #795b00; }
.status-betaald { background: #e8f5e9; color: #1b5e20; }
.klant-bottom { display: flex; justify-content: space-between; align-items: center; }
.klant-info { font-size: 13px; color: #555; }
.klant-btns { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 10px; }
.actie-btn { padding: 7px 13px; border-radius: 8px; font-size: 12px; font-weight: 600; border: 1.5px solid #e8e8e8; background: #fff; cursor: pointer; color: #1a1a1a; }
.actie-btn.aan  { background: #1a1a1a; border-color: #1a1a1a; color: #F5C200; }
.actie-btn.geel { background: #F5C200; border-color: #F5C200; color: #1a1a1a; }
.actie-btn.groen { background: #2d7a3a; border-color: #2d7a3a; color: #fff; }
.actie-btn.grijs { background: #f0f0f0; border-color: #e0e0e0; color: #888; font-size: 11px; }
.actie-btn:active { transform: scale(0.97); }
.toggle-rij { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 8px; border-top: 1px solid #f0f0f0; padding-top: 8px; }
.toggle-btn { padding: 8px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; border: 1.5px solid #e0e0e0; background: #f5f5f5; cursor: pointer; color: #999; }
.toggle-btn:active { transform: scale(0.97); }
.t-aan { background: #2d7a3a !important; border-color: #2d7a3a !important; color: #fff !important; }

/* ---- KLANT LIJST ---- */
.freq-tag { display: inline-block; font-size: 10px; padding: 2px 7px; border-radius: 20px; font-weight: 600; margin-top: 3px; }
.freq-w  { background: #e3f0ff; color: #1a4b8c; }
.freq-2w { background: #e8f5e9; color: #1b5e20; }
.freq-3w { background: #fff8e1; color: #795b00; }
.route-tag { display: inline-flex; align-items: center; justify-content: center; min-width: 26px; height: 24px; padding: 0 7px; border-radius: 8px; background: #1a1a1a; color: #F5C200; font-size: 11px; font-weight: 800; margin-right: 7px; vertical-align: 1px; }
.route-help { font-size: 12px; color: #777; margin: -2px 0 12px; line-height: 1.35; }

/* ---- INLINE BEWERKEN ---- */
.bewerk-formulier { display: none; margin-top: 12px; border-top: 1.5px solid #f0f0f0; padding-top: 12px; }
.bewerk-formulier.open { display: block; }
.bewerk-formulier input, .bewerk-formulier select {
    width: 100%; padding: 9px 11px; border: 1.5px solid #e8e8e8; border-radius: 8px;
    font-size: 14px; color: #1a1a1a; background: #fff; margin-bottom: 8px; outline: none;
}
.bewerk-formulier input:focus, .bewerk-formulier select:focus { border-color: #F5C200; }
.bewerk-rij-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.bewerk-acties { display: flex; gap: 6px; margin-top: 4px; }
.bewerk-acties .opslaan { flex: 1; padding: 10px; background: #F5C200; border: none; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; }
.bewerk-acties .uitschrijven { padding: 10px 14px; background: #fdecea; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; color: #b71c1c; cursor: pointer; }
.bewerk-lbl { display: block; font-size: 11px; font-weight: 600; color: #888; margin-bottom: 3px; text-transform: uppercase; letter-spacing: 0.3px; }

/* ---- FORMULIER ---- */
.form-card { background: #fff; border-radius: 12px; padding: 1.25rem; border: 1.5px solid #f0f0f0; }
.form-card h3 { font-size: 16px; font-weight: 700; color: #1a1a1a; margin-bottom: 1rem; }
.form-row { margin-bottom: 14px; }
.form-row label { display: block; font-size: 12px; font-weight: 600; color: #555; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.3px; }
.form-row input, .form-row select, .form-row textarea {
    width: 100%; padding: 11px 13px; border: 1.5px solid #e8e8e8; border-radius: 9px;
    font-size: 15px; color: #1a1a1a; background: #fff; outline: none;
}
.form-row input:focus, .form-row select:focus { border-color: #F5C200; }
.form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.submit-btn { width: 100%; padding: 14px; background: #F5C200; color: #1a1a1a; border: none; border-radius: 10px; font-size: 16px; font-weight: 700; cursor: pointer; margin-top: 6px; }
.submit-btn:active { transform: scale(0.98); }

/* ---- LEGE STAAT ---- */
.leeg { text-align: center; padding: 2.5rem 1rem; color: #aaa; font-size: 14px; }

/* ---- TOAST ---- */
.toast { position: fixed; bottom: 1.5rem; left: 50%; transform: translateX(-50%); background: #1a1a1a; color: #fff; padding: 10px 22px; border-radius: 10px; font-size: 14px; font-weight: 500; opacity: 0; transition: opacity 0.25s; pointer-events: none; z-index: 999; white-space: nowrap; }
.toast.zichtbaar { opacity: 1; }

/* ---- SECTIE HEADER ---- */
.straat-header { font-size: 11px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin: 1rem 0 6px; padding-left: 2px; }
@media (max-width: 420px) {
    .voorraad-card { grid-template-columns: 1fr; }
    .voorraad-card button { width: 100%; }
}
</style>
</head>
<body>

<?php if (!$ingelogd): ?>
<header class="public-header">
  <form class="top-login" method="post">
    <?php if (isset($login_fout)): ?>
      <span class="login-fout">Onjuist wachtwoord</span>
    <?php endif; ?>
    <input type="password" name="wachtwoord" placeholder="Wachtwoord" aria-label="Wachtwoord">
    <button type="submit">Login</button>
  </form>
</header>

<div class="register-wrap">
  <div class="register-card">
    <div class="register-head">
      <img src="logo.png" alt="Julian's Verse Eitjes">
      <h1>Inschrijven</h1>
      <p>Ontvang wekelijks vers gekweekte eieren</p>
    </div>

    <div id="message" class="message" style="display:none;"></div>

    <form id="registerForm">
      <div class="form-row-2">
        <div class="form-row">
          <label>Voornaam <span class="required">*</span></label>
          <input type="text" name="voornaam" placeholder="Jan" required>
        </div>
        <div class="form-row">
          <label>Achternaam <span class="required">*</span></label>
          <input type="text" name="achternaam" placeholder="Jansen" required>
        </div>
      </div>

      <div class="form-row-2">
        <div class="form-row">
          <label>Straat <span class="required">*</span></label>
          <select name="straat" required>
            <option value="">-- Kies uw straat --</option>
            <option value="1e Weerdsweg">1e Weerdsweg</option>
            <option value="2e Weerdsweg">2e Weerdsweg</option>
            <option value="Anna Reynvaanstraat">Anna Reynvaanstraat</option>
            <option value="Borgerlerstraat">Borgerlerstraat</option>
            <option value="Hallensstraat">Hallensstraat</option>
            <option value="Jacob van Bredastraat">Jacob van Bredastraat</option>
            <option value="J.P. Sweelinckstraat">J.P. Sweelinckstraat</option>
            <option value="Johannes Sinthenstraat">Johannes Sinthenstraat</option>
            <option value="Kromme Kerkstraat">Kromme Kerkstraat</option>
            <option value="Radstakeweg">Radstakeweg</option>
            <option value="Reinckenstraat">Reinckenstraat</option>
            <option value="Richard Paffraedstraat">Richard Paffraedstraat</option>
            <option value="Sint Jurrienstraat">Sint Jurrienstraat</option>
            <option value="Zwolseweg">Zwolseweg</option>
          </select>
        </div>
        <div class="form-row">
          <label>Huisnummer <span class="required">*</span></label>
          <input type="text" name="huisnummer" placeholder="12" required>
        </div>
      </div>

      <div class="form-row">
        <label>Telefoonnummer <span class="required">*</span></label>
        <input type="tel" name="telefoon" placeholder="06-12345678" required>
      </div>

      <div class="form-row">
        <label>Email</label>
        <input type="email" name="email" placeholder="uw@email.nl">
      </div>

      <div class="form-row-2">
        <div class="form-row">
          <label>Aantal dozen <span class="required">*</span></label>
          <input type="number" name="aantal_dozen" min="1" max="10" value="1" required>
        </div>
        <div class="form-row">
          <label>Frequentie <span class="required">*</span></label>
          <select name="frequentie" required>
            <option value="wekelijks">Elke week</option>
            <option value="2wekelijks">Elke 2 weken</option>
            <option value="3wekelijks">Elke 3 weken</option>
          </select>
        </div>
      </div>

      <button type="submit" class="submit-btn">Inschrijven</button>
    </form>

    <div class="register-info">
      <strong>Hoe werkt het?</strong><br>
      1. Vul het formulier in<br>
      2. We nemen contact met u op<br>
      3. Uw eerste levering volgt snel<br>
      <br>
      Vragen? Bel: 06-19528377
    </div>
  </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    var voornaam = document.querySelector('input[name="voornaam"]').value.trim();
    var achternaam = document.querySelector('input[name="achternaam"]').value.trim();
    var straat = document.querySelector('select[name="straat"]').value;
    var huisnummer = document.querySelector('input[name="huisnummer"]').value.trim();
    var telefoon = document.querySelector('input[name="telefoon"]').value.trim();
    var email = document.querySelector('input[name="email"]').value.trim();
    var aantal_dozen = document.querySelector('input[name="aantal_dozen"]').value;
    var frequentie = document.querySelector('select[name="frequentie"]').value;

    if (!voornaam || !achternaam || !straat || !huisnummer || !telefoon) {
        showMessage('Vul alstublieft alle verplichte velden in', 'error');
        return;
    }

    try {
        var response = await fetch('api.php?action=register', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                voornaam: voornaam,
                achternaam: achternaam,
                straat: straat,
                huisnummer: huisnummer,
                telefoon: telefoon,
                email: email,
                aantal_dozen: aantal_dozen,
                frequentie: frequentie
            })
        });
        var result = await response.json();

        if (result.ok) {
            showMessage('Bedankt! We nemen snel contact met u op.', 'success');
            document.getElementById('registerForm').reset();
        } else {
            showMessage(result.error || 'Er ging iets mis', 'error');
        }
    } catch (err) {
        showMessage('Verbindingsfout', 'error');
    }
});

function showMessage(msg, type) {
    var el = document.getElementById('message');
    el.className = 'message ' + type;
    el.style.display = 'block';
    el.textContent = msg;
    el.scrollIntoView({behavior: 'smooth', block: 'nearest'});
}
</script>

<?php else: ?>

<div class="app-header">
  <div class="app-header-left">
    <img src="logo.png" alt="Logo">
    <div>
      <div class="app-header-title">Julian's Verse Eitjes</div>
      <div class="app-header-sub">Eieren administratie</div>
    </div>
  </div>
  <a href="?uitloggen=1"><button class="uitlog-btn">Uitloggen</button></a>
</div>

<div class="main">
  <div class="tab-nav">
    <button class="tab-btn active" onclick="toonTab('week')">Deze week</button>
    <button class="tab-btn" onclick="toonTab('klanten')">Klanten</button>
    <button class="tab-btn" onclick="toonTab('nieuw')">+ Nieuw</button>
  </div>

  <!-- WEEK TAB -->
  <div id="tab-week">
    <div class="week-nav">
      <button class="nav-btn" onclick="wisselWeek(-1)">&#8249;</button>
      <div class="week-nav-title">
        <h2 id="week-titel">Week <span id="week-nr"></span></h2>
        <span id="week-sub">laden...</span>
      </div>
      <button class="nav-btn" onclick="wisselWeek(1)">&#8250;</button>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">Klanten</div>
        <div class="stat-val" id="s-klanten">-</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Tikkie open</div>
        <div class="stat-val rood" id="s-open">-</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Winst</div>
        <div class="stat-val geld" id="s-winst">-</div>
      </div>
    </div>

    <div class="stats-grid" style="margin-bottom: 1.5rem;">
      <div class="stat-card">
        <div class="stat-label">Dozen totaal</div>
        <div class="stat-val" id="s-dozen">-</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Trays nodig</div>
        <div class="stat-val" id="s-trays">-</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Omzet</div>
        <div class="stat-val geld" id="s-omzet">-</div>
      </div>
    </div>

    <div class="voorraad-card">
      <div>
        <label for="overgebleven-dozen">Over van vorige week</label>
        <input type="number" id="overgebleven-dozen" min="0" max="999" value="0">
      </div>
      <button type="button" onclick="voorraadOpslaan()">Opslaan</button>
      <div class="voorraad-help">
        Bestelling: <span id="s-bestel-uitleg">laden...</span>
      </div>
    </div>

    <div id="week-lijst"></div>
  </div>

  <!-- KLANTEN TAB -->
  <div id="tab-klanten" style="display:none;">
    <div id="klant-lijst"></div>
  </div>

  <!-- NIEUW TAB -->
  <div id="tab-nieuw" style="display:none;">
    <div class="form-card">
      <h3>Nieuwe klant toevoegen</h3>
      <div class="form-row">
        <label>Naam *</label>
        <input type="text" id="f-naam" placeholder="Jan de Vries">
      </div>
      <div class="form-row-2">
        <div class="form-row">
          <label>Straat *</label>
          <input type="text" id="f-straat" placeholder="Zwolseweg">
        </div>
        <div class="form-row">
          <label>Huisnummer *</label>
          <input type="text" id="f-huisnr" placeholder="12">
        </div>
      </div>
      <div class="form-row">
        <label>Telefoonnummer</label>
        <input type="tel" id="f-tel" placeholder="06-12345678">
      </div>
      <div class="form-row-2">
        <div class="form-row">
          <label>Aantal dozen *</label>
          <input type="number" id="f-dozen" min="1" max="10" value="1">
        </div>
        <div class="form-row">
          <label>Startweek</label>
          <input type="number" id="f-start" min="1" max="52" value="1">
        </div>
      </div>
      <div class="form-row">
        <label>Route volgorde</label>
        <input type="number" id="f-route" min="0" max="999" value="0" placeholder="Bijv. 10">
      </div>
      <div class="form-row">
        <label>Frequentie *</label>
        <select id="f-freq">
          <option value="wekelijks">Elke week</option>
          <option value="2wekelijks">Elke 2 weken</option>
          <option value="3wekelijks">Elke 3 weken</option>
          <option value="eenmalig">Eenmalig</option>
        </select>
      </div>
      <div class="form-row">
        <label>Notitie</label>
        <textarea id="f-notitie" rows="2" placeholder="Bijv. belt altijd voor bezorging" style="resize: none;"></textarea>
      </div>
      <button class="submit-btn" onclick="klantOpslaan()">Klant opslaan</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
var huidigWeek = <?php echo (int)date('W'); ?>;
var huidigJaar = <?php echo (int)date('Y'); ?>;
var weekOffset = 0;

function getWeek() { return huidigWeek + weekOffset; }
function getJaar() { return huidigJaar; }

function toonTab(tab) {
    document.getElementById('tab-week').style.display    = 'none';
    document.getElementById('tab-klanten').style.display = 'none';
    document.getElementById('tab-nieuw').style.display   = 'none';
    document.getElementById('tab-' + tab).style.display  = 'block';
    document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
    var map = {week: 0, klanten: 1, nieuw: 2};
    document.querySelectorAll('.tab-btn')[map[tab]].classList.add('active');
    if (tab === 'week')    laadWeek();
    if (tab === 'klanten') laadKlanten();
}

function wisselWeek(dir) {
    weekOffset += dir;
    laadWeek();
}

function laadWeek() {
    var week = getWeek();
    var jaar = getJaar();
    document.getElementById('week-nr').textContent = week;
    document.getElementById('week-sub').textContent = jaar;

    fetch('api.php?action=week&week=' + week + '&jaar=' + jaar)
        .then(function(r) { return r.json(); })
        .then(function(data) { renderWeekLijst(data, week, jaar); });

    fetch('api.php?action=stats&week=' + week + '&jaar=' + jaar)
        .then(function(r) { return r.json(); })
        .then(function(s) {
            document.getElementById('s-klanten').textContent = s.totaal_dozen ? '-' : '0';
            document.getElementById('s-open').textContent    = s.open_tikkies;
            document.getElementById('s-winst').textContent   = '€' + parseFloat(s.winst).toFixed(2);
            document.getElementById('s-dozen').textContent   = s.totaal_dozen;
            document.getElementById('s-trays').textContent   = s.trays_nodig;
            document.getElementById('s-omzet').textContent   = '€' + parseFloat(s.omzet).toFixed(2);
            document.getElementById('overgebleven-dozen').value = s.overgebleven_dozen || 0;
            document.getElementById('s-bestel-uitleg').textContent =
                s.totaal_dozen + ' nodig + ' + s.reserve_dozen + ' reserve - ' +
                s.overgebleven_dozen + ' over = ' + s.te_bestellen_dozen +
                ' dozen bestellen (' + s.trays_nodig + ' trays)';
        });
}

function voorraadOpslaan() {
    var waarde = parseInt(document.getElementById('overgebleven-dozen').value, 10);
    if (isNaN(waarde) || waarde < 0) {
        toast('Vul een geldig aantal overgebleven dozen in');
        return;
    }

    api('voorraad_opslaan', {
        week: getWeek(),
        jaar: getJaar(),
        overgebleven_dozen: waarde
    }, 'Voorraad opgeslagen');
    setTimeout(laadWeek, 250);
}

function renderWeekLijst(klanten, week, jaar) {
    document.getElementById('s-klanten').textContent = klanten.length;
    if (klanten.length === 0) {
        document.getElementById('week-lijst').innerHTML = '<div class="leeg">Geen klanten deze week</div>';
        return;
    }

    var html = '';
    html += '<div class="straat-header">Looproute</div>';
    klanten.forEach(function(k, index) {
        var status = k.betaald ? 'betaald' : (k.tikkie ? 'tikkie' : 'open');
        var badgeTekst = k.betaald ? 'Betaald' : (k.tikkie ? 'Tikkie verstuurd' : 'Tikkie open');
        var bedrag = (k.aantal_dozen * 3.30).toFixed(2);
        var id = k.id;
        var route = parseInt(k.bezorg_volgorde || 0, 10);
        var routeLabel = route > 0 ? route : (index + 1);

        html += '<div class="klant-card">';
        html += '<div class="klant-top">';
        html += '<div><div class="klant-naam"><span class="route-tag">' + routeLabel + '</span>' + esc(k.naam) + '</div>';
        html += '<div class="klant-adres">' + esc(k.straat) + ' ' + esc(k.huisnummer) + '</div></div>';
        html += '<span class="status-badge status-' + status + '">' + badgeTekst + '</span>';
        html += '</div>';
        html += '<div style="font-size:13px;color:#555;margin-top:6px;">' + k.aantal_dozen + ' doos &nbsp;·&nbsp; €' + bedrag + '</div>';
        html += '<div class="toggle-rij">';

        var bezorgd  = k.bezorgd  === true;
        var tikkie   = k.tikkie   === true;
        var betaald  = k.betaald  === true;

        html += '<button id="bezorgd-' + id + '" class="toggle-btn' + (bezorgd ? ' t-aan' : '') + '" onclick="toggleBezorgd(' + id + ',' + week + ',' + jaar + ',' + (bezorgd ? 1 : 0) + ')">' + (bezorgd ? '&#10003; Bezorgd' : 'Bezorgd') + '</button>';
        html += '<button id="tikkie-'  + id + '" class="toggle-btn' + (tikkie  ? ' t-aan' : '') + '" onclick="toggleTikkie('  + id + ',' + week + ',' + jaar + ',' + (tikkie  ? 1 : 0) + ')">' + (tikkie  ? '&#10003; Tikkie'  : 'Tikkie')  + '</button>';
        html += '<button id="betaald-' + id + '" class="toggle-btn' + (betaald ? ' t-aan' : '') + '" onclick="toggleBetaald(' + id + ',' + week + ',' + jaar + ',' + (betaald ? 1 : 0) + ')">' + (betaald ? '&#10003; Betaald' : 'Betaald') + '</button>';

        html += '</div></div>';
    });

    document.getElementById('week-lijst').innerHTML = html;
}

function laadKlanten() {
    fetch('api.php?action=klanten')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.length === 0) {
                document.getElementById('klant-lijst').innerHTML = '<div class="leeg">Nog geen klanten</div>';
                return;
            }
            var html = '';
            html += '<div class="straat-header">Route volgorde</div>';
            html += '<div class="route-help">Geef klanten een route nummer. Lage nummers komen eerst in de weeklijst. Laat 0 staan voor automatische volgorde onderaan.</div>';
            data.forEach(function(k, index) {
                    var freqMap   = {wekelijks: 'Wekelijks', '2wekelijks': '2-wekelijks', '3wekelijks': '3-wekelijks', eenmalig: 'Eenmalig'};
                    var freqClass = {wekelijks: 'freq-w', '2wekelijks': 'freq-2w', '3wekelijks': 'freq-3w', eenmalig: 'freq-3w'};
                    var fOpt = function(val, label, cur) {
                        return '<option value="' + val + '"' + (cur === val ? ' selected' : '') + '>' + label + '</option>';
                    };
                    var route = parseInt(k.bezorg_volgorde || 0, 10);
                    var routeLabel = route > 0 ? route : (index + 1);

                    html += '<div class="klant-card" id="kkaart-' + k.id + '">';

                    // Vaste weergave bovenaan
                    html += '<div style="display:flex;justify-content:space-between;align-items:flex-start;">';
                    html += '<div>';
                    html += '<div class="klant-naam"><span class="route-tag">' + routeLabel + '</span>' + esc(k.naam) + '</div>';
                    html += '<div class="klant-adres">' + esc(k.straat) + ' ' + esc(k.huisnummer) + '</div>';
                    if (k.telefoon) html += '<div class="klant-adres">' + esc(k.telefoon) + '</div>';
                    html += '<div style="margin-top:4px;font-size:12px;color:#555;">' + k.aantal_dozen + ' doos · route ' + (route || 'auto') + ' · startweek ' + k.startweek + '</div>';
                    html += '</div>';
                    html += '<div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;">';
                    html += '<span class="freq-tag ' + freqClass[k.frequentie] + '">' + freqMap[k.frequentie] + '</span>';
                    html += '<button class="actie-btn grijs" style="font-size:11px;" onclick="toggleBewerk(' + k.id + ')">Bewerken</button>';
                    html += '</div></div>';

                    // Inline bewerkformulier (verborgen)
                    html += '<div class="bewerk-formulier" id="bewerk-' + k.id + '">';

                    html += '<label class="bewerk-lbl">Naam</label>';
                    html += '<input type="text" id="b-naam-' + k.id + '" value="' + esc(k.naam) + '">';

                    html += '<div class="bewerk-rij-2">';
                    html += '<div><label class="bewerk-lbl">Straat</label><input type="text" id="b-straat-' + k.id + '" value="' + esc(k.straat) + '"></div>';
                    html += '<div><label class="bewerk-lbl">Huisnr</label><input type="text" id="b-huisnr-' + k.id + '" value="' + esc(k.huisnummer) + '"></div>';
                    html += '</div>';

                    html += '<label class="bewerk-lbl">Telefoon</label>';
                    html += '<input type="tel" id="b-tel-' + k.id + '" value="' + esc(k.telefoon || '') + '">';

                    html += '<div class="bewerk-rij-2">';
                    html += '<div><label class="bewerk-lbl">Aantal dozen</label><input type="number" id="b-dozen-' + k.id + '" value="' + k.aantal_dozen + '" min="1" max="20"></div>';
                    html += '<div><label class="bewerk-lbl">Startweek</label><input type="number" id="b-start-' + k.id + '" value="' + k.startweek + '" min="1" max="52"></div>';
                    html += '</div>';

                    html += '<label class="bewerk-lbl">Route volgorde</label>';
                    html += '<input type="number" id="b-route-' + k.id + '" value="' + (k.bezorg_volgorde || 0) + '" min="0" max="999">';

                    html += '<label class="bewerk-lbl">Frequentie</label>';
                    html += '<select id="b-freq-' + k.id + '">';
                    html += fOpt('wekelijks',  'Elke week',    k.frequentie);
                    html += fOpt('2wekelijks', 'Elke 2 weken', k.frequentie);
                    html += fOpt('3wekelijks', 'Elke 3 weken', k.frequentie);
                    html += fOpt('eenmalig',   'Eenmalig',     k.frequentie);
                    html += '</select>';

                    html += '<div class="bewerk-acties">';
                    html += '<button class="opslaan" onclick="klantUpdaten(' + k.id + ')">Opslaan</button>';
                    html += '<button class="uitschrijven" onclick="deactiveerKlant(' + k.id + ', \'' + esc(k.naam) + '\')">Uitschrijven</button>';
                    html += '</div>';

                    html += '</div>'; // einde bewerk-formulier
                    html += '</div>'; // einde klant-card
            });
            document.getElementById('klant-lijst').innerHTML = html;
        });
}

function toggleBewerk(id) {
    var el = document.getElementById('bewerk-' + id);
    el.classList.toggle('open');
}

function klantUpdaten(id) {
    var data = {
        id:           id,
        naam:         document.getElementById('b-naam-'  + id).value.trim(),
        straat:       document.getElementById('b-straat-' + id).value.trim(),
        huisnummer:   document.getElementById('b-huisnr-' + id).value.trim(),
        telefoon:     document.getElementById('b-tel-'   + id).value.trim(),
        aantal_dozen: document.getElementById('b-dozen-' + id).value,
        frequentie:   document.getElementById('b-freq-'  + id).value,
        startweek:    document.getElementById('b-start-' + id).value,
        bezorg_volgorde: document.getElementById('b-route-' + id).value
    };
    if (!data.naam || !data.straat || !data.huisnummer) {
        toast('Vul naam, straat en huisnummer in');
        return;
    }
    fetch('api.php?action=klant_updaten', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    }).then(function(r) { return r.json(); })
      .then(function(res) {
          if (res.ok) {
              toast('Klant opgeslagen');
              laadKlanten();
          } else {
              toast(res.error || 'Er ging iets mis');
          }
      });
}

function toggleBezorgd(id, week, jaar, huidig) {
    var nieuw = huidig ? 0 : 1;
    apiToggle('toggle_bezorgd', {klant_id: id, week: week, jaar: jaar, waarde: nieuw}, 'bezorgd-' + id, nieuw, nieuw ? '&#10003; Bezorgd' : 'Bezorgd');
}
function toggleTikkie(id, week, jaar, huidig) {
    var nieuw = huidig ? 0 : 1;
    apiToggle('toggle_tikkie', {klant_id: id, week: week, jaar: jaar, waarde: nieuw}, 'tikkie-' + id, nieuw, nieuw ? '&#10003; Tikkie' : 'Tikkie');
}
function toggleBetaald(id, week, jaar, huidig) {
    var nieuw = huidig ? 0 : 1;
    apiToggle('toggle_betaald', {klant_id: id, week: week, jaar: jaar, waarde: nieuw}, 'betaald-' + id, nieuw, nieuw ? '&#10003; Betaald' : 'Betaald');
}
function apiToggle(action, data, btnId, nieuw, tekst) {
    fetch('api.php?action=' + action, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    }).then(function(r) { return r.json(); })
      .then(function(res) {
          if (res.ok) {
              var btn = document.getElementById(btnId);
              if (btn) {
                  if (nieuw) {
                      btn.classList.add('t-aan');
                  } else {
                      btn.classList.remove('t-aan');
                  }
                  btn.innerHTML = tekst;
                  var nieuweHuidig = nieuw ? 1 : 0;
                  var onclick = btn.getAttribute('onclick');
                  btn.setAttribute('onclick', onclick.replace(/,\s*[01]\s*\)$/, ',' + nieuweHuidig + ')'));
              }
          } else {
              toast(res.error || 'Er ging iets mis');
          }
      });
}
function deactiveerKlant(id, naam) {
    if (!confirm(naam + ' uitschrijven?')) return;
    api('klant_deactiveren', {id: id}, naam + ' uitgeschreven');
    setTimeout(laadKlanten, 400);
}

function klantOpslaan() {
    var data = {
        naam:         document.getElementById('f-naam').value.trim(),
        straat:       document.getElementById('f-straat').value.trim(),
        huisnummer:   document.getElementById('f-huisnr').value.trim(),
        telefoon:     document.getElementById('f-tel').value.trim(),
        aantal_dozen: document.getElementById('f-dozen').value,
        frequentie:   document.getElementById('f-freq').value,
        startweek:    document.getElementById('f-start').value,
        bezorg_volgorde: document.getElementById('f-route').value,
        notitie:      document.getElementById('f-notitie').value.trim()
    };
    if (!data.naam || !data.straat || !data.huisnummer) {
        toast('Vul naam, straat en huisnummer in');
        return;
    }
    api('klant_opslaan', data, 'Klant opgeslagen!');
    document.getElementById('f-naam').value    = '';
    document.getElementById('f-straat').value  = '';
    document.getElementById('f-huisnr').value  = '';
    document.getElementById('f-tel').value     = '';
    document.getElementById('f-dozen').value   = '1';
    document.getElementById('f-route').value   = '0';
    document.getElementById('f-notitie').value = '';
    setTimeout(function() { toonTab('klanten'); }, 600);
}

function api(action, data, bericht) {
    fetch('api.php?action=' + action, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    }).then(function(r) { return r.json(); })
      .then(function(res) {
          if (res.ok) {
              toast(bericht);
              if (document.getElementById('tab-week').style.display !== 'none') laadWeek();
          } else {
              toast(res.error || 'Er ging iets mis');
          }
      });
}

function toast(msg) {
    var t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('zichtbaar');
    setTimeout(function() { t.classList.remove('zichtbaar'); }, 2200);
}

function esc(s) {
    return (s || '').toString()
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

laadWeek();
</script>

<?php endif; ?>
</body>
</html>
