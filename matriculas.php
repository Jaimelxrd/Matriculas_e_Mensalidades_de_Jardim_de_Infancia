<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Buscar turmas do banco de dados
$db = getDB();
$stmt = $db->query("SELECT * FROM turmas ORDER BY id");
$turmas = $stmt->fetchAll();

$erro = isset($_SESSION['erro_matricula']) ? $_SESSION['erro_matricula'] : null;
unset($_SESSION['erro_matricula']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link
  href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css"
  rel="stylesheet"
/>
  <title>Matrículas — Kindergarten</title>
  <style>
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

    :root {
      --red:        #ff4d4d;
      --red-dark:   #e63535;
      --yellow:     #ffd93d;
      --orange:     #ff983e;
      --dark:       #1a1a2e;
      --dark2:      #16213e;
      --text:       #333;
      --text-light: #666;
      --bg:         #fffaf5;
      --white:      #ffffff;
      --transition: 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      --shadow:     0 8px 32px rgba(0,0,0,0.10);
      --shadow-lg:  0 20px 60px rgba(0,0,0,0.14);
      --radius:     12px;
    }

    html { scroll-behavior: smooth; }
    body { font-family: "Poppins", sans-serif; background: #fffaf5; color: #222; overflow-x: hidden; }
    a { text-decoration: none; color: inherit; }
    img { display: block; max-width: 100%; }

    header {
      position: fixed;
      top: 0; left: 0; right: 0;
      z-index: 1000;
      transition: var(--transition);
      padding: 10px 0;
    }
    header.scrolled {
      background: rgba(255,255,255,0.96);
      backdrop-filter: blur(14px);
      box-shadow: 0 4px 28px rgba(0,0,0,0.08);
      padding: 4px 0;
    }
    .nav-inner {
      max-width: 1200px;
      margin: 0 auto;
      padding: 14px 24px;
      display: flex;
      align-items: center;
      gap: 24px;
    }
    .logo {
      display: flex;
      align-items: center;
      gap: 10px;
      font-family: 'Fredoka One', cursive;
      font-size: 1.5rem;
      color: var(--dark);
      margin-right: auto;
    }
   
    .logo:hover div { transform: rotate(-8deg) scale(1.1); }
    .nav-links { display: flex; gap: 4px; }
    .nav-links a {
      font-weight: 700;
      font-size: 0.9rem;
      color: var(--text-light);
      padding: 8px 14px;
      border-radius: 999px;
      transition: var(--transition);
    }
    .nav-links a:hover,
    .nav-links a.active { color: var(--red); background: rgba(255,77,77,0.08); }
    .nav-actions { display: flex; align-items: center; gap: 12px; }
    .btn-nav {
      background: var(--red);
      color: #fff;
      font-weight: 700;
      font-size: 0.9rem;
      padding: 11px 22px;
      border-radius: 999px;
      transition: var(--transition);
      white-space: nowrap;
    }
    .btn-nav:hover {
      background: var(--red-dark);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(255,77,77,0.35);
    }
    .hamburger {
      display: none;
      flex-direction: column;
      gap: 5px;
      background: none;
      border: none;
      cursor: pointer;
      padding: 4px;
    }
    .hamburger span {
      display: block;
      width: 24px; height: 2.5px;
      background: var(--dark);
      border-radius: 3px;
      transition: var(--transition);
    }
    .hamburger.open span:nth-child(1) { transform: translateY(7.5px) rotate(45deg); }
    .hamburger.open span:nth-child(2) { opacity: 0; }
    .hamburger.open span:nth-child(3) { transform: translateY(-7.5px) rotate(-45deg); }
    .nav-links.mobile-open {
      display: flex;
      flex-direction: column;
      position: fixed;
      inset: 0;
      background: rgba(255,255,255,0.98);
      align-items: center;
      justify-content: center;
      gap: 12px;
      z-index: 999;
    }
    .nav-links.mobile-open a { font-size: 1.4rem; padding: 12px 32px; }

    .hero-section {
      position: relative;
      height: 100vh;
      min-height: 560px;
      width: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
    }
    .video-background {
      position: absolute;
      inset: 0;
      z-index: 1;
    }
    #videoFundo {
      width: 100%; height: 100%;
      object-fit: cover;
    }
    .video-overlay {
      position: absolute;
      inset: 0;
      z-index: 2;
      background:linear-gradient(rgba(31, 31, 31, 0.43), rgba(196, 172, 172, 0.45));
    }
    .hero-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
      position: relative;
      z-index: 3;
      max-width: 1100px;
      width: 100%;
      animation: heroIn 1s ease both;
    }
    @keyframes heroIn {
      from { opacity: 0; transform: translateY(40px); }
      to   { opacity: 1; transform: none; }
    }
    .hero-tag {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(255,217,61,0.18);
      border: 1px solid rgba(255,217,61,0.45);
      color: var(--yellow);
      font-weight: 700;
      font-size: 0.75rem;
      letter-spacing: 2px;
      text-transform: uppercase;
      padding: 7px 18px;
      border-radius: 999px;
      margin-bottom: 24px;
    }
    .hero-tag i { font-size: 0.8rem; }
    .hero-title {
      font-size: clamp(2rem, 5vw, 3.8rem);
      font-weight: 800;
      color: #fff;
      line-height: 1.15;
      margin-bottom: 22px;
      max-width: 780px;
      text-shadow: 0 2px 16px rgba(0,0,0,0.4);
    }
    .hero-title span { color: var(--yellow); }
    .hero-subtitle {
      font-size: clamp(0.95rem, 2vw, 1.1rem);
      color: rgba(255,255,255,0.82);
      max-width: 540px;
      line-height: 1.7;
      margin-bottom: 36px;
      font-weight: 400;
    }
    .hero-actions { display: flex; gap: 16px; flex-wrap: wrap; }
    .hero-button {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: var(--orange);
      color: #fff;
      padding: 15px 32px;
      border-radius: 999px;
      font-size: 1rem;
      font-weight: 700;
      transition: var(--transition);
      box-shadow: 0 8px 24px rgba(255,152,62,0.4);
    }
    .hero-button:hover {
      background: #e8821a;
      transform: translateY(-3px);
      box-shadow: 0 14px 32px rgba(255,152,62,0.5);
    }
    .hero-button-ghost {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: transparent;
      color: #fff;
      padding: 15px 30px;
      border-radius: 999px;
      font-size: 1rem;
      font-weight: 600;
      border: 2px solid rgba(255,255,255,0.4);
      transition: var(--transition);
    }
    .hero-button-ghost:hover {
      background: rgba(255,255,255,0.12);
      border-color: rgba(255,255,255,0.7);
    }
    .hero-badges {
      position: absolute;
      right: 5%;
      top: 50%;
      transform: translateY(-50%);
      z-index: 3;
      display: flex;
      flex-direction: column;
      gap: 16px;
    }
    .hero-badge {
      background: rgba(255,255,255,0.12);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 16px;
      padding: 16px 20px;
      color: #fff;
      text-align: center;
      min-width: 130px;
      animation: floatBadge 3s ease-in-out infinite;
    }
    .hero-badge:nth-child(2) { animation-delay: 1s; }
    .hero-badge:nth-child(3) { animation-delay: 2s; }
    @keyframes floatBadge {
      0%,100% { transform: translateY(0); }
      50%      { transform: translateY(-8px); }
    }
    .badge-num {
      font-family: 'Fredoka One', cursive;
      font-size: 1.8rem;
      color: var(--yellow);
      line-height: 1;
      margin-bottom: 4px;
    }
    .badge-lbl { font-size: 0.72rem; opacity: 0.85; font-weight: 600; letter-spacing: 0.5px; }
    .scroll-indicator {
      position: absolute;
      bottom: 30px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 3;
      color: rgba(255,255,255,0.7);
      font-size: 1.3rem;
      animation: bounce 2s infinite;
    }
    @keyframes bounce {
      0%,100% { transform: translateX(-50%) translateY(0); }
      50%      { transform: translateX(-50%) translateY(8px); }
    }

    .servicos-section {
      background-color: #f3f3e9;
      padding: 100px 20px;
    }
    .servicos-container {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      gap: 40px;
    }
    .servicos-header {
      flex-shrink: 0;
      width: 280px;
      padding: 10px 0;
    }
    .servicos-title {
      font-size: clamp(1.5rem, 2.5vw, 2.2rem);
      font-weight: 700;
      color: #2c2c2c;
      margin-bottom: 20px;
    }
    .servicos-intro-text {
      font-size: 0.97rem;
      color: #6e6e6e;
      line-height: 1.6;
      margin-bottom: 28px;
    }
    .servicos-talk-button {
      display: inline-block;
      background-color: #90d381;
      color: #2c2c2c;
      padding: 12px 24px;
      border-radius: 8px;
      font-size: 0.95rem;
      font-weight: 600;
      transition: opacity 0.3s, transform 0.3s;
    }
    .servicos-talk-button:hover { opacity: 0.88; transform: translateY(-2px); }
    .servicos-grid {
      flex-grow: 1;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
    }
    .servico-card {
      background: #fff;
      padding: 28px;
      border-radius: var(--radius);
      box-shadow: 0 4px 14px rgba(0,0,0,0.05);
      border: 1px solid #f0f0f0;
      transition: transform 0.35s ease, box-shadow 0.35s ease;
    }
    .servico-card:hover { transform: translateY(-6px); box-shadow: var(--shadow); }
    .featured-card { background: #2c3a37; grid-row: span 2; }
    .card-icon-light { color: #2c2c2c; margin-bottom: 18px; }
    .card-icon-light i { font-size: 1.1rem; margin-right: 8px; }
    .card-icon-light span { font-size: 1rem; font-weight: 600; }
    .card-icon-dark { color: #fff; margin-bottom: 18px; }
    .card-icon-dark i { font-size: 1.1rem; margin-right: 8px; }
    .card-icon-dark span { font-size: 1rem; font-weight: 600; }
    .card-text-light { font-size: 0.88rem; color: #6e6e6e; line-height: 1.6; margin-bottom: 16px; }
    .card-text-dark  { font-size: 0.88rem; color: #fff; line-height: 1.6; margin-bottom: 16px; }
    .read-more-dark  { color: #90d381; font-size: 0.88rem; font-weight: 600; display: block; transition: letter-spacing 0.25s; }
    .read-more-dark:hover { letter-spacing: 0.5px; }

    .documentos-section {
      background: #f4f7fa;
      padding: 100px 20px;
      text-align: center;
    }
    .documentos-container { max-width: 900px; margin: 0 auto; }
    .documentos-title { font-size: 2rem; font-weight: 700; color: #007bff; margin-bottom: 28px; }
    .documentos-content-box {
      background: #fff;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 4px 14px rgba(0,0,0,0.05);
      text-align: left;
      border: 1px solid #eee;
    }
    .documento-list { list-style: none; padding: 0; }
    .documento-list li {
      font-size: 1.05rem;
      color: #333;
      line-height: 1.6;
      margin-bottom: 18px;
      display: flex;
      align-items: flex-start;
    }
    .documento-list li:last-child { margin-bottom: 0; }
    .lista-bullet {
      width: 8px; height: 8px;
      background: #28a745;
      border-radius: 50%;
      margin-right: 14px;
      margin-top: 8px;
      flex-shrink: 0;
    }

    .section-table { background: #fcfcfc; padding: 80px 20px; text-align: center; }
    .container-table { max-width: 1000px; margin: 0 auto; }
    .title-table { font-size: 2rem; font-weight: 700; color: #2c2c2c; margin-bottom: 48px; }
    .tabela { overflow-x: auto; }
    .table-classica {
      width: 100%; min-width: 500px;
      border-collapse: separate; border-spacing: 0;
      border-radius: var(--radius); overflow: hidden;
      border: 1px solid #eee;
      box-shadow: 0 4px 14px rgba(0,0,0,0.05);
      text-align: center;
    }
    .table-classica th, .table-classica td {
      padding: 15px 10px;
      border-bottom: 1px solid #eee;
      font-size: 0.95rem;
      color: #2c2c2c;
      font-weight: 500;
    }
    .linha-classica th {
      background: #f9f9f9;
      font-weight: 700;
      padding: 20px 10px;
      border-bottom: none;
    }
    .linha-classica th:first-child { text-align: left; padding-left: 20px; }
    .titulo-classico { background: #fafafa; text-align: left; padding-left: 20px; font-weight: 600; color: #444; }
    .highlight-fee-classic { font-weight: 600; }

    /* ── FORM ── */
    .matricula-section { background: #f7f8fc; padding: 80px 20px; }
    .matricula-container { max-width: 800px; margin: 0 auto; }
    .matricula-header { text-align: center; margin-bottom: 40px; }
    .matricula-header h2 { font-size: 1.8rem; font-weight: 700; color: #2c2c2c; margin-bottom: 6px; }
    .matricula-header p { color: #666; font-size: 1rem; }
    .form-group-box {
      background: #fff;
      padding: 30px;
      margin-bottom: 24px;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }
    .group-title {
      font-size: 1.15rem;
      font-weight: 700;
      color: #2c2c2c;
      border-bottom: 1px solid #e0e0e0;
      padding-bottom: 14px;
      margin-bottom: 24px;
    }
    .form-row { display: flex; flex-wrap: wrap; gap: 18px; margin-bottom: 10px; }
    .form-field { display: flex; flex-direction: column; }
    .form-field.full-width { width: 100%; }
    .form-field.half-width { flex: 1; min-width: 180px; }
    .form-field label { font-size: 0.88rem; font-weight: 600; color: #2c2c2c; margin-bottom: 5px; }
    .form-group-box input:not([type="radio"]),
    .form-group-box select,
    .form-group-box textarea {
      padding: 12px 14px;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      font-size: 0.97rem;
      background: #f7f8fc;
      color: #2c2c2c;
      transition: border-color 0.25s;
      outline: none;
      font-family: "Poppins", sans-serif;
    }
    .form-group-box input:focus,
    .form-group-box select:focus,
    .form-group-box textarea:focus { border-color: var(--orange); }
    .form-group-box textarea { resize: vertical; }
    .form-documents-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 18px; }
    .upload-card {
      border: 1px dashed #e0e0e0;
      border-radius: 10px;
      padding: 18px 14px;
      text-align: center;
      cursor: pointer;
      transition: border-color 0.3s;
    }
    .upload-card:hover { border-color: var(--orange); }
    .upload-card label { display: block; font-size: 0.88rem; font-weight: 600; color: #2c2c2c; margin-bottom: 4px; cursor: pointer; }
    .upload-label { display: block; color: #007bff; font-size: 0.82rem; text-decoration: underline; }
    .hidden-file-input { display: none; }
    .checkbox-container { display: flex; align-items: center; margin-top: 14px; }
    .checkbox-container input[type="checkbox"] { margin-right: 10px; }
    .radio-button-group { display: flex; gap: 10px; }
    .radio-button-group input[type="radio"] { display: none; }
    .radio-label {
      padding: 8px 14px;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      cursor: pointer;
      font-size: 0.88rem;
      color: #666;
      transition: background-color 0.3s, border-color 0.3s, color 0.3s;
    }
    .radio-button-group input[type="radio"]:checked + .radio-label {
      background: var(--orange); border-color: var(--orange); color: #fff;
    }
    .form-actions { display: flex; justify-content: flex-end; gap: 14px; margin-top: 20px; flex-wrap: wrap; }
    .form-actions button {
      padding: 12px 24px; border-radius: 10px;
      font-size: 0.97rem; font-weight: 600;
      cursor: pointer; transition: opacity 0.3s, transform 0.3s;
      font-family: "Poppins", sans-serif;
    }
    .btn-reset { background: transparent; border: 1px solid #e0e0e0; color: #666; }
    .btn-submit { background: var(--orange); color: #fff; border: none; }
    .btn-submit:hover { transform: translateY(-2px); opacity: 0.92; }

    .hero-tennis2 {
      height: 60vh;
      min-height: 360px;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: #fff;
      position: relative;
      overflow: hidden;
      background: linear-gradient(135deg, var(--dark) 0%, var(--dark2) 100%);
    }
    .hero-tennis2::before {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,0.2);
      z-index: 1;
    }
    .hero-content2 { position: relative; z-index: 2; max-width: 800px; padding: 0 24px; }
    .hero-tennis2 h1 { font-size: clamp(1.6rem, 4vw, 2.6rem); font-weight: 700; line-height: 1.3; margin-bottom: 18px; }
    .hero-tennis2 p {
      font-size: 1rem; font-weight: 400; opacity: 0.88;
      margin-bottom: 36px; max-width: 560px; margin-left: auto; margin-right: auto; line-height: 1.7;
    }
    .hero-btn2 {
      background: #0a0a23; color: #fff;
      padding: 14px 36px; border-radius: 40px;
      font-size: 1rem; font-weight: 600;
      transition: 0.3s ease; box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    }
    .hero-btn2:hover { background: #1a1a4a; transform: scale(1.05); }

    .main-footer { background: #121c1a; color: #ccc; padding-top: 50px; margin-top: 20px; }
    .footer-container {
      max-width: 1200px; margin: 0 auto;
      padding: 0 20px 40px;
      display: flex; justify-content: space-between; flex-wrap: wrap;
    }
    .footer-column { width: 20%; min-width: 150px; margin-bottom: 30px; }
    .description-column { width: 25%; min-width: 200px; padding-right: 20px; }
    .footer-column h4 { color: #fff; font-size: 0.9rem; font-weight: 700; margin-bottom: 18px; letter-spacing: 0.5px; }
    .footer-column ul { list-style: none; }
    .footer-column li { margin-bottom: 10px; }
    .footer-column a { color: #ccc; font-size: 0.9rem; transition: color 0.25s; }
    .footer-column a:hover { color: #fff; }
    .footer-bottom {
      max-width: 1200px; margin: 0 auto; padding: 20px;
      border-top: 1px solid #333;
      display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;
    }
    .footer-bottom a { color: #ccc; }

    .reveal {
      opacity: 0;
      transform: translateY(32px);
      transition: opacity 0.6s ease, transform 0.6s ease;
    }
    .reveal.visible { opacity: 1; transform: none; }
    .reveal-left  { opacity: 0; transform: translateX(-32px); transition: opacity 0.6s ease, transform 0.6s ease; }
    .reveal-right { opacity: 0; transform: translateX(32px);  transition: opacity 0.6s ease, transform 0.6s ease; }
    .reveal-left.visible, .reveal-right.visible { opacity: 1; transform: none; }
    .reveal-delay-1 { transition-delay: 0.1s; }
    .reveal-delay-2 { transition-delay: 0.2s; }
    .reveal-delay-3 { transition-delay: 0.3s; }
    .reveal-delay-4 { transition-delay: 0.4s; }

    @media (max-width: 900px) {
      .servicos-container { flex-direction: column; }
      .servicos-header { width: 100%; }
      .servicos-grid { grid-template-columns: 1fr 1fr; }
      .featured-card { grid-row: span 1; }
      .hero-badges { display: none; }
    }
    @media (max-width: 768px) {
      .nav-links { display: none; }
      .hamburger { display: flex; }

      .servicos-grid { grid-template-columns: 1fr; }
      .form-documents-row { grid-template-columns: 1fr 1fr; }
      .form-field.half-width { min-width: 100%; }
      .footer-container { flex-direction: column; }
      .footer-column, .description-column { width: 100%; }
      .footer-bottom { flex-direction: column; gap: 8px; text-align: center; }
      .hero-content { padding: 0 24px; }
      .hero-title { font-size: 2rem; }
      .hero-actions { flex-direction: column; align-items: flex-start; }
    }
    @media (max-width: 480px) {
      .form-documents-row { grid-template-columns: 1fr; }
      .form-actions { justify-content: stretch; }
      .form-actions button { width: 100%; }
    }
  </style>
</head>
<body>

  <header id="navbar">
    <div class="nav-inner">
      <a href="index.php" class="logo">
        <div><i class="ri-graduation-cap-line"></i></div>
        <span>Kindergarten</span>
      </a>
      <nav class="nav-links" id="navLinks">
        <a href="index.php">Início</a>
        <a href="sobre.php">Sobre nós</a>
        <a href="programa.php">Programa</a>
        <a href="matriculas.php" class="active">Matrículas</a>
        <a href="mensalidades.php">Mensalidades</a>
      </nav>
      <div class="nav-actions">
        <a href="contact.php" class="btn-nav">Entrar em contacto</a>
        <a href="perfil_aluno.php" class="btn-nav"><i class="ri-user-line"></i></a>
        <button class="hamburger" id="hamburger" aria-label="Menu">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>
  </header>

  <section class="hero-section">
    <div class="video-background">
      <video autoplay loop muted playsinline id="videoFundo">
        <source src="../imagens/vd2.mp4" type="video/mp4">
      </video>
    </div>
    <div class="video-overlay"></div>

    <div class="hero-content">
      <h1 class="hero-title">
        Garanta já a vaga!
      </h1>
      <p class="hero-subtitle">
        Educação de qualidade, com amor e alegria em cada momento. Um lugar onde a infância é celebrada todos os dias.
      </p>
      <div class="hero-actions">
        <a href="#fazer-matricula" class="hero-button">
          <i class="fas fa-pen-to-square"></i> Matricular agora
        </a>
        <a href="sobre.php" class="hero-button-ghost">
          Saber mais <i class="fas fa-arrow-right"></i>
        </a>
      </div>
    </div>

    <div class="hero-badges">
      <div class="hero-badge">
        <div class="badge-num">500+</div>
        <div class="badge-lbl">Alunos Formados</div>
      </div>
      <div class="hero-badge">
        <div class="badge-num">15+</div>
        <div class="badge-lbl">Anos de Experiência</div>
      </div>
      <div class="hero-badge">
        <div class="badge-num">98%</div>
        <div class="badge-lbl">Satisfação dos Pais</div>
      </div>
    </div>

    <div class="scroll-indicator">
      <i class="fas fa-chevron-down"></i>
    </div>
  </section>

  <section class="servicos-section">
    <div class="servicos-container">
      <div class="servicos-header reveal-left">
        <h2 class="servicos-title">Como funciona o processo de matrícula</h2>
        <p class="servicos-intro-text">
          A matrícula no Kindergarten pode ser feita de forma online através do nosso formulário, ou presencialmente na secretaria da escola. Nosso objetivo é tornar o processo simples, rápido e transparente.
        </p>
        <a href="contact.php" class="servicos-talk-button">Entrar em contacto</a>
      </div>
      <div class="servicos-grid">
        <div class="servico-card featured-card reveal" style="transition-delay:0.1s">
          <div class="card-icon-dark">
            <i class="fas fa-file-pen"></i>
            <span>1. Pré-inscrição</span>
          </div>
          <p class="card-text-dark">Os pais ou encarregados de educação podem preencher um formulário online ou visitar a escola pessoalmente.</p>
          <a href="#fazer-matricula" class="read-more-dark">Fazer Pré-inscrição online →</a>
        </div>
        <div class="servico-card reveal" style="transition-delay:0.15s">
          <div class="card-icon-light"><i class="fas fa-check-circle"></i><span>2. Entrega de Documentos</span></div>
          <p class="card-text-light">Os documentos necessários devem ser entregues na secretaria da escola ou enviados digitalmente.</p>
        </div>
        <div class="servico-card reveal" style="transition-delay:0.2s">
          <div class="card-icon-light"><i class="fas fa-user-friends"></i><span>3. Avaliação e Entrevista</span></div>
          <p class="card-text-light">Algumas crianças podem passar por uma entrevista ou atividade de adaptação leve para que a equipe conheça melhor o perfil de cada aluno.</p>
        </div>
        <div class="servico-card reveal" style="transition-delay:0.25s">
          <div class="card-icon-light"><i class="fas fa-box-open"></i><span>4. Confirmação da matrícula</span></div>
          <p class="card-text-light">Após análise da documentação e cumprimento das etapas anteriores, a matrícula é oficialmente confirmada.</p>
        </div>
        <div class="servico-card reveal" style="transition-delay:0.3s">
          <div class="card-icon-light"><i class="fas fa-handshake"></i><span>5. Integração e boas-vindas</span></div>
          <p class="card-text-light">Antes do início das aulas, é feita uma integração com as crianças e famílias, incluindo dia de adaptação ou encontro inicial com professores.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="documentos-section">
    <div class="documentos-container">
      <h2 class="documentos-title reveal">Documentos necessários:</h2>
      <div class="documentos-content-box reveal reveal-delay-1">
        <ul class="documento-list">
          <li><span class="lista-bullet"></span>Cópia da Certidão de Nascimento da criança.</li>
          <li><span class="lista-bullet"></span>Cópia do Bilhete de Identidade do encarregado</li>
          <li><span class="lista-bullet"></span>2 Fotografias tipo passe</li>
          <li><span class="lista-bullet"></span>Comprovativo de residência</li>
          <li><span class="lista-bullet"></span>Ficha de inscrição, que pode ser entregue presencialmente na escola ou enviada online através do nosso formulário</li>
        </ul>
      </div>
    </div>
  </section>

  <section class="section-table">
    <div class="container-table">
      <h2 class="title-table reveal">Idades e Níveis</h2>
      <div class="tabela reveal reveal-delay-1">
        <table class="table-classica">
          <thead>
            <tr class="linha-classica">
              <th>Nível</th>
              <th>Faixa Etária</th>
              <th>Período</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th class="titulo-classico">Mini Kids</th>
              <td>2 a 3 anos</td>
              <td>Meio período</td>
            </tr>
            <tr>
              <th class="titulo-classico">Júnior</th>
              <td class="highlight-fee-classic">4 a 5 anos</td>
              <td class="highlight-fee-classic">Integral</td>
            </tr>
            <tr>
              <th class="titulo-classico">Pré-Escolar</th>
              <td class="highlight-fee-classic">5 a 6 anos</td>
              <td class="highlight-fee-classic">Integral</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <section class="matricula-section" id="fazer-matricula">
    <div class="matricula-container">
      <div class="matricula-header reveal">
        <h2>Formulário de Matrícula</h2>
        <p>Preencha os dados abaixo para matricular a sua criança.</p>
      </div>
      <form
    class="matricula-form"
    action="api/processar_matricula.php"
    method="POST"
    enctype="multipart/form-data">
        <div class="form-group-box reveal reveal-delay-1">
          <h3 class="group-title">Informações da Criança</h3>
          <div class="form-row">
            <div class="form-field full-width">
              <label for="nomeCrianca">Nome completo</label>
              <input
    type="text"
    id="nomeCrianca"
    name="nome_crianca"
    placeholder="Digite o nome completo da criança"
    required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-field half-width">
              <label for="dataNascimento">
    Data de nascimento
</label>

<input
    type="date"
    id="dataNascimento"
    name="data_nascimento"
    required>
            </div>
            <div class="form-field half-width">
              <label for="sexo">Sexo</label>
              <select
    id="sexo"
    name="sexo"
    required>
                <option value="Masculino">Masculino</option>
                <option value="Feminino">Feminino</option>
              </select>
            </div>
            <div class="form-field half-width">
              <label for="turmaDesejada">Turma desejada</label>
              <select
    id="turmaDesejada"
    name="turma_id"
    required>

    <option value="">
        Selecione uma turma
    </option>
   <?php foreach($turmas as $turma): ?>
                  <option value="<?= $turma['id'] ?>">
                    <?= htmlspecialchars($turma['nome']) ?> - <?= htmlspecialchars($turma['faixa_etaria']) ?>
                  </option>
                <?php endforeach; ?>

</select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-field full-width">
              <label for="infoSaude">Informações sobre saúde</label>
             <textarea
    id="infoSaude"
    name="info_saude"
    rows="3"
    placeholder="Alergias, medicamentos, condições especiais..."></textarea>
            </div>
          </div>
        </div>

        <div class="form-group-box reveal reveal-delay-2">
          <h3 class="group-title">Informações dos Pais / Encarregados</h3>
          <div class="form-row">
            <div class="form-field half-width">
              <label for="nomeResponsavel">Nome completo</label>
              <input type="text" id="nomeResponsavel" name="nome_responsavel" placeholder="Digite o seu nome completo" required>
            </div>
            <div class="form-field half-width">
              <label for="telefone">Telefone</label>
              <input type="tel" id="telefone" name="telefone" placeholder="+258 XXXXXXXXX" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-field half-width">
              <label for="emailResponsavel">E-mail</label>
              <input type="email" id="emailResponsavel" name="email" placeholder="exemplo@email.com" required>
            </div>
            <div class="form-field half-width">
              <label for="relacaoCrianca">Relação com a criança</label>
              <select id="relacaoCrianca" name="relacao" required>
                <option value="Pai">Pai</option>
                <option value="Mae">Mãe</option>
                <option value="Outro">Outro</option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-field full-width">
              <label for="endereco">Endereço completo</label>
              <input type="text" id="endereco" name="endereco" placeholder="Rua, número, bairro, cidade" required>
            </div>
          </div>
        </div>

        <div class="form-group-box reveal reveal-delay-3">
          <h3 class="group-title">Documentos / Confirmações</h3>
          <div class="form-documents-row">
            <div class="upload-card">
              <label for="certidao">Certidão de nascimento</label>
              <span class="upload-label">Escolher arquivo</span>
              <input type="file" id="certidao" name="certidao" class="hidden-file-input">
            </div>
            <div class="upload-card">
              <label for="biResponsavel">BI do responsável</label>
              <span class="upload-label">Escolher arquivo</span>
              <input type="file" id="biResponsavel" name="bi_responsavel" class="hidden-file-input">
            </div>
            <div class="upload-card">
              <label for="fotosTipoPasse">Fotos tipo passe</label>
              <span class="upload-label">Escolher arquivo</span>
              <input type="file" id="fotosTipoPasse" name="foto_passe" class="hidden-file-input">
            </div>
          </div>
          <div class="checkbox-container">
            <input type="checkbox" id="aceiteTermos" name="aceite_termos" required>
            <label for="aceiteTermos">Li e aceito os termos de matrícula e políticas da escola</label>
          </div>
        </div>

        <div class="form-group-box reveal reveal-delay-4">
          <h3 class="group-title">Preferências e Observações</h3>
          <div class="form-row">
            <div class="form-field half-width">
              <label>Período</label>
              <div class="radio-button-group">
                <input type="radio" id="meioPeriodo" name="periodo" value="Meio período" checked>
                <label for="meioPeriodo" class="radio-label">Meio período</label>
                <input type="radio" id="integral" name="periodo" value="Integral">
                <label for="integral" class="radio-label">Integral</label>
              </div>
            </div>
            <div class="form-field half-width">
              <label for="comoSoube">Como soube da escola?</label>
              <input type="text" id="comoSoube" name="como_soube" placeholder="Ex: Redes sociais, indicação...">
            </div>
          </div>
          <div class="form-row">
            <div class="form-field full-width">
              <label for="obsCrianca">Observações sobre a criança</label>
              <textarea id="obsCrianca" name="observacoes" rows="3" placeholder="Comportamentos, preferências, ou outra informação relevante..."></textarea>
            </div>
          </div>
        </div>

        <div class="form-actions reveal">
          <button type="reset" class="btn-reset">Limpar formulário</button>
          <button type="submit" class="btn-submit">Enviar matrícula</button>
        </div>
      </form>
    </div>
  </section>

  <section class="hero-tennis2">
    <div class="hero-content2">
      <h1>Estamos prontos para receber o seu pequeno com amor e dedicação!</h1>
      <p>As vagas são limitadas e podem acabar a qualquer momento. Matricule agora e garanta essa oportunidade única para seu filho.</p>
      <a href="contact.php" class="hero-btn2">Contacte-nos</a>
    </div>
  </section>

  <footer class="main-footer">
    <div class="footer-container">
      <div class="footer-column description-column">
        <p>Somos o melhor centro infantil de Moçambique, dedicado a oferecer uma educação de excelência, com amor, criatividade e cuidado para o crescimento das nossas crianças.</p>
      </div>
      <div class="footer-column">
        <h4>LINKS ÚTEIS</h4>
        <ul>
          <li><a href="index.php">Início</a></li>
          <li><a href="sobre.php">Sobre nós</a></li>
          <li><a href="programa.php">Programa Educacional</a></li>
          <li><a href="matriculas.php">Matrículas</a></li>
          <li><a href="mensalidades.php">Mensalidades</a></li>
        </ul>
      </div>
      <div class="footer-column">
        <h4>Kindergarten</h4>
        <ul>
          <li>Rua das Flores 111</li>
          <li>Cidade Maputo, Moçambique</li>
        </ul>
      </div>
      <div class="footer-column">
        <h4>Contactos</h4>
        <ul>
          <li><a href="mailto:jaimenhacumba@gmail.com">jaimenhacumba@gmail.com</a></li>
          <li><a href="tel:+258847852208">+258 847 852 208</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p class="copyright">© 2025 Kindergarten. Todos os Direitos Reservados.</p>
      <a href="#">Desenvolvido por Jaime Fernando Nhacumba</a>
    </div>
  </footer>

  <script>
    // Navbar scroll
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
      navbar.classList.toggle('scrolled', window.scrollY > 40);
    });

    // Hamburger
    const hamburger = document.getElementById('hamburger');
    const navLinks  = document.getElementById('navLinks');
    hamburger.addEventListener('click', () => {
      hamburger.classList.toggle('open');
      navLinks.classList.toggle('mobile-open');
    });
    navLinks.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', () => {
        hamburger.classList.remove('open');
        navLinks.classList.remove('mobile-open');
      });
    });

    // Scroll reveal
    const allReveals = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('visible');
          observer.unobserve(e.target);
        }
      });
    }, { threshold: 0.1 });
    allReveals.forEach(el => observer.observe(el));

    // File upload labels
    document.querySelectorAll('.upload-card').forEach(card => {
      const input = card.querySelector('input[type="file"]');
      const label = card.querySelector('.upload-label');
      card.addEventListener('click', () => input.click());
      input.addEventListener('change', () => {
        if (input.files.length > 0) {
          label.textContent = input.files[0].name;
          label.style.color = '#28a745';
        }
      });
    });
  </script>
</body>
</html>