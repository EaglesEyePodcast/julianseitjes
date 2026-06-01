<?php
session_start();
require_once dirname(__DIR__) . '/config/config.php';

// Login verwerken
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wachtwoord'])) {
    if ($_POST['wachtwoord'] === APP_WACHTWOORD) {
        $_SESSION['julian_in'] = true;
        header('Location: administratie.php');
        exit;
    } else {
        $login_fout = true;
    }
}

if (isset($_GET['uitloggen'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

$administratie_ingelogd = isset($_SESSION['julian_in']);
$ingelogd = false;
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Julian's Verse Eitjes</title>
<meta name="description" content="Verse vrije-uitloopeieren aan de deur in de Zwolse Wijk. Julian bezorgt iedere week verse eitjes rechtstreeks van de boer.">
<link rel="icon" type="image/png" href="logo.png">
<link rel="apple-touch-icon" href="logo.png">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Julian's Verse Eitjes">
<meta property="og:title" content="Julian's Verse Eitjes">
<meta property="og:description" content="Verse vrije-uitloopeieren aan de deur in de Zwolse Wijk.">
<meta property="og:url" content="https://eitjes.kunkeler.net/">
<meta property="og:image" content="https://eitjes.kunkeler.net/logo.png">
<meta property="og:image:secure_url" content="https://eitjes.kunkeler.net/logo.png">
<meta property="og:image:type" content="image/png">
<meta property="og:image:width" content="1024">
<meta property="og:image:height" content="1024">
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="Julian's Verse Eitjes">
<meta name="twitter:description" content="Verse vrije-uitloopeieren aan de deur in de Zwolse Wijk.">
<meta name="twitter:image" content="https://eitjes.kunkeler.net/logo.png">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; -webkit-tap-highlight-color: transparent; }
html { scroll-behavior: smooth; scroll-padding-top: 90px; }
body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #fffaf0; min-height: 100vh; color: #17120d; }

/* ---- PUBLIEKE INSCHRIJVING ---- */
.public-page { overflow: hidden; }
.public-header { width: 100%; padding: 16px max(18px, calc((100vw - 1180px) / 2)); display: flex; justify-content: space-between; align-items: center; gap: 18px; position: sticky; top: 0; z-index: 10; background: rgba(255,250,240,0.88); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(26,18,10,0.08); }
.brand { display: inline-flex; align-items: center; gap: 10px; text-decoration: none; color: #17120d; font-weight: 900; letter-spacing: -0.02em; }
.brand img { width: 46px; height: 46px; object-fit: contain; filter: drop-shadow(0 6px 12px rgba(0,0,0,0.12)); }
.brand span { display: block; line-height: 1.05; }
.site-nav { display: flex; align-items: center; justify-content: center; gap: 8px; flex: 1; }
.site-nav a { padding: 9px 12px; border-radius: 999px; color: #332b24; font-size: 14px; font-weight: 850; text-decoration: none; transition: background 0.2s ease, color 0.2s ease; white-space: nowrap; }
.site-nav a:hover { background: #17120d; color: #F5C200; }
.top-login { display: flex; align-items: center; gap: 8px; }
.top-login input { width: 160px; padding: 9px 11px; border: 1.5px solid #e0e0e0; border-radius: 9px; font-size: 14px; outline: none; background: #fff; }
.top-login input:focus { border-color: #F5C200; }
.top-login button { padding: 9px 13px; background: #1a1a1a; color: #F5C200; border: none; border-radius: 9px; font-size: 13px; font-weight: 700; cursor: pointer; }
.top-link { display: inline-flex; align-items: center; justify-content: center; min-height: 35px; padding: 9px 13px; background: #1a1a1a; color: #F5C200; border-radius: 9px; font-size: 13px; font-weight: 700; text-decoration: none; }
.top-link.secondary { background: #fff; color: #555; border: 1.5px solid #e0e0e0; }
.top-login .login-fout { color: #c0392b; font-size: 12px; font-weight: 600; white-space: nowrap; }
.section { padding: 86px max(18px, calc((100vw - 1180px) / 2)); position: relative; }
.hero { min-height: calc(100vh - 78px); display: grid; grid-template-columns: minmax(0, 1.02fr) minmax(320px, 0.98fr); align-items: center; gap: 56px; padding-top: 54px; background: radial-gradient(circle at 16% 16%, rgba(245,194,0,0.28), transparent 26%), linear-gradient(135deg, #fffaf0 0%, #fff7dd 42%, #ffffff 100%); }
.hero::after { content: ""; position: absolute; left: 0; right: 0; bottom: -1px; height: 90px; background: linear-gradient(176deg, transparent 49%, #17120d 50%); pointer-events: none; }
.eyebrow { display: inline-flex; align-items: center; gap: 9px; padding: 8px 13px; border-radius: 999px; background: #17120d; color: #F5C200; font-size: 13px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.04em; }
.hero h1 { margin-top: 22px; max-width: 720px; font-size: clamp(42px, 7vw, 86px); line-height: 0.94; letter-spacing: -0.04em; color: #17120d; }
.hero h1 em { color: #d31d20; font-style: normal; }
.hero p { margin-top: 22px; max-width: 660px; color: #40362c; font-size: clamp(18px, 2vw, 22px); line-height: 1.48; }
.hero-actions { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 32px; }
.btn { display: inline-flex; align-items: center; justify-content: center; gap: 9px; min-height: 52px; padding: 14px 22px; border-radius: 999px; border: 2px solid #17120d; font-weight: 900; text-decoration: none; box-shadow: 0 12px 24px rgba(0,0,0,0.12); transition: transform 0.2s ease, box-shadow 0.2s ease; }
.btn:hover { transform: translateY(-2px); box-shadow: 0 16px 30px rgba(0,0,0,0.16); }
.btn.primary { background: #F5C200; color: #17120d; }
.btn.dark { background: #17120d; color: #fff; }
.trust-strip { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 28px; }
.trust-pill { display: inline-flex; align-items: center; gap: 8px; padding: 10px 13px; background: #fff; border: 1px solid rgba(23,18,13,0.1); border-radius: 999px; color: #17120d; font-weight: 800; box-shadow: 0 10px 28px rgba(0,0,0,0.08); }
.hero-visual { position: relative; min-height: 620px; display: grid; place-items: center; }
.burst { position: absolute; inset: 4% -4% 6% 4%; background: #F5C200; clip-path: polygon(48% 0, 56% 26%, 80% 5%, 73% 34%, 100% 30%, 78% 49%, 96% 67%, 68% 64%, 66% 94%, 48% 69%, 26% 98%, 29% 65%, 2% 71%, 23% 50%, 0 31%, 29% 34%, 21% 6%, 43% 26%); opacity: 0.98; filter: drop-shadow(0 30px 40px rgba(245,194,0,0.28)); }
.photo-card { position: relative; width: min(430px, 90vw); aspect-ratio: 0.72; border-radius: 34px; overflow: hidden; border: 10px solid #fff; box-shadow: 0 28px 60px rgba(0,0,0,0.22); transform: rotate(1.5deg); background: #ddd; }
.photo-card img { width: 100%; height: 100%; object-fit: cover; display: block; }
.delivery-card { position: absolute; right: 0; bottom: 42px; width: 225px; padding: 18px; border-radius: 28px; background: #17120d; color: #fff; box-shadow: 0 18px 40px rgba(0,0,0,0.22); transform: rotate(-3deg); }
.delivery-card strong { display: block; color: #F5C200; font-size: 26px; }
.delivery-card span { display: block; margin-top: 3px; font-size: 13px; font-weight: 800; }
.dark-section { background: #17120d; color: #fff; }
.section-head { max-width: 760px; margin-bottom: 34px; }
.section-head.center { margin-left: auto; margin-right: auto; text-align: center; }
.section-head h2 { font-size: clamp(32px, 4.6vw, 58px); line-height: 1; letter-spacing: -0.035em; }
.section-head p { margin-top: 14px; color: #6c6259; font-size: 18px; line-height: 1.55; }
.dark-section .section-head p { color: rgba(255,255,255,0.72); }
.feature-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 18px; }
.feature-card, .podcast-card, .about-copy, .register-card { border-radius: 30px; background: #fff; box-shadow: 0 18px 45px rgba(0,0,0,0.09); }
.feature-card { min-height: 240px; padding: 24px; border: 1px solid rgba(23,18,13,0.08); color: #17120d; display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.2s ease; }
.feature-card:hover { transform: translateY(-4px); }
.icon-badge { width: 58px; height: 58px; display: grid; place-items: center; border-radius: 20px; background: #F5C200; border: 3px solid #17120d; font-size: 27px; box-shadow: 7px 7px 0 #d31d20; }
.feature-card h3 { margin-top: 26px; font-size: 21px; line-height: 1.12; letter-spacing: -0.02em; }
.feature-card p { margin-top: 12px; color: #665d55; font-size: 15px; line-height: 1.45; font-weight: 650; }
.about-grid { display: grid; grid-template-columns: 0.9fr 1.1fr; gap: 34px; align-items: center; }
.about-photo { position: relative; border-radius: 34px; overflow: hidden; min-height: 520px; box-shadow: 0 24px 58px rgba(0,0,0,0.14); border: 10px solid #fff; background: #F5C200; }
.about-photo img { width: 100%; height: 100%; min-height: 520px; object-fit: cover; display: block; }
.about-copy { padding: clamp(28px, 4vw, 48px); }
.about-copy p { color: #443b32; font-size: 19px; line-height: 1.65; margin-top: 18px; }
.supporter-note { margin-top: 24px; padding: 18px; border-radius: 22px; background: #fff3bc; border-left: 8px solid #d31d20; font-weight: 900; }
.podcast-section { background: linear-gradient(135deg, #d31d20 0%, #a41014 54%, #17120d 54%, #17120d 100%); color: #fff; }
.podcast-card { display: grid; grid-template-columns: minmax(108px, 160px) 1fr auto; gap: 22px; align-items: center; padding: clamp(24px, 4vw, 40px); background: rgba(255,255,255,0.97); color: #17120d; border: 4px solid #F5C200; }
.podcast-logo { width: 100%; aspect-ratio: 1; object-fit: contain; border-radius: 24px; background: #17120d; box-shadow: 0 14px 28px rgba(0,0,0,0.18); }
.podcast-card h2 { font-size: clamp(30px, 4vw, 48px); letter-spacing: -0.03em; }
.podcast-card p { margin-top: 8px; color: #4f463d; font-size: 18px; line-height: 1.5; }
.podcast-badge { display: inline-flex; width: fit-content; margin-bottom: 10px; padding: 8px 12px; border-radius: 999px; background: #17120d; color: #F5C200; font-size: 13px; font-weight: 900; }
.register-section { background: linear-gradient(180deg, #fffaf0 0%, #fff 100%); }
.register-wrap { max-width: 760px; margin: 0 auto; }
.register-card { padding: clamp(22px, 4vw, 42px); border: 1px solid rgba(23,18,13,0.08); }
.register-head { text-align: center; margin-bottom: 2rem; }
.register-head img { width: 90px; margin-bottom: 1rem; }
.register-head h2 { font-size: clamp(30px, 5vw, 50px); line-height: 1.03; letter-spacing: -0.035em; color: #1a1a1a; margin-bottom: 0.8rem; }
.register-head p { color: #665d55; font-size: 18px; }
.register-info { font-size: 14px; color: #40362c; background: #fff6d1; padding: 16px; border-radius: 18px; margin-top: 1.5rem; line-height: 1.55; border: 1px solid rgba(245,194,0,0.45); }
.required { color: #c0392b; }
.message { font-size: 13px; padding: 12px; border-radius: 8px; margin-bottom: 1rem; border-left: 3px solid transparent; }
.message.error { color: #c0392b; background: #fdecea; border-left-color: #c0392b; }
.message.success { color: #1b5e20; background: #e8f5e9; border-left-color: #1b5e20; }
.public-footer { padding: 42px max(18px, calc((100vw - 1180px) / 2)); background: #17120d; color: #fff; }
.footer-grid { display: flex; flex-wrap: wrap; gap: 14px 18px; align-items: center; }
.footer-brand { font-size: 24px; font-weight: 900; color: #F5C200; }
.footer-item { color: rgba(255,255,255,0.82); font-weight: 750; white-space: nowrap; }
.footer-item a { color: inherit; text-decoration: none; }
.reveal { opacity: 0; transform: translateY(24px); transition: opacity 0.7s ease, transform 0.7s ease; }
.reveal.visible { opacity: 1; transform: translateY(0); }
.public-page .form-row label { font-size: 13px; color: #2f2922; }
.public-page .form-row input, .public-page .form-row select { min-height: 52px; border-radius: 15px; border-color: #e7ded0; font-size: 16px; }
.public-page .submit-btn { min-height: 56px; border-radius: 17px; box-shadow: 0 12px 24px rgba(245,194,0,0.24); }

@media (max-width: 760px) {
    html { scroll-padding-top: 122px; }
    .public-header { position: sticky; align-items: center; gap: 10px; padding: 10px 14px; flex-wrap: wrap; }
    .brand { gap: 8px; min-width: 0; }
    .brand img { width: 38px; height: 38px; }
    .brand span { font-size: 13px; line-height: 1; }
    .site-nav { order: 3; flex: 0 0 100%; justify-content: flex-start; gap: 7px; overflow-x: auto; padding: 2px 0 1px; scrollbar-width: none; }
    .site-nav::-webkit-scrollbar { display: none; }
    .site-nav a { padding: 8px 10px; background: #fff; border: 1px solid rgba(23,18,13,0.08); font-size: 12px; box-shadow: 0 6px 14px rgba(0,0,0,0.06); }
    .top-login { max-width: 172px; justify-content: flex-end; gap: 6px; }
    .top-login input { width: 108px; min-width: 0; padding: 8px 9px; font-size: 12px; border-radius: 10px; }
    .top-login button { padding: 8px 10px; font-size: 12px; border-radius: 10px; }
    .top-link { min-height: 34px; padding: 8px 10px; font-size: 12px; }
    .top-login .login-fout { position: absolute; top: 56px; right: 14px; }

    .section { padding: 46px 16px; }
    .hero { min-height: auto; grid-template-columns: 1fr; gap: 20px; padding-top: 18px; padding-bottom: 58px; background: linear-gradient(180deg, #fff7df 0%, #fffaf0 60%, #fff 100%); }
    .hero::after { height: 34px; background: linear-gradient(174deg, transparent 48%, #17120d 49%); }
    .hero-copy { display: flex; flex-direction: column; align-items: flex-start; }
    .hero-visual { order: -1; min-height: 350px; margin: 0 -6px 2px; }
    .burst { inset: 2% -8% 3% -8%; transform: rotate(4deg); }
    .photo-card { width: min(270px, 72vw); border-radius: 26px; border-width: 7px; box-shadow: 0 18px 34px rgba(0,0,0,0.2); transform: rotate(1deg); }
    .delivery-card { right: 8px; bottom: 10px; width: min(176px, 48vw); padding: 14px; border-radius: 22px; }
    .delivery-card strong { font-size: 24px; }
    .delivery-card span { font-size: 12px; line-height: 1.22; }
    .eyebrow { font-size: 11px; padding: 7px 11px; }
    .hero h1 { margin-top: 16px; font-size: clamp(36px, 10.5vw, 46px); line-height: 0.98; letter-spacing: -0.035em; }
    .hero p { margin-top: 15px; font-size: 16px; line-height: 1.46; }
    .hero-actions { width: 100%; display: grid; grid-template-columns: 1fr; gap: 10px; margin-top: 22px; }
    .btn { width: 100%; min-height: 50px; padding: 13px 16px; border-radius: 16px; box-shadow: 0 10px 18px rgba(0,0,0,0.1); }
    .trust-strip { display: grid; grid-template-columns: 1fr; width: 100%; gap: 9px; margin-top: 18px; }
    .trust-pill { width: 100%; justify-content: flex-start; padding: 10px 12px; border-radius: 16px; font-size: 14px; }

    .section-head { margin-bottom: 22px; }
    .section-head.center { text-align: left; }
    .section-head h2 { font-size: clamp(31px, 9vw, 40px); line-height: 1.02; }
    .section-head p { font-size: 16px; line-height: 1.5; }
    .feature-grid, .about-grid, .podcast-card { grid-template-columns: 1fr; }
    .feature-grid { gap: 12px; }
    .feature-card, .podcast-card, .about-copy, .register-card { border-radius: 24px; }
    .feature-card { min-height: 0; padding: 18px; display: grid; grid-template-columns: auto 1fr; align-items: center; gap: 16px; }
    .feature-card h3 { margin-top: 0; font-size: 18px; line-height: 1.18; }
    .feature-card p { grid-column: 2; margin-top: -8px; font-size: 14px; }
    .icon-badge { width: 48px; height: 48px; border-radius: 16px; font-size: 23px; box-shadow: 5px 5px 0 #d31d20; }

    .about-grid { gap: 16px; }
    .about-photo { min-height: 330px; border-radius: 26px; border-width: 7px; }
    .about-photo img { min-height: 330px; }
    .about-copy { padding: 22px; }
    .about-copy p { font-size: 16px; line-height: 1.58; margin-top: 14px; }
    .supporter-note { margin-top: 18px; padding: 15px; border-radius: 17px; font-size: 15px; }

    .podcast-section { background: linear-gradient(160deg, #d31d20 0%, #a41014 50%, #17120d 50%, #17120d 100%); }
    .podcast-card { align-items: start; gap: 14px; padding: 20px; border-width: 3px; }
    .podcast-logo { width: min(150px, 46vw); justify-self: center; border-radius: 20px; }
    .podcast-card h2 { font-size: 32px; }
    .podcast-card p { font-size: 16px; line-height: 1.45; }
    .podcast-badge { font-size: 12px; }
    .register-wrap { max-width: none; }
    .register-card { padding: 20px; }
    .register-head { margin-bottom: 1.35rem; }
    .register-head img { width: 72px; margin-bottom: 0.7rem; }
    .register-head h2 { font-size: clamp(29px, 8.5vw, 38px); line-height: 1.05; }
    .register-head p { font-size: 16px; line-height: 1.45; }
    .public-page .form-row label { font-size: 12px; }
    .public-page .form-row input, .public-page .form-row select { min-height: 50px; border-radius: 14px; font-size: 16px; }
    .form-row-2 { grid-template-columns: 1fr; gap: 0; }
    .register-info { font-size: 13px; border-radius: 16px; }

    .public-footer { padding: 32px 16px; }
    .footer-grid { align-items: start; gap: 10px; }
    .footer-brand { font-size: 22px; margin-bottom: 6px; }
    .footer-item { white-space: normal; font-size: 14px; }
}

@media (max-width: 380px) {
    .hero h1 { font-size: 34px; }
    .photo-card { width: min(248px, 70vw); }
    .hero-visual { min-height: 324px; }
    .delivery-card { width: 158px; }
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
</style>
</head>
<body>

<?php if (!$ingelogd): ?>
<div class="public-page">
<header class="public-header">
  <a class="brand" href="#top" aria-label="Julian's Verse Eitjes">
    <img src="logo.png" alt="">
    <span>Julian's<br>Verse Eitjes</span>
  </a>
  <nav class="site-nav" aria-label="Hoofdnavigatie">
    <a href="#waarom-eitjes">Waarom mijn eitjes?</a>
    <a href="#wie-ben-ik">Wie ben ik?</a>
    <a href="#podcast">Podcast</a>
    <a href="#inschrijven">Inschrijven</a>
  </nav>
  <?php if ($administratie_ingelogd): ?>
    <nav class="top-login" aria-label="Administratie">
      <a class="top-link" href="administratie.php">Administratie</a>
      <a class="top-link secondary" href="?uitloggen=1">Uitloggen</a>
    </nav>
  <?php else: ?>
  <form class="top-login" method="post">
    <?php if (isset($login_fout)): ?>
      <span class="login-fout">Onjuist wachtwoord</span>
    <?php endif; ?>
    <input type="password" name="wachtwoord" placeholder="Wachtwoord" aria-label="Wachtwoord">
    <button type="submit">Login</button>
  </form>
  <?php endif; ?>
</header>

<main id="top">
  <section class="section hero">
    <div class="hero-copy reveal">
      <span class="eyebrow">Zwolse Wijk - elke zondag</span>
      <h1>Verse vrije-uitloopeieren, rechtstreeks van de <em>boerderij.</em></h1>
      <p>Ik ben Julian, 14 jaar, en iedere week bezorg ik verse eieren aan huis in de Zwolse Wijk. Rechtstreeks van de boer, persoonlijk bezorgd.</p>
      <div class="hero-actions">
        <a class="btn primary" href="#inschrijven">Schrijf je in</a>
        <a class="btn dark" href="https://wa.me/31619528377">WhatsApp Julian</a>
      </div>
      <div class="trust-strip" aria-label="Waarom mensen Julian vertrouwen">
        <span class="trust-pill">⭐ Meer dan 35 tevreden gezinnen</span>
        <span class="trust-pill">🥚 Wekelijks vers van de boer</span>
        <span class="trust-pill">🚲 Bezorging op zondag</span>
      </div>
    </div>
    <div class="hero-visual reveal">
      <div class="burst" aria-hidden="true"></div>
      <figure class="photo-card">
        <img src="julian-lyon.jpg" alt="Julian met voetbalsjaal in Lyon">
      </figure>
      <div class="delivery-card">
        <strong>35+</strong>
        <span>gezinnen krijgen al eitjes van Julian</span>
      </div>
    </div>
  </section>

  <section class="section dark-section" id="waarom-eitjes">
    <div class="section-head center reveal">
      <h2>Waarom zijn mijn eitjes zo lekker?</h2>
      <p>Mijn eieren komen rechtstreeks van de boerderij en worden iedere week vers geleverd. Daardoor zijn ze vaak veel verser dan eieren die eerst via distributiecentra en supermarkten reizen. Dat proef je.</p>
    </div>
    <div class="feature-grid">
      <article class="feature-card reveal"><span class="icon-badge">🥚</span><div><h3>Altijd vers</h3><p>Rechtstreeks van de boerderij naar jouw keukentafel.</p></div></article>
      <article class="feature-card reveal"><span class="icon-badge">🐔</span><div><h3>Van vrije-uitloopkippen</h3><p>Eieren van kippen met ruimte om vrij rond te lopen.</p></div></article>
      <article class="feature-card reveal"><span class="icon-badge">🚜</span><div><h3>Geen onnodige tussenhandel</h3><p>Geen lange route via grote distributieketens, maar direct van de boer.</p></div></article>
      <article class="feature-card reveal"><span class="icon-badge">🚲</span><div><h3>Lokaal bezorgd</h3><p>Ik bezorg de eitjes persoonlijk bij gezinnen in de Zwolse Wijk.</p></div></article>
    </div>
  </section>

  <section class="section" id="wie-ben-ik">
    <div class="about-grid">
      <figure class="about-photo reveal">
        <img src="julian-trofee.png" alt="Julian met de Go Ahead Eagles beker">
      </figure>
      <div class="about-copy reveal">
        <div class="section-head">
          <h2>Wie ben ik?</h2>
        </div>
        <p>Hoi! Ik ben Julian, 14 jaar, en ik woon in de Zwolse Wijk. Wat begon als een klein idee is inmiddels uitgegroeid tot een bezorgservice voor meer dan 35 gezinnen in de buurt.</p>
        <p>Iedere week haal ik verse vrije-uitloopeieren rechtstreeks bij de boerderij op en bezorg ik ze persoonlijk aan huis.</p>
        <p>Naast mijn passie voor ondernemen ben ik groot supporter van Go Ahead Eagles en maak ik de Eagles Eye Podcast.</p>
        <div class="supporter-note">Mijn doel is simpel: mensen in de buurt voorzien van echt verse eieren, met persoonlijke service en een glimlach aan de deur.</div>
      </div>
    </div>
  </section>

  <section class="section podcast-section" id="podcast">
    <div class="podcast-card reveal">
      <img class="podcast-logo" src="eagles-eye-podcast.png" alt="Eagles Eye Podcast logo">
      <div>
        <span class="podcast-badge">Bekend van de Eagles Eye Podcast 🎙️</span>
        <h2>Eagles Eye Podcast</h2>
        <p>Naast mijn bezorgservice maak ik ook de Eagles Eye Podcast. Daar praat ik over Go Ahead Eagles en alles rondom de club.</p>
      </div>
      <a class="btn primary" href="https://www.EaglesEyePodcast.nl">Bekijk de podcast</a>
    </div>
  </section>

  <section class="section register-section" id="inschrijven">
    <div class="register-wrap reveal">
      <div class="register-card">
        <div class="register-head">
          <img src="logo.png" alt="Julian's Verse Eitjes">
          <h2>Wil jij ook iedere week verse eitjes aan huis?</h2>
          <p>Vul hieronder je gegevens in. Ik neem daarna persoonlijk contact met je op om de eerste levering af te stemmen.</p>
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
                <option value="Alexander Hegiusstraat">Alexander Hegiusstraat</option>
                <option value="Anna Reynvaanstraat">Anna Reynvaanstraat</option>
                <option value="Borgerlerstraat">Borgerlerstraat</option>
                <option value="Florens Radewijnszstraat">Florens Radewijnszstraat</option>
                <option value="Hallensstraat">Hallensstraat</option>
                <option value="Jacob van Bredastraat">Jacob van Bredastraat</option>
                <option value="JP Sweelinckstraat">JP Sweelinckstraat</option>
                <option value="Johannes Sinthenstraat">Johannes Sinthenstraat</option>
                <option value="Kromme Kerkstraat">Kromme Kerkstraat</option>
                <option value="Radstakeweg">Radstakeweg</option>
                <option value="Reinckenstraat">Reinckenstraat</option>
                <option value="Richard Paffraedstraat">Richard Paffraedstraat</option>
                <option value="Sallandstraat">Sallandstraat</option>
                <option value="Sint Jurrienstraat">Sint Jurrienstraat</option>
                <option value="Zwolseweg">Zwolseweg</option>
                <option value="Mijn straat staat er niet tussen, maar ik wil wel graag eitjes">Mijn straat staat er niet tussen, maar ik wil wel graag eitjes</option>
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
                <option value="eenmalig">Eenmalig</option>
              </select>
            </div>
          </div>

          <button type="submit" class="submit-btn">Inschrijven</button>
        </form>

        <div class="register-info">
          <strong>Hoe werkt het?</strong><br>
          1. Vul het formulier in<br>
          2. Ik neem persoonlijk contact met je op<br>
          3. Je eerste levering volgt snel<br>
          <br>
          Vragen? Bel of app: 06-19 52 83 77
        </div>
      </div>
    </div>
  </section>
</main>

<footer class="public-footer">
  <div class="footer-grid">
    <div class="footer-brand">Julian's Verse Eitjes</div>
    <div class="footer-item">📍 Zwolse Wijk</div>
    <div class="footer-item">🥚 Verse vrije-uitloopeieren</div>
    <div class="footer-item">🚜 Rechtstreeks van de boerderij</div>
    <div class="footer-item">🚲 Iedere zondag bezorgd</div>
    <div class="footer-item">🌐 eitjes.kunkeler.net</div>
    <div class="footer-item"><a href="https://www.EaglesEyePodcast.nl">🎙 EaglesEyePodcast.nl</a></div>
    <div class="footer-item">📱 06-19 52 83 77</div>
  </div>
</footer>
</div>

<script>
if ('IntersectionObserver' in window) {
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, {threshold: 0.12});
    document.querySelectorAll('.reveal').forEach(function(el) { observer.observe(el); });
} else {
    document.querySelectorAll('.reveal').forEach(function(el) { el.classList.add('visible'); });
}

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
        });
}

function renderWeekLijst(klanten, week, jaar) {
    document.getElementById('s-klanten').textContent = klanten.length;
    if (klanten.length === 0) {
        document.getElementById('week-lijst').innerHTML = '<div class="leeg">Geen klanten deze week</div>';
        return;
    }

    var straatGroepen = {};
    klanten.forEach(function(k) {
        if (!straatGroepen[k.straat]) straatGroepen[k.straat] = [];
        straatGroepen[k.straat].push(k);
    });

    var html = '';
    Object.keys(straatGroepen).sort().forEach(function(straat) {
        html += '<div class="straat-header">' + esc(straat) + '</div>';
        straatGroepen[straat].forEach(function(k) {
            var status = k.betaald ? 'betaald' : (k.tikkie ? 'tikkie' : 'open');
            var badgeTekst = k.betaald ? 'Betaald' : (k.tikkie ? 'Tikkie verstuurd' : 'Tikkie open');
            var bedrag = (k.aantal_dozen * 3.30).toFixed(2);
            var id = k.id;

            html += '<div class="klant-card">';
            html += '<div class="klant-top">';
            html += '<div><div class="klant-naam">' + esc(k.naam) + '</div>';
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
            var straatGroepen = {};
            data.forEach(function(k) {
                if (!straatGroepen[k.straat]) straatGroepen[k.straat] = [];
                straatGroepen[k.straat].push(k);
            });
            var html = '';
            Object.keys(straatGroepen).sort().forEach(function(straat) {
                html += '<div class="straat-header">' + esc(straat) + '</div>';
                straatGroepen[straat].forEach(function(k) {
                    var freqMap   = {wekelijks: 'Wekelijks', '2wekelijks': '2-wekelijks', '3wekelijks': '3-wekelijks', eenmalig: 'Eenmalig'};
                    var freqClass = {wekelijks: 'freq-w', '2wekelijks': 'freq-2w', '3wekelijks': 'freq-3w', eenmalig: 'freq-3w'};
                    var fOpt = function(val, label, cur) {
                        return '<option value="' + val + '"' + (cur === val ? ' selected' : '') + '>' + label + '</option>';
                    };

                    html += '<div class="klant-card" id="kkaart-' + k.id + '">';

                    // Vaste weergave bovenaan
                    html += '<div style="display:flex;justify-content:space-between;align-items:flex-start;">';
                    html += '<div>';
                    html += '<div class="klant-naam">' + esc(k.naam) + '</div>';
                    html += '<div class="klant-adres">' + esc(k.straat) + ' ' + esc(k.huisnummer) + '</div>';
                    if (k.telefoon) html += '<div class="klant-adres">' + esc(k.telefoon) + '</div>';
                    html += '<div style="margin-top:4px;font-size:12px;color:#555;">' + k.aantal_dozen + ' doos · startweek ' + k.startweek + '</div>';
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
        startweek:    document.getElementById('b-start-' + id).value
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
