<?php
// Geen sessie nodig voor registratie
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Inschrijven - Julian's Verse Eitjes</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f5f0; min-height: 100vh; padding: 1rem; }

.container { max-width: 500px; margin: 2rem auto; background: #fff; border-radius: 16px; padding: 2rem; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
.header { text-align: center; margin-bottom: 2rem; }
.header img { width: 100px; margin-bottom: 1rem; }
.header h1 { font-size: 22px; font-weight: 700; color: #1a1a1a; margin-bottom: 0.5rem; }
.header p { color: #666; font-size: 14px; }

.form-row { margin-bottom: 1.25rem; }
.form-row label { display: block; font-size: 12px; font-weight: 600; color: #555; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
.form-row input, .form-row select { width: 100%; padding: 11px 13px; border: 1.5px solid #e8e8e8; border-radius: 9px; font-size: 15px; color: #1a1a1a; background: #fff; outline: none; }
.form-row input:focus, .form-row select:focus { border-color: #F5C200; }
.form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.form-row-2 .form-row { margin-bottom: 0; }

.submit-btn { width: 100%; padding: 13px; background: #F5C200; color: #1a1a1a; border: none; border-radius: 10px; font-size: 16px; font-weight: 700; cursor: pointer; margin-top: 1rem; }
.submit-btn:active { transform: scale(0.98); }

.error { color: #c0392b; font-size: 13px; padding: 12px; background: #fdecea; border-radius: 8px; margin-bottom: 1rem; border-left: 3px solid #c0392b; }
.success { color: #1b5e20; font-size: 13px; padding: 12px; background: #e8f5e9; border-radius: 8px; margin-bottom: 1rem; border-left: 3px solid #1b5e20; }

.info { font-size: 12px; color: #666; background: #f9f9f9; padding: 12px; border-radius: 8px; margin-top: 1.5rem; }

.required { color: #c0392b; }
</style>
</head>
<body>

<div class="container">
  <div class="header">
    <img src="index/logo.png" alt="Julian's Verse Eitjes">
    <h1>Inschrijven</h1>
    <p>Ontvang wekelijks vers gekweekte eieren</p>
  </div>

  <div id="message"></div>

  <form id="registerForm">
    <div class="form-row">
      <label>Voornaam <span class="required">*</span></label>
      <input type="text" name="naam" placeholder="Jan" required>
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

  <div class="info">
    <strong>Hoe werkt het?</strong><br>
    1. Vul het formulier in<br>
    2. We nemen contact met u op<br>
    3. Uw eerste levering volgt snel<br>
    <br>
    Vragen? Bel: 06-19528377
  </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', async function(e) {
  e.preventDefault();

  const naam = document.querySelector('input[name="naam"]').value.trim();
  const straat = document.querySelector('select[name="straat"]').value;
  const huisnummer = document.querySelector('input[name="huisnummer"]').value.trim();
  const telefoon = document.querySelector('input[name="telefoon"]').value.trim();
  const email = document.querySelector('input[name="email"]').value.trim();
  const aantal_dozen = document.querySelector('input[name="aantal_dozen"]').value;
  const frequentie = document.querySelector('select[name="frequentie"]').value;

  if (!naam || !straat || !huisnummer || !telefoon) {
    showMessage('Vul alstublieft alle verplichte velden in', 'error');
    return;
  }

  try {
    const response = await fetch('../api.php?action=register', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        naam, straat, huisnummer, telefoon, email, aantal_dozen, frequentie
      })
    });

    const result = await response.json();

    if (result.ok) {
      showMessage('Bedankt! We nemen snel contact met u op.', 'success');
      document.getElementById('registerForm').reset();
      setTimeout(() => {
        window.location.href = 'index/index.php';
      }, 2000);
    } else {
      showMessage(result.error || 'Er ging iets mis', 'error');
    }
  } catch (err) {
    showMessage('Verbindingsfout', 'error');
  }
});

function showMessage(msg, type) {
  const el = document.getElementById('message');
  el.className = type;
  el.textContent = msg;
  el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
</script>

</body>
</html>
