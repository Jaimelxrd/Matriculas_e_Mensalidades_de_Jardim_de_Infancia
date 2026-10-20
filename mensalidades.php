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
  <title>Mensalidades — Kindergarten</title>
  <style>
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

    :root {
      --red:       #ff4d4d;
      --red-dark:  #e63535;
      --yellow:    #ffd93d;
      --orange:    #fd9644;
      --teal:      #4ecdc4;
      --green:     #55efc4;
      --dark:      #1a1a2e;
      --dark2:     #16213e;
      --text:      #333;
      --text-light:#666;
      --bg:        #fffaf5;
      --white:     #ffffff;
      --transition:0.35s cubic-bezier(0.4,0,0.2,1);
      --shadow:    0 8px 32px rgba(0,0,0,0.10);
      --shadow-lg: 0 20px 60px rgba(0,0,0,0.14);
      --radius:    16px;
      --radius-lg: 28px;
    }

    html { scroll-behavior: smooth; }
    body { font-family: "Poppins", sans-serif; background: var(--bg); color: var(--text); overflow-x: hidden; }
    a { text-decoration: none; color: inherit; }
    img { display: block; max-width: 100%; }

    header {
      position: fixed; top: 0; left: 0; right: 0;
      z-index: 1000; transition: var(--transition); padding: 10px 0;
    }
    header.scrolled {
      background: rgba(255,255,255,0.96);
      backdrop-filter: blur(14px);
      box-shadow: 0 4px 28px rgba(0,0,0,0.08);
      padding: 4px 0;
    }
    .nav-inner {
      max-width: 1200px; margin: 0 auto; padding: 14px 24px;
      display: flex; align-items: center; gap: 24px;
    }
    .logo {
      display: flex; align-items: center; gap: 10px;
       font-size: 1.5rem;
      color: var(--dark); margin-right: auto;
    }
   
    .logo:hover div { transform: rotate(-8deg) scale(1.1); }
    .nav-links { display: flex; gap: 4px; }
    .nav-links a {
      font-weight: 700; font-size: 0.9rem; color: var(--text-light);
      padding: 8px 14px; border-radius: 999px; transition: var(--transition);
    }
    .nav-links a:hover, .nav-links a.active { color: var(--red); background: rgba(255,77,77,0.08); }
    .nav-actions { display: flex; align-items: center; gap: 12px; }
    .btn-nav {
      background: var(--red); color: #fff; font-weight: 700; font-size: 0.9rem;
      padding: 11px 22px; border-radius: 999px; transition: var(--transition); white-space: nowrap;
    }
    .btn-nav:hover { background: var(--red-dark); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255,77,77,0.35); }
    .hamburger {
      display: none; flex-direction: column; gap: 5px;
      background: none; border: none; cursor: pointer; padding: 4px;
    }
    .hamburger span {
      display: block; width: 24px; height: 2.5px;
      background: var(--dark); border-radius: 3px; transition: var(--transition);
    }
    .hamburger.open span:nth-child(1) { transform: translateY(7.5px) rotate(45deg); }
    .hamburger.open span:nth-child(2) { opacity: 0; }
    .hamburger.open span:nth-child(3) { transform: translateY(-7.5px) rotate(-45deg); }
    .nav-links.mobile-open {
      display: flex; flex-direction: column; position: fixed; inset: 0;
      background: rgba(255,255,255,0.98); align-items: center; justify-content: center;
      gap: 12px; z-index: 999;
    }
    .nav-links.mobile-open a { font-size: 1.4rem; padding: 12px 32px; }

    .hero {
      position: relative; height: 72vh; min-height: 480px;
      display: flex; align-items: center; justify-content: center;
      overflow: hidden;
    }
    .hero::before {
      content: '';
      position: absolute; inset: 0;
      background: url("../imagens/img1.jpg") center/cover no-repeat;
      opacity: 1;
    }
    .hero-overlay {
      position: absolute; inset: 0;
     background: linear-gradient(to bottom, rgba(255,255,245,0.95) 0%, rgba(255,248,235,0.98) 100%);
    }
    .hero-inner {
      position: relative; z-index: 2; text-align: center;
      padding: 0 24px; max-width: 820px;
      animation: heroIn 0.9s ease both;
    }
    @keyframes heroIn {
      from { opacity: 0; transform: translateY(36px); }
      to   { opacity: 1; transform: none; }
    }
    .hero-tag {
      display: inline-block;
      background: rgba(255,217,61,0.18); border: 1px solid rgba(255,217,61,0.4);
      color: var(--yellow); font-weight: 700; font-size: 0.75rem;
      letter-spacing: 2px; text-transform: uppercase;
      padding: 7px 18px; border-radius: 999px; margin-bottom: 20px;
    }
    .hero h1 {
      font-size: clamp(2rem, 5vw, 3.6rem); font-weight: 800;
      color: #131313; line-height: 1.2; margin-bottom: 18px;
    }
    .hero h1 span { color: var(--yellow); }
    .hero p {
      color: rgba(10, 10, 10, 0.82); font-size: 1.05rem;
      max-width: 580px; margin: 0 auto 36px; line-height: 1.7;
    }
    .hero-btn {
      display: inline-flex; align-items: center; gap: 10px;
      background: var(--red); color: #fff; font-weight: 700;
      padding: 15px 34px; border-radius: 999px; font-size: 1rem;
      transition: var(--transition); box-shadow: 0 8px 24px rgba(255,77,77,0.4);
    }
    .hero-btn:hover { background: var(--red-dark); transform: translateY(-3px); box-shadow: 0 14px 32px rgba(255,77,77,0.5); }

    .reveal { opacity: 0; transform: translateY(32px); transition: opacity 0.6s ease, transform 0.6s ease; }
    .reveal.visible { opacity: 1; transform: none; }
    .reveal-left  { opacity: 0; transform: translateX(-32px); transition: opacity 0.65s ease, transform 0.65s ease; }
    .reveal-right { opacity: 0; transform: translateX(32px);  transition: opacity 0.65s ease, transform 0.65s ease; }
    .reveal-left.visible, .reveal-right.visible { opacity: 1; transform: none; }
    .reveal-delay-1 { transition-delay: 0.1s; }
    .reveal-delay-2 { transition-delay: 0.2s; }
    .reveal-delay-3 { transition-delay: 0.3s; }

    .section-pad { padding: 100px 0; }
    .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
    .section-tag {
      display: inline-block; background: #fff3e0; color: var(--orange);
      font-weight: 800; font-size: 0.78rem; letter-spacing: 1.5px;
      text-transform: uppercase; padding: 6px 16px; border-radius: 999px; margin-bottom: 12px;
    }
    .section-header { text-align: center; margin-bottom: 60px; }
    .section-header h2 {
      font-size: clamp(1.9rem, 3.5vw, 2.8rem);
      color: var(--dark); margin: 8px 0 14px; line-height: 1.2;
    }
    .section-header p { color: var(--text-light); max-width: 600px; margin: 0 auto; line-height: 1.7; font-size: 1.02rem; }

    .pricing { background: #f5f9fc; padding: 100px 0; }
    .pricing-table {
      display: grid; grid-template-columns: repeat(3, 1fr); gap: 28px;
    }
    .pricing-card {
      background: #fff; border-radius: var(--radius-lg); overflow: hidden;
      box-shadow: var(--shadow); text-align: center;
      transition: transform 0.35s ease, box-shadow 0.35s ease;
      display: flex; flex-direction: column; position: relative;
    }
    .pricing-card:hover { transform: translateY(-12px); box-shadow: var(--shadow-lg); }
    .pricing-card.featured {
      transform: scale(1.03);
    }
    .pricing-card.featured:hover { transform: scale(1.03) translateY(-10px); }
    .popular-badge {
      position: absolute; top: 16px; right: 16px;
      background: var(--red); color: #fff;
      font-size: 0.7rem; font-weight: 700; letter-spacing: 1px;
      text-transform: uppercase; padding: 5px 12px; border-radius: 999px;
    }
    .pricing-header {
      padding: 28px 20px;
      background: var(--dark);
      color: #fff;
    }
    .pricing-card.featured .pricing-header { background: var(--red); }
    .pricing-header .plan-icon { font-size: 2rem; margin-bottom: 10px; }
    .pricing-header h3 { font-size: 1.3rem; font-weight: 700; }
    .pricing-features { padding: 28px 24px; flex: 1; display: flex; flex-direction: column; }
    .plan-level {
      font-size: 0.82rem; font-weight: 700; letter-spacing: 1px;
      text-transform: uppercase; color: var(--text-light); margin-bottom: 6px;
    }
    .price-amount {
      font-size: 2.6rem; font-weight: 800; color: var(--dark); line-height: 1;
    }
    .price-amount span { font-size: 1rem; font-weight: 500; color: var(--text-light); }
    .price-sub { font-size: 0.82rem; color: var(--text-light); margin-bottom: 24px; }
    .pricing-features ul { list-style: none; margin-bottom: 28px; text-align: left; flex: 1; }
    .pricing-features ul li {
      padding: 10px 0; border-bottom: 1px solid #f0f0f0;
      font-size: 0.93rem; color: var(--text-light);
      display: flex; align-items: center; gap: 10px;
    }
    .pricing-features ul li i { color: var(--teal); font-size: 0.85rem; flex-shrink: 0; }
    .btn-plan {
      display: inline-flex; align-items: center; justify-content: center; gap: 8px;
      background: var(--dark); color: #fff; font-weight: 700;
      padding: 14px 28px; border-radius: 999px; font-size: 0.95rem;
      transition: var(--transition); margin-top: auto;
    }
    .btn-plan:hover { background: var(--red); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255,77,77,0.3); }
    .pricing-card.featured .btn-plan { background: var(--red); }
    .pricing-card.featured .btn-plan:hover { background: var(--red-dark); }

    .taxas { background: #fcfcfc; padding: 100px 0; }
    .table-responsive-wrapper { overflow-x: auto; border-radius: var(--radius); box-shadow: var(--shadow); }
    .comparison-table-classic {
      width: 100%; min-width: 560px;
      border-collapse: separate; border-spacing: 0;
      border-radius: var(--radius); overflow: hidden;
      border: 1px solid #eee; text-align: center;
    }
    .comparison-table-classic th,
    .comparison-table-classic td {
      padding: 16px 14px; border-bottom: 1px solid #eee;
      font-size: 0.93rem; color: #2c2c2c; font-weight: 500;
    }
    .header-row-classic th {
      background: #f9f9f9; font-weight: 700; padding: 20px 14px; border-bottom: none;
    }
    .header-row-classic th:first-child { text-align: left; padding-left: 24px; }
    .feature-cell-classic {
      background: #fafafa; text-align: left; padding-left: 24px; font-weight: 600; color: #444;
    }
    .highlight-fee-classic { font-weight: 600; color: #28a745; }

    .descontos { background: var(--white); padding: 100px 0; }
    .split-grid {
      display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center;
    }
    .split-img {
      border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-lg); position: relative;
    }
    .split-img img {
      width: 100%; height: 420px; object-fit: cover; transition: transform 0.6s ease;
    }
    .split-img:hover img { transform: scale(1.04); }
    .split-img-badge {
      position: absolute; bottom: -16px; right: -16px;
      background: var(--yellow); border-radius: var(--radius);
      padding: 16px 20px; box-shadow: var(--shadow); text-align: center;
    }
    .split-img-badge .big { font-size: 2rem; color: var(--dark); line-height: 1; }
    .split-img-badge .lbl { font-size: 0.75rem; font-weight: 700; color: var(--dark); }
    .split-text .section-tag { margin-bottom: 12px; }
    .split-text h2 {
     font-size: clamp(1.8rem, 3vw, 2.4rem);
      color: var(--dark); margin-bottom: 14px; line-height: 1.2;
    }
    .split-text > p { color: var(--text-light); line-height: 1.7; font-size: 1rem; margin-bottom: 28px; }
    .discount-list { list-style: none; display: flex; flex-direction: column; gap: 14px; }
    .discount-list li {
      display: flex; align-items: flex-start; gap: 14px;
      background: var(--bg); border-radius: var(--radius); padding: 16px 18px;
      transition: var(--transition);
    }
    .discount-list li:hover { transform: translateX(6px); background: #fff3e0; }
    .discount-icon {
      width: 40px; height: 40px; border-radius: 10px;
      background: #fff3f3; display: flex; align-items: center; justify-content: center;
      color: var(--red); font-size: 1rem; flex-shrink: 0;
    }
    .discount-text strong { display: block; font-weight: 700; color: var(--dark); margin-bottom: 2px; font-size: 0.97rem; }
    .discount-text span { font-size: 0.85rem; color: var(--text-light); }

    .faq-section { background: #f8f4ff; padding: 100px 0; }
    .faq-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .faq-item {
      background: var(--white); border-radius: var(--radius); padding: 28px 24px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.05); cursor: pointer;
      transition: var(--transition);
    }
    .faq-item:hover { transform: translateY(-4px); box-shadow: var(--shadow); }
    .faq-q {
      display: flex; align-items: center; gap: 12px; margin-bottom: 10px;
    }
    .faq-q i { color: var(--red); font-size: 1rem; flex-shrink: 0; }
    .faq-q strong { font-weight: 700; color: var(--dark); font-size: 0.97rem; }
    .faq-item p { font-size: 0.9rem; color: var(--text-light); line-height: 1.6; margin-left: 30px; }

    .pagamento { background: var(--white); padding: 100px 0; }
    .meios-grid {
      display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;
    }
    .meios-card {
      background: #fcfcfc; padding: 32px 24px; border-radius: var(--radius);
      box-shadow: 0 4px 14px rgba(0,0,0,0.05); border: 1px solid #eee;
      transition: transform 0.35s ease, box-shadow 0.35s ease, border-color 0.35s ease;
      position: relative; overflow: hidden;
    }
    .meios-card::before {
      content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
      background: linear-gradient(90deg, var(--red), var(--orange));
      transform: scaleX(0); transform-origin: left; transition: transform 0.4s ease;
    }
    .meios-card:hover { transform: translateY(-8px); box-shadow: var(--shadow); border-color: transparent; }
    .meios-card:hover::before { transform: scaleX(1); }
    .meios-icon {
      width: 52px; height: 52px; border-radius: 14px;
      background: #fff3f3; display: flex; align-items: center; justify-content: center;
      color: var(--red); font-size: 1.3rem; margin-bottom: 16px;
    }
    .meios-number {
      font-size: 1rem;
      color: rgba(0,0,0,0.12); position: absolute; top: 16px; right: 20px;
    }
    .meios-label { font-size: 1rem; font-weight: 700; color: #2c2c2c; margin-bottom: 12px; }
    .meios-text { font-size: 0.88rem; color: #6e6e6e; line-height: 1.6; }

    .trust-bar { background: var(--dark); padding: 60px 0; }
    .trust-grid {
      display: grid; grid-template-columns: repeat(4, 1fr); gap: 32px; text-align: center;
    }
    .trust-item { display: flex; flex-direction: column; align-items: center; gap: 10px; }
    .trust-icon { font-size: 2rem; color: var(--yellow); }
    .trust-num {
      font-size: 2.2rem; color: var(--white); line-height: 1;
    }
    .trust-lbl { font-size: 0.83rem; color: rgba(255,255,255,0.65); font-weight: 600; }

    .cta { background: linear-gradient(135deg, var(--red) 0%, #ff8c42 100%); padding: 80px 0; text-align: center; position: relative; overflow: hidden; }
    .cta::before { content: '🎓'; position: absolute; font-size: 18rem; opacity: 0.05; top: -40px; right: -40px; line-height: 1; }
    .cta h2 {  font-size: clamp(1.8rem, 3.5vw, 2.8rem); color: #fff; margin-bottom: 14px; }
    .cta p { color: rgba(255,255,255,0.9); max-width: 500px; margin: 0 auto 36px; font-size: 1rem; line-height: 1.7; }
    .btn-white {
      display: inline-flex; align-items: center; gap: 10px;
      background: #fff; color: var(--red); font-weight: 800;
      padding: 16px 36px; border-radius: 999px; font-size: 1rem;
      transition: var(--transition); box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    }
    .btn-white:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(0,0,0,0.2); }

    /* ── FOOTER ── */
    .main-footer { background: #121c1a; color: #ccc; padding-top: 50px; }
    .footer-container {
      max-width: 1200px; margin: 0 auto; padding: 0 20px 40px;
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
      border-top: 1px solid #333; display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;
    }
    .footer-bottom a { color: #ccc; }

    /* ── RESPONSIVE ── */
    @media (max-width: 1024px) {
      .pricing-table { grid-template-columns: 1fr 1fr; }
      .meios-grid { grid-template-columns: 1fr 1fr; }
      .trust-grid { grid-template-columns: 1fr 1fr; }
      .faq-grid { grid-template-columns: 1fr; }
      .pricing-card.featured { transform: scale(1); }
      .pricing-card.featured:hover { transform: translateY(-10px); }
    }
    @media (max-width: 768px) {
      .nav-links { display: none; }
      .hamburger { display: flex; }
      .pricing-table { grid-template-columns: 1fr; }
      .split-grid { grid-template-columns: 1fr; gap: 40px; }
      .split-img img { height: 300px; }
      .split-img-badge { bottom: 10px; right: 10px; }
      .meios-grid { grid-template-columns: 1fr 1fr; }
      .footer-container { flex-direction: column; }
      .footer-column, .description-column { width: 100%; }
      .footer-bottom { flex-direction: column; gap: 8px; text-align: center; }
    }
    @media (max-width: 480px) {
      .meios-grid { grid-template-columns: 1fr; }
      .trust-grid { grid-template-columns: 1fr 1fr; }
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
        <a href="matriculas.php">Matrículas</a>
        <a href="mensalidades.php" class="active">Mensalidades</a>
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

  <section class="hero">
    <div class="hero-overlay"></div>
    <div class="hero-inner">
      <h1>Investimento no futuro<br>do seu <span>pequeno</span></h1>
      <p>No Kindergarten, acreditamos que a educação de qualidade deve estar ao alcance de todos. Planos flexíveis, pensados para cada família.</p>
      <a href="#planos" class="hero-btn">
        <i class="fas fa-arrow-down"></i> Ver planos
      </a>
    </div>
  </section>

  <section class="trust-bar">
    <div class="container">
      <div class="trust-grid">
        <div class="trust-item reveal">
          <div class="trust-icon"><i class="fas fa-shield-halved"></i></div>
          <div class="trust-num">100%</div>
          <div class="trust-lbl">Transparência nos preços</div>
        </div>
        <div class="trust-item reveal reveal-delay-1">
          <div class="trust-icon"><i class="fas fa-wallet"></i></div>
          <div class="trust-num">3</div>
          <div class="trust-lbl">Planos disponíveis</div>
        </div>
        <div class="trust-item reveal reveal-delay-2">
          <div class="trust-icon"><i class="fas fa-mobile-screen"></i></div>
          <div class="trust-num">4</div>
          <div class="trust-lbl">Formas de pagamento</div>
        </div>
        <div class="trust-item reveal reveal-delay-3">
          <div class="trust-icon"><i class="fas fa-percent"></i></div>
          <div class="trust-num">10%</div>
          <div class="trust-lbl">Desconto para irmãos</div>
        </div>
      </div>
    </div>
  </section>

  <section class="pricing" id="planos">
    <div class="container">
      <div class="section-header reveal">
        <h2>Escolha o plano ideal para a sua família</h2>
        <p>Todos os planos incluem material didático, acompanhamento pedagógico e um ambiente seguro e acolhedor.</p>
      </div>
      <div class="pricing-table">

        <div class="pricing-card reveal">
          <div class="pricing-header">
           
            <h3>Meio Período</h3>
          </div>
          <div class="pricing-features">
            <div class="plan-level">Mini Kids</div>
            <div class="price-amount">4.500 MT<span>/mês</span></div>
            <div class="price-sub">2 a 3 anos • Manhã ou Tarde</div>
            <ul>
              <li><i class="fas fa-check"></i> 4 horas diárias</li>
              <li><i class="fas fa-check"></i> Manhã ou Tarde</li>
              <li><i class="fas fa-check"></i> Lanche incluso</li>
              <li><i class="fas fa-check"></i> Atividades pedagógicas</li>
              <li><i class="fas fa-check"></i> Material didático</li>
            </ul>
            <a href="matriculas.php#fazer-matricula" class="btn-plan">Escolher Plano <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>

        <div class="pricing-card featured reveal reveal-delay-1">
          <div class="popular-badge">Mais popular</div>
          <div class="pricing-header">
            <h3>Período Integral</h3>
          </div>
          <div class="pricing-features">
            <div class="plan-level">Pré-Escolar</div>
            <div class="price-amount">6.800 MT<span>/mês</span></div>
            <div class="price-sub">5 a 6 anos • Manhã e Tarde</div>
            <ul>
              <li><i class="fas fa-check"></i> 8 horas diárias</li>
              <li><i class="fas fa-check"></i> Manhã e Tarde</li>
              <li><i class="fas fa-check"></i> 3 refeições inclusas</li>
              <li><i class="fas fa-check"></i> Atividades extras</li>
              <li><i class="fas fa-check"></i> Material didático</li>
            </ul>
            <a href="matriculas.php#fazer-matricula" class="btn-plan">Escolher Plano <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>

        <div class="pricing-card reveal reveal-delay-2">
          <div class="pricing-header">

            <h3>Período Flexível</h3>
          </div>
          <div class="pricing-features">
            <div class="plan-level">Júnior</div>
            <div class="price-amount">5.500 MT<span>/mês</span></div>
            <div class="price-sub">4 a 5 anos • Horário flexível</div>
            <ul>
              <li><i class="fas fa-check"></i> 6 horas diárias</li>
              <li><i class="fas fa-check"></i> Horário flexível</li>
              <li><i class="fas fa-check"></i> 2 refeições inclusas</li>
              <li><i class="fas fa-check"></i> Atividades pedagógicas</li>
              <li><i class="fas fa-check"></i> Material didático</li>
            </ul>
            <a href="matriculas.php#fazer-matricula" class="btn-plan">Escolher Plano <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>

      </div>
    </div>
  </section>

  <section class="taxas">
    <div class="container">
      <div class="section-header reveal">
        <h2>Taxas Adicionais</h2>
        <p>No Kindergarten, valorizamos a transparência. Apresentamos abaixo todas as taxas de forma clara e acessível.</p>
      </div>
      <div class="table-responsive-wrapper reveal reveal-delay-1">
        <table class="comparison-table-classic">
          <thead>
            <tr class="header-row-classic">
              <th>Taxa</th>
              <th>Valor</th>
              <th>Observação</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th class="feature-cell-classic">Taxa de Matrícula</th>
              <td>1.000 MZN</td>
              <td>Paga apenas uma vez, no ato da inscrição.</td>
            </tr>
            <tr>
              <th class="feature-cell-classic">Material Escolar</th>
              <td class="highlight-fee-classic">Incluído</td>
              <td class="highlight-fee-classic">Todos os alunos recebem o material básico necessário.</td>
            </tr>
            <tr>
              <th class="feature-cell-classic">Transporte Escolar</th>
              <td class="highlight-fee-classic">Opcional</td>
              <td class="highlight-fee-classic">Disponível mediante acordo com a direção.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <section class="descontos">
    <div class="container">
      <div class="split-grid">
        <div class="split-img reveal-left">
          <img src="../imagens/img1.jpg" alt="Crianças aprendendo juntas">
          <div class="split-img-badge">
            <div class="big">10%</div>
            <div class="lbl">Desconto<br>irmãos</div>
          </div>
        </div>
        <div class="split-text reveal-right">
          <h2>Descontos e Benefícios para sua família</h2>
          <p>Oferecemos benefícios especiais para famílias e responsáveis que acreditam e investem no futuro dos seus pequenos conosco. Queremos facilitar o acesso à educação de qualidade para todas as famílias.</p>
          <ul class="discount-list">
            <li>
              <div class="discount-icon"><i class="fas fa-users"></i></div>
              <div class="discount-text">
                <strong>10% de desconto para irmãos</strong>
                <span>Válido para irmãos matriculados simultaneamente no mesmo ano letivo.</span>
              </div>
            </li>
            <li>
              <div class="discount-icon"><i class="fas fa-calendar-check"></i></div>
              <div class="discount-text">
                <strong>5% de desconto para pagamento anual antecipado</strong>
                <span>Pague o ano todo com desconto e sem preocupações mensais.</span>
              </div>
            </li>
            <li>
              <div class="discount-icon"><i class="fas fa-tag"></i></div>
              <div class="discount-text">
                <strong>Campanhas promocionais em meses específicos</strong>
                <span>Fique atento às nossas promoções sazonais e condições especiais.</span>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <section class="faq-section">
    <div class="container">
      <div class="section-header reveal">
        <h2>Perguntas Frequentes</h2>
        <p>Respondemos as dúvidas mais comuns sobre mensalidades, pagamentos e condições.</p>
      </div>
      <div class="faq-grid">
        <div class="faq-item reveal">
          <div class="faq-q"><i class="fas fa-circle-question"></i><strong>Quando é a data de vencimento da mensalidade?</strong></div>
          <p>As mensalidades vencem todo dia 5 de cada mês. Em caso de dificuldade, entre em contacto com a secretaria antecipadamente para negociar.</p>
        </div>
        <div class="faq-item reveal reveal-delay-1">
          <div class="faq-q"><i class="fas fa-circle-question"></i><strong>Existe multa por atraso no pagamento?</strong></div>
          <p>Pagamentos realizados após o dia 10 estão sujeitos a uma taxa de mora. Recomendamos sempre o pagamento em dia para evitar cobranças adicionais.</p>
        </div>
        <div class="faq-item reveal reveal-delay-2">
          <div class="faq-q"><i class="fas fa-circle-question"></i><strong>Posso mudar de plano ao longo do ano?</strong></div>
          <p>Sim, a mudança de plano pode ser solicitada junto à secretaria com 30 dias de antecedência, sujeita à disponibilidade de vagas.</p>
        </div>
        <div class="faq-item reveal reveal-delay-3">
          <div class="faq-q"><i class="fas fa-circle-question"></i><strong>Os preços sofrem reajuste anual?</strong></div>
          <p>Os valores são revistos anualmente com base na inflação e nos custos operacionais. As famílias são comunicadas com pelo menos 60 dias de antecedência.</p>
        </div>
        <div class="faq-item reveal">
          <div class="faq-q"><i class="fas fa-circle-question"></i><strong>O material didático está incluído em todos os planos?</strong></div>
          <p>Sim, o material didático básico está incluído em todos os planos. Materiais especiais para projetos ou atividades extras podem ter custo adicional.</p>
        </div>
        <div class="faq-item reveal reveal-delay-1">
          <div class="faq-q"><i class="fas fa-circle-question"></i><strong>Como solicito um recibo ou comprovativo de pagamento?</strong></div>
          <p>O recibo é emitido automaticamente após cada pagamento. Para segundas vias ou comprovativo para imposto de renda, contacte a secretaria.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="pagamento">
    <div class="container">
      <div class="section-header reveal">
        <h2>Como Efetuar o Pagamento</h2>
        <p>Disponibilizamos diferentes formas de pagamento para garantir praticidade, segurança e comodidade aos encarregados de educação.</p>
      </div>
      <div class="meios-grid">
        <div class="meios-card reveal">
          <div class="meios-number">01</div>
          <div class="meios-icon"><i class="fas fa-credit-card"></i></div>
          <p class="meios-label">Multicaixa / POS</p>
          <p class="meios-text">O pagamento pode ser realizado diretamente na secretaria através do terminal Multicaixa (POS). Transação rápida e segura, com emissão imediata do comprovativo.</p>
        </div>
        <div class="meios-card reveal reveal-delay-1">
          <div class="meios-number">02</div>
          <div class="meios-icon"><i class="fas fa-building-columns"></i></div>
          <p class="meios-label">Transferência Bancária</p>
          <p class="meios-text">Também aceitamos pagamentos por transferência bancária. Os dados da conta institucional podem ser solicitados junto à secretaria.</p>
        </div>
        <div class="meios-card reveal reveal-delay-2">
          <div class="meios-number">03</div>
          <div class="meios-icon"><i class="fas fa-money-bill-wave"></i></div>
          <p class="meios-label">Pagamento em Dinheiro</p>
          <p class="meios-text">O pagamento pode ser feito presencialmente em dinheiro, diretamente na secretaria. O recibo é emitido no ato, garantindo total transparência.</p>
        </div>
        <div class="meios-card reveal reveal-delay-3">
          <div class="meios-number">04</div>
          <div class="meios-icon"><i class="fas fa-mobile-screen-button"></i></div>
          <p class="meios-label">Carteiras Móveis</p>
          <p class="meios-text">Aceitamos <strong>MPesa, e-Mola</strong> e <strong>mKesh</strong>. Após o pagamento, envie o comprovativo digital por e-mail para validação.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="cta reveal">
    <div class="container">
      <h2>Pronto para garantir a vaga do seu filho?</h2>
      <p>Não perca tempo — as vagas são limitadas. Faça a matrícula agora e comece esta jornada incrível connosco.</p>
      <a href="matriculas.php" class="btn-white">
        Fazer matrícula <i class="fas fa-arrow-right"></i>
      </a>
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
          <li><a href="programa.html">Programa Educacional</a></li>
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
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
      navbar.classList.toggle('scrolled', window.scrollY > 40);
    });

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
  </script>
</body>
</html>