<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sobre Nós — Kindergarten</title>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
   <link
  href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css"
  rel="stylesheet"
/>
  <style>
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

    :root {
      --red:        #ff4d4d;
      --red-dark:   #e63535;
      --yellow:     #ffd93d;
      --teal:       #4ecdc4;
      --purple:     #a29bfe;
      --green:      #55efc4;
      --orange:     #fd9644;
      --dark:       #1a1a2e;
      --dark2:      #16213e;
      --text:       #333;
      --text-light: #666;
      --bg:         #fffaf5;
      --white:      #ffffff;
      --radius:     16px;
      --radius-lg:  28px;
      --shadow:     0 8px 32px rgba(0,0,0,0.10);
      --shadow-lg:  0 20px 60px rgba(0,0,0,0.14);
      --transition: 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }

    html { scroll-behavior: smooth; font-size: 16px; }
    body {
       font-family: "Poppins", sans-serif;
      background: var(--bg);
      color: var(--text);
      overflow-x: hidden;
    }
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
    .nav-links {
      display: flex;
      gap: 4px;
    }
    .nav-links a {
      font-weight: 700;
      font-size: 0.9rem;
      color: var(--text-light);
      padding: 8px 14px;
      border-radius: 999px;
      transition: var(--transition);
    }
    .nav-links a:hover,
    .nav-links a.active {
      color: var(--red);
      background: rgba(255,77,77,0.08);
    }
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

    .hero {
      position: relative;
      height: 85vh;
      min-height: 500px;
      background:linear-gradient(rgba(0, 0, 0, 0.35), rgba(0, 0, 0, 0.45));
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }
    .hero::before {
      content: '';
      position: absolute;
      inset: 0;
    
       background-image: linear-gradient(rgba(0, 0, 0, 0.35), rgba(0, 0, 0, 0.45)), 
                             url("../imagens/Connecting\ Children’s\ Vocabulary.jpeg");
            background-size: cover;
            background-position: center;
      opacity: 2;
    }
    .hero-overlay {
      position: absolute;
      inset: 0;
    }
    .hero-content {
      position: relative;
      z-index: 2;
      text-align: center;
      padding: 0 24px;
      animation: heroFadeIn 1s ease both;
    }
    @keyframes heroFadeIn {
      from { opacity: 0; transform: translateY(40px); }
      to { opacity: 1; transform: none; }
    }
    .hero-tag {
      display: inline-block;
      background: rgba(255,217,61,0.2);
      border: 1px solid rgba(255,217,61,0.4);
      color: var(--yellow);
      font-weight: 800;
      font-size: 0.78rem;
      letter-spacing: 2px;
      text-transform: uppercase;
      padding: 6px 18px;
      border-radius: 999px;
      margin-bottom: 20px;
    }
    .hero h1 {
      font-family: 'Fredoka One', cursive;
      font-size: clamp(2.5rem, 6vw, 4.5rem);
      color: #fff;
      line-height: 1.15;
      margin-bottom: 20px;
    }
    .hero h1 span { color: var(--yellow); }
    .hero p {
      color: rgba(255,255,255,0.8);
      font-size: 1.1rem;
      max-width: 560px;
      margin: 0 auto 36px;
      line-height: 1.7;
    }
    .hero-scroll {
      position: absolute;
      bottom: 32px;
      left: 50%;
      transform: translateX(-50%);
      color: rgba(255,255,255,0.6);
      font-size: 0.8rem;
      letter-spacing: 1px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      animation: bounce 2s infinite;
    }
    @keyframes bounce {
      0%, 100% { transform: translateX(-50%) translateY(0); }
      50% { transform: translateX(-50%) translateY(8px); }
    }
    .hero-scroll i { font-size: 1.2rem; }

    .stats-bar {
      background: var(--white);
      box-shadow: var(--shadow);
      padding: 32px 0;
      position: relative;
      z-index: 10;
    }
    .stats-grid {
      max-width: 900px;
      margin: 0 auto;
      padding: 0 24px;
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 24px;
      text-align: center;
    }
    .stat-item {
      padding: 16px;
      border-radius: var(--radius);
      transition: var(--transition);
    }
    .stat-item:hover { background: #fff9f0; transform: translateY(-4px); }
    .stat-num {
      font-family: 'Fredoka One', cursive;
      font-size: 2.4rem;
      color: var(--red);
      line-height: 1;
      margin-bottom: 6px;
    }
    .stat-label {
      font-size: 0.85rem;
      color: var(--text-light);
      font-weight: 700;
    }
    .section-pad { padding: 100px 0; }
    .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
    .section-tag {
      display: inline-block;
      background: #fff3e0;
      color: var(--orange);
      font-weight: 800;
      font-size: 0.78rem;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      padding: 6px 16px;
      border-radius: 999px;
      margin-bottom: 12px;
    }
    .section-header {
      text-align: center;
      margin-bottom: 64px;
    }
    .section-header h2 {
      font-size: clamp(2rem, 4vw, 3rem);
      color: var(--dark);
      margin: 8px 0 14px;
    }
    .section-header p {
      color: var(--text-light);
      max-width: 560px;
      margin: 0 auto;
      line-height: 1.7;
      font-size: 1.05rem;
    }
    .quem-somos {
      background: var(--white);
    }
    .quem-somos-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 80px;
      align-items: center;
    }
    .quem-somos-text .big-label {
      font-family: 'Fredoka One', cursive;
      font-size: clamp(3rem, 5vw, 5rem);
      color: var(--dark);
      line-height: 1;
      margin-bottom: 24px;
    }
    .quem-somos-text .big-label span { color: var(--red); }
    .quem-somos-text p {
      font-size: 1.08rem;
      color: var(--text-light);
      line-height: 1.8;
      margin-bottom: 16px;
    }
    .quem-somos-img-wrap {
      position: relative;
    }
    .quem-somos-img-wrap img {
      width: 100%;
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-lg);
      transition: transform 0.5s ease;
    }
    .quem-somos-img-wrap:hover img { transform: scale(1.02); }
    .img-badge {
      position: absolute;
      bottom: -20px;
      left: -20px;
      background: var(--yellow);
      border-radius: var(--radius);
      padding: 16px 20px;
      box-shadow: var(--shadow);
      display: flex;
      align-items: center;
      gap: 12px;
      font-weight: 800;
      color: var(--dark);
    }
    .img-badge .badge-icon { font-size: 1.6rem; }
    .img-badge .badge-text { font-size: 0.85rem; line-height: 1.3; }
    .diretrizes { background: #f8f4ff; }
    .dir-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 28px;
    }
    .dir-card {
      background: var(--white);
      border-radius: var(--radius-lg);
      padding: 36px 28px;
      box-shadow: var(--shadow);
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }
    .dir-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 4px;
    }
    .dir-card:hover {
      transform: translateY(-8px);
      box-shadow: var(--shadow-lg);
    }
    .dir-icon {
      width: 56px; height: 56px;
      border-radius: 16px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.6rem;
      margin-bottom: 20px;
      background: var(--icon-bg, #fff3f3);
    }
    .dir-card h3 {
      font-size: 1.4rem;
      color: var(--dark);
      margin-bottom: 12px;
    }
    .dir-card p {
      color: var(--text-light);
      line-height: 1.7;
      font-size: 0.97rem;
    }
    .visao { background: var(--white); }
    .split-section {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 80px;
      align-items: center;
      margin-bottom: 80px;
    }
    .split-section:last-child { margin-bottom: 0; }
    .split-section.reverse { direction: rtl; }
    .split-section.reverse > * { direction: ltr; }
    .split-img {
      border-radius: var(--radius-lg);
      overflow: hidden;
      box-shadow: var(--shadow-lg);
    }
    .split-img img {
      width: 100%;
      height: 380px;
      object-fit: cover;
      transition: transform 0.6s ease;
    }
    .split-img:hover img { transform: scale(1.05); }
    .split-text .section-tag { margin-bottom: 12px; }
    .split-text h2 {
      font-size: clamp(1.8rem, 3vw, 2.4rem);
      color: var(--dark);
      margin-bottom: 16px;
      line-height: 1.2;
    }
    .split-text p {
      color: var(--text-light);
      line-height: 1.8;
      font-size: 1.05rem;
      margin-bottom: 20px;
    }
    .chave { background: linear-gradient(135deg, var(--dark) 0%, var(--dark2) 100%); }
    .chave .section-header h2 { color: var(--white); }
    .chave .section-header p { color: rgba(255,255,255,0.7); }
    .chave .section-tag { background: rgba(255,217,61,0.15); color: var(--yellow); }
    .chave-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
    }
    .chave-item {
      background: rgba(255,255,255,0.07);
      border: 1px solid rgba(255,255,255,0.12);
      border-radius: var(--radius);
      padding: 28px;
      display: flex;
      align-items: center;
      gap: 16px;
      transition: var(--transition);
      cursor: default;
    }
    .chave-item:hover {
      background: rgba(255,255,255,0.12);
      transform: translateX(8px);
      border-color: rgba(255,217,61,0.4);
    }
    .chave-num {
      font-size: 2rem;
      color: var(--yellow);
      opacity: 0.5;
      min-width: 44px;
    }
    .chave-label {
      font-weight: 800;
      color: var(--white);
      font-size: 1.05rem;
    }
    .chave-desc {
      font-size: 0.85rem;
      color: rgba(255,255,255,0.6);
      margin-top: 4px;
    }
    .valores { background: #fffaf5; }
    .val-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 24px;
    }
    .val-card {
      background: var(--white);
      border-radius: var(--radius-lg);
      padding: 32px 20px;
      text-align: center;
      box-shadow: var(--shadow);
      transition: var(--transition);
      border: 2px solid transparent;
    }
    .val-card:hover {
      transform: translateY(-6px);
      border-color: var(--red);
      box-shadow: 0 16px 48px rgba(255,77,77,0.15);
    }
    .val-emoji { font-size: 2.5rem; margin-bottom: 14px; }
    .val-card h4 {
      font-size: 1.2rem;
      color: var(--dark);
      margin-bottom: 8px;
    }
    .val-card p {
      font-size: 0.9rem;
      color: var(--text-light);
      line-height: 1.6;
    }
    .cta-section {
      background: linear-gradient(rgba(0, 0, 0, 0.35), rgba(0, 0, 0, 0.45)), url("../imagens/img5.jpeg") center/cover no-repeat ;
      padding: 80px 0;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .cta-section::before {
      content: '🎓';
      position: absolute;
      font-size: 18rem;
      opacity: 0.06;
      top: -40px; right: -40px;
      line-height: 1;
    }
    .cta-section h2 {
      font-size: clamp(2rem, 4vw, 3rem);
      color: var(--white);
      margin-bottom: 14px;
    }
    .cta-section p {
      color: rgba(255,255,255,0.9);
      max-width: 500px;
      margin: 0 auto 36px;
      font-size: 1.05rem;
      line-height: 1.7;
    }
    .btn-white {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: var(--white);
      color: var(--red);
      font-weight: 800;
      padding: 16px 36px;
      border-radius: 999px;
      transition: var(--transition);
      font-size: 1rem;
      box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    }
    .btn-white:hover {
      transform: translateY(-4px);
      box-shadow: 0 16px 40px rgba(0,0,0,0.2);
    }
    .contact-section {
      background: var(--white);
      padding: 100px 0;
    }
    .contact-grid {
      display: grid;
      grid-template-columns: 1fr 1.2fr;
      gap: 64px;
      align-items: start;
    }
    .contact-info h2 {
      font-size: 2.4rem;
      color: var(--dark);
      margin-bottom: 12px;
    }
    .contact-info p {
      color: var(--text-light);
      line-height: 1.7;
      margin-bottom: 36px;
    }
    .contact-item {
      display: flex;
      align-items: flex-start;
      gap: 16px;
      margin-bottom: 24px;
    }
    .contact-icon {
      width: 44px; height: 44px;
      background: #fff3f3;
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      color: var(--red);
      font-size: 1rem;
      flex-shrink: 0;
    }
    .contact-item-text strong { display: block; color: var(--dark); margin-bottom: 2px; }
    .contact-item-text span { color: var(--text-light); font-size: 0.9rem; }
    .contact-map {
      border-radius: var(--radius-lg);
      overflow: hidden;
      box-shadow: var(--shadow-lg);
      height: 320px;
      margin-bottom: 24px;
    }
    .contact-map iframe { width: 100%; height: 100%; border: none; }
    .contact-form { background: var(--bg); padding: 36px; border-radius: var(--radius-lg); }
    .contact-form h3 {
      font-size: 1.6rem;
      color: var(--dark);
      margin-bottom: 24px;
    }
    .form-group { margin-bottom: 20px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-group input,
    .form-group textarea {
      width: 100%;
      background: var(--white);
      border: 2px solid #e8e8e8;
      border-radius: 12px;
      padding: 14px 16px;
      font-family: 'Nunito', sans-serif;
      font-size: 0.95rem;
      color: var(--text);
      transition: border-color 0.25s;
      outline: none;
    }
    .form-group input:focus,
    .form-group textarea:focus { border-color: var(--red); }
    .form-group textarea { resize: vertical; min-height: 110px; }
    .btn-primary {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: var(--red);
      color: #fff;
      font-weight: 800;
      font-size: 1rem;
      padding: 14px 30px;
      border-radius: 999px;
      border: none;
      cursor: pointer;
      transition: var(--transition);
      box-shadow: 0 6px 24px rgba(255,77,77,0.35);
      width: 100%;
      justify-content: center;
    }
    .btn-primary:hover {
      background: var(--red-dark);
      transform: translateY(-2px);
    }
    .main-footer {
      background: var(--dark);
      color: rgba(255,255,255,0.7);
      padding: 64px 0 0;
    }
    .footer-grid {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1fr;
      gap: 40px;
      padding-bottom: 48px;
    }
    .footer-brand .logo { color: var(--white); margin-bottom: 16px; display: inline-flex; }
    .footer-brand p { font-size: 0.9rem; line-height: 1.7; max-width: 260px; }
    .footer-col h4 {
      color: var(--white);
      font-weight: 800;
      font-size: 0.85rem;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-bottom: 20px;
    }
    .footer-col ul { list-style: none; }
    .footer-col li { margin-bottom: 10px; }
    .footer-col a {
      color: rgba(255,255,255,0.6);
      font-size: 0.9rem;
      transition: color 0.25s;
    }
    .footer-col a:hover { color: var(--white); }
    .footer-bottom {
      border-top: 1px solid rgba(255,255,255,0.08);
      padding: 20px 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.82rem;
      color: rgba(255,255,255,0.4);
    }
    .reveal {
      opacity: 0;
      transform: translateY(36px);
      transition: opacity 0.65s ease, transform 0.65s ease;
    }
    .reveal.visible { opacity: 1; transform: none; }
    .reveal-delay-1 { transition-delay: 0.1s; }
    .reveal-delay-2 { transition-delay: 0.2s; }
    .reveal-delay-3 { transition-delay: 0.3s; }

    @media (max-width: 1024px) {
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
      .dir-grid { grid-template-columns: 1fr 1fr; }
      .val-grid { grid-template-columns: 1fr 1fr; }
      .footer-grid { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 768px) {
      .nav-links { display: none; }
      .hamburger { display: flex; }
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

      .quem-somos-grid,
      .split-section,
      .contact-grid { grid-template-columns: 1fr; gap: 40px; }
      .split-section.reverse { direction: ltr; }
      .chave-grid { grid-template-columns: 1fr 1fr; }
      .dir-grid { grid-template-columns: 1fr; }
      .val-grid { grid-template-columns: 1fr 1fr; }
      .footer-grid { grid-template-columns: 1fr; }
      .img-badge { left: 10px; bottom: -10px; }
      .form-row { grid-template-columns: 1fr; }
      .stats-grid { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 480px) {
      .chave-grid { grid-template-columns: 1fr; }
      .val-grid { grid-template-columns: 1fr; }
      .footer-bottom { flex-direction: column; gap: 8px; text-align: center; }
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
        <a href="sobre.php" class="active">Sobre nós</a>
        <a href="programa.php">Programa</a>
        <a href="matriculas.php">Matrículas</a>
        <a href="mensalidades.php">Mensalidades</a>
      </nav>
      <div class="nav-actions">
        <a href="contact.php" class="btn-nav">Entrar em contacto</a>
        <button class="hamburger" id="hamburger" aria-label="Menu">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>
  </header>

  <main>

    <section class="hero">
      <div class="hero-overlay"></div>
      <div class="hero-content">
        <h1>Onde cada <span>criança</span><br>brilha à sua maneira</h1>
        <p>Há anos cultivando mentes curiosas, corações empáticos e sonhos grandes em Moçambique.</p>
        <a href="#quem-somos" class="btn-white" style="display:inline-flex;">
          Saber mais <i class="fas fa-arrow-down"></i>
        </a>
      </div>
      <div class="hero-scroll">
        <span>Deslize</span>
        <i class="fas fa-chevron-down"></i>
      </div>
    </section>

    <section class="stats-bar">
      <div class="stats-grid">
        <div class="stat-item reveal">
          <div class="stat-num">500+</div>
          <div class="stat-label">Alunos Formados</div>
        </div>
        <div class="stat-item reveal reveal-delay-1">
          <div class="stat-num">15+</div>
          <div class="stat-label">Anos de Experiência</div>
        </div>
        <div class="stat-item reveal reveal-delay-2">
          <div class="stat-num">40+</div>
          <div class="stat-label">Educadores Certificados</div>
        </div>
        <div class="stat-item reveal reveal-delay-3">
          <div class="stat-num">98%</div>
          <div class="stat-label">Satisfação dos Pais</div>
        </div>
      </div>
    </section>

  
    <section class="quem-somos section-pad" id="quem-somos">
      <div class="container">
        <div class="quem-somos-grid">
          <div class="quem-somos-text reveal">
            
            <div class="big-label">Quem <span>Somos</span></div>
            <p>A Kindergarten oferece jornadas de aprendizagem para que cada estudante desenvolva múltiplos talentos por meio da educação integral.</p>
            <p>Esta, por sua vez, engloba os conceitos fundamentais das diferentes áreas do saber, enfatizando as conexões globais, a sustentabilidade, e a inteligência socioemocional e a educação digital.</p>
            <p>Acreditamos que o ambiente educacional é fundamental para o desenvolvimento pleno da criança — por isso criamos espaços seguros, acolhedores e estimulantes.</p>
          </div>
          <div class="quem-somos-img-wrap reveal reveal-delay-2">
            <img src="../imagens/img1.jpg" alt="Crianças aprendendo" loading="lazy">
            <div class="img-badge">
              <span class="badge-icon"><i class="ri-award-line"></i></span>
              <div class="badge-text">Melhor Centro<br>Infantil de Moçambique</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="diretrizes section-pad">
      <div class="container">
        <div class="section-header reveal">
         
          <h2>Nossas Diretrizes</h2>
          <p>Três pilares que orientam tudo o que fazemos no dia a dia do nosso centro educacional.</p>
        </div>
        <div class="dir-grid">
          <div class="dir-card reveal" >
            
            <h3>Missão</h3>
            <p>Fornecer um programa de aprendizado focado no aluno em um ambiente seguro e com todos os recursos que estimulam jovens e crianças a construírem um futuro melhor e que promovam a paixão pelo aprendizado ao longo de toda vida.</p>
          </div>
          <div class="dir-card reveal reveal-delay-1">
            <h3>Visão</h3>
            <p>Fornecer Educação Infantil, Ensino Fundamental e Médio de alta qualidade com base no programa moçambicano e nas melhores práticas que atendam às expectativas dos pais e da comunidade global.</p>
          </div>
          <div class="dir-card reveal reveal-delay-2">
            <h3>Valores</h3>
            <p>Autoconhecimento, empatia, excelência, autonomia, comprometimento e criatividade — estes são os valores que cultivamos em cada aluno, preparando-os para a vida em sociedade.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="visao section-pad">
      <div class="container">
        <div class="split-section">
          <div class="split-img reveal">
            <img src="../imagens/img5.jpeg" alt="Crianças aprendendo" loading="lazy">
          </div>
          <div class="split-text reveal reveal-delay-1">
            
            <h2>Uma educação que vai além da sala de aula</h2>
            <p>Nosso currículo integra o programa moçambicano com as melhores práticas pedagógicas internacionais, garantindo que cada aluno esteja preparado para os desafios do século XXI.</p>
            <p>Utilizamos metodologias ativas, aprendizagem baseada em projetos e tecnologia educacional para tornar o aprendizado mais envolvente e significativo.</p>
          </div>
        </div>
        <div class="split-section reverse">
          <div class="split-img reveal reveal-delay-1">
            <img src="../imagens/Connecting Children’s Vocabulary.jpeg" alt="Atividades em grupo" loading="lazy">
          </div>
          <div class="split-text reveal">
            
            <h2>Um lugar onde a criança se sente em casa</h2>
            <p>Criamos um ambiente acolhedor, seguro e estimulante onde as crianças se sentem à vontade para explorar, errar e crescer. Cada espaço foi projetado pensando no bem-estar e no desenvolvimento integral.</p>
            <p>Nossa equipe de educadores certificados está sempre presente para guiar, apoiar e celebrar cada pequena conquista.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="chave section-pad">
      <div class="container">
        <div class="section-header reveal">
          
          <h2>Chave para o Futuro</h2>
          <p>Seis competências essenciais que desenvolvemos em cada aluno para prepará-los para uma vida plena e significativa.</p>
        </div>
        <div class="chave-grid">
          <div class="chave-item reveal">
            <div class="chave-num">01</div>
            <div>
              <div class="chave-label">Autoconhecimento</div>
              <div class="chave-desc">Entender a si mesmo é o primeiro passo</div>
            </div>
          </div>
          <div class="chave-item reveal reveal-delay-1">
            <div class="chave-num">02</div>
            <div>
              <div class="chave-label">Empatia</div>
              <div class="chave-desc">Respeitar e compreender o outro</div>
            </div>
          </div>
          <div class="chave-item reveal reveal-delay-2">
            <div class="chave-num">03</div>
            <div>
              <div class="chave-label">Excelência</div>
              <div class="chave-desc">Dar sempre o melhor de si</div>
            </div>
          </div>
          <div class="chave-item reveal">
            <div class="chave-num">04</div>
            <div>
              <div class="chave-label">Autonomia</div>
              <div class="chave-desc">Pensar e agir com independência</div>
            </div>
          </div>
          <div class="chave-item reveal reveal-delay-1">
            <div class="chave-num">05</div>
            <div>
              <div class="chave-label">Comprometimento</div>
              <div class="chave-desc">Responsabilidade com o que se propõe</div>
            </div>
          </div>
          <div class="chave-item reveal reveal-delay-2">
            <div class="chave-num">06</div>
            <div>
              <div class="chave-label">Criatividade</div>
              <div class="chave-desc">Inovar e imaginar novos caminhos</div>
            </div>
          </div>
        </div>
      </div>
    </section>


    <section class="valores section-pad">
      <div class="container">
        <div class="section-header reveal">
          
          <h2>Nossos Diferenciais</h2>
          <p>O que faz a Kindergarten ser especial para tantas famílias moçambicanas.</p>
        </div>
        <div class="val-grid">
          <div class="val-card reveal">
            <h4>Educação Integral</h4>
            <p>Desenvolvemos o ser humano em todas as suas dimensões: cognitiva, emocional, social e física.</p>
          </div>
          <div class="val-card reveal reveal-delay-1">
            <h4>Professores Qualificados</h4>
            <p>Nossa equipe é formada por educadores apaixonados e continuamente capacitados.</p>
          </div>
          <div class="val-card reveal reveal-delay-2">
            <h4>Visão Global</h4>
            <p>Preparamos os alunos para serem cidadãos do mundo, conscientes e conectados.</p>
          </div>
          <div class="val-card reveal reveal-delay-3">
            <h4>Parceria com Famílias</h4>
            <p>Acreditamos que educação é uma parceria entre escola e família, caminhando juntos.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="cta-section reveal">
      <div class="container">
        <h2>Pronto para fazer parte da nossa família?</h2>
        <p>Venha conhecer o nosso espaço e descubra por que somos a escolha de tantas famílias em Moçambique.</p>
        <a href="matriculas.php" class="btn-white">
          Fazer matrícula agora <i class="fas fa-arrow-right"></i>
        </a>
      </div>
    </section>

    <section class="contact-section">
      <div class="container">
        <div class="contact-grid">
          <div>
            <div class="reveal">
              
              <div class="contact-info">
                <h2>Estamos aqui para ajudar</h2>
                <p>Tem dúvidas sobre as nossas turmas, horários ou mensalidades? Entre em contacto — será um prazer atendê-lo.</p>
              </div>
            </div>
            <div class="reveal reveal-delay-1">
              <div class="contact-item">
                <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div class="contact-item-text">
                  <strong>Endereço</strong>
                  <span>Rua das Flores 111, Cidade de Maputo, Moçambique</span>
                </div>
              </div>
              <div class="contact-item">
                <div class="contact-icon"><i class="fas fa-phone"></i></div>
                <div class="contact-item-text">
                  <strong>Telefone</strong>
                  <span>+258 84 785 2208</span>
                </div>
              </div>
              <div class="contact-item">
                <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                <div class="contact-item-text">
                  <strong>E-mail</strong>
                  <span>jaimenhacumba@gmail.com</span>
                </div>
              </div>
              <div class="contact-map">
                <iframe src="https://www.google.com/maps?q=Maputo,+Mozambique&output=embed" loading="lazy"></iframe>
              </div>
            </div>
          </div>
          <div class="contact-form reveal reveal-delay-2">
    <h3>Envie uma mensagem</h3>
    <form id="formContato" method="POST" action="api/enviar_contato.php">
        <div class="form-group">
            <input type="text" name="assunto" placeholder="Assunto (opcional)">
        </div>
        <div class="form-group">
            <textarea name="mensagem" placeholder="Escreva a sua mensagem aqui..." required></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <input type="text" name="nome" placeholder="O seu nome *" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="O seu e-mail *" required>
            </div>
        </div>
        <button class="btn-primary" type="submit">
            <i class="fas fa-paper-plane"></i> Enviar mensagem
        </button>
    </form>
    <div id="contatoMensagem" style="margin-top: 15px;"></div>
</div>
        </div>
      </div>
    </section>

  </main>
  <footer class="main-footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-brand">
          <a href="index.php" class="logo">
            <div style="width:36px;height:36px;background:linear-gradient(135deg,#ffd93d,#ff6b6b);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">🎓</div>
            <span style="font-size:1.3rem;color:#fff;">Kindergarten</span>
          </a>
          <p style="margin-top:12px;">Somos o melhor centro infantil de Moçambique, dedicado a oferecer uma educação de excelência, com amor, criatividade e cuidado.</p>
        </div>
        <div class="footer-col">
          <h4>Links Úteis</h4>
          <ul>
            <li><a href="index.php">Início</a></li>
            <li><a href="sobre.php">Sobre nós</a></li>
            <li><a href="programa.php">Programa Educacional</a></li>
            <li><a href="matriculas.php">Matrículas</a></li>
            <li><a href="mensalidades.php">Mensalidades</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Localização</h4>
          <ul>
            <li>Rua das Flores 111</li>
            <li>Cidade de Maputo</li>
            <li>Moçambique</li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Contactos</h4>
          <ul>
            <li><a href="mailto:jaimenhacumba@gmail.com">jaimenhacumba@gmail.com</a></li>
            <li><a href="tel:+258847852208">+258 84 785 2208</a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <span>© 2025 Kindergarten. Todos os direitos reservados.</span>
        <span>Desenvolvido por Jaime Fernando Nhacumba</span>
      </div>
    </div>
  </footer>

  <script>

    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
      navbar.classList.toggle('scrolled', window.scrollY > 40);
    });


    const hamburger = document.getElementById('hamburger');
    const navLinks = document.getElementById('navLinks');
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

    const reveals = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('visible');
          observer.unobserve(e.target);
        }
      });
    }, { threshold: 0.12 });
    reveals.forEach(el => observer.observe(el));

    document.querySelectorAll('a[href^="#"]').forEach(a => {
      a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (target) {
          e.preventDefault();
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });

document.getElementById('formContato').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = this.querySelector('button[type="submit"]');
    const btnOriginalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
    btn.disabled = true;
    
    const msgDiv = document.getElementById('contatoMensagem');
    msgDiv.innerHTML = '';
    
    const formData = new FormData(this);
    

    fetch('api/enviar_contato.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            msgDiv.innerHTML = '<div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; border: 1px solid #c3e6cb;">' +
                               '<i class="fas fa-check-circle"></i> ' + data.message + '</div>';
            document.getElementById('formContato').reset();
        } else {
            msgDiv.innerHTML = '<div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; border: 1px solid #f5c6cb;">' +
                               '<i class="fas fa-exclamation-triangle"></i> ' + data.message + '</div>';
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        msgDiv.innerHTML = '<div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; border: 1px solid #f5c6cb;">' +
                           '<i class="fas fa-exclamation-triangle"></i> Erro ao enviar mensagem. Tente novamente mais tarde.</div>';
    })
    .finally(() => {
        btn.innerHTML = btnOriginalText;
        btn.disabled = false;
        
        setTimeout(() => {
            if (msgDiv.innerHTML) {
                msgDiv.style.opacity = '0';
                setTimeout(() => { msgDiv.innerHTML = ''; msgDiv.style.opacity = '1'; }, 500);
            }
        }, 5000);
    });
});
  </script>
</body>
</html>