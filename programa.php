<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <title>Programa Educacional — Kindergarten</title>
  <style>
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

    :root {
      --red:        #ff4d4d;
      --red-dark:   #e63535;
      --yellow:     #ffd93d;
      --dark:       #1a1a2e;
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
    body { font-family: "Poppins", sans-serif; background: var(--bg); color: var(--text); overflow-x: hidden; }
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
    .hero-tennis {
      margin-top: 0;
      padding-top: 80px;
      height: 65vh;
      min-height: 420px;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: white;
      position: relative;
      overflow: hidden;
      background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
    }
    .hero-tennis::before {
      content: "";
      position: absolute;
      inset: 0;
      background: url("imagens/img4.png") center/cover no-repeat;
      opacity: 0.18;
    }
    .hero-tennis::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(to bottom, rgba(26,26,46,0.4) 0%, rgba(26,26,46,0.8) 100%);
    }
    .hero-content {
      position: relative;
      z-index: 2;
      max-width: 800px;
      padding: 0 24px;
      animation: heroIn 0.9s ease both;
    }
    @keyframes heroIn {
      from { opacity: 0; transform: translateY(36px); }
      to   { opacity: 1; transform: none; }
    }
    .hero-tag {
      display: inline-block;
      background: rgba(255,217,61,0.18);
      border: 1px solid rgba(255,217,61,0.4);
      color: var(--yellow);
      font-weight: 700;
      font-size: 0.75rem;
      letter-spacing: 2px;
      text-transform: uppercase;
      padding: 6px 18px;
      border-radius: 999px;
      margin-bottom: 18px;
    }
    .hero-tennis h1 {
      font-size: clamp(2rem, 5vw, 3rem);
      font-weight: 700;
      line-height: 1.25;
      margin-bottom: 18px;
      color: #fff;
    }
    .hero-tennis p {
      font-size: 1rem;
      font-weight: 400;
      opacity: 0.85;
      max-width: 620px;
      margin: 0 auto;
      line-height: 1.7;
    }
    .reveal {
      opacity: 0;
      transform: translateY(32px);
      transition: opacity 0.6s ease, transform 0.6s ease;
    }
    .reveal.visible { opacity: 1; transform: none; }
    .reveal-delay-1 { transition-delay: 0.1s; }
    .reveal-delay-2 { transition-delay: 0.2s; }
    .reveal-delay-3 { transition-delay: 0.3s; }
    .reveal-left {
      opacity: 0;
      transform: translateX(-36px);
      transition: opacity 0.65s ease, transform 0.65s ease;
    }
    .reveal-right {
      opacity: 0;
      transform: translateX(36px);
      transition: opacity 0.65s ease, transform 0.65s ease;
    }
    .reveal-left.visible,
    .reveal-right.visible { opacity: 1; transform: none; }
    .process-section {
      background-color: #fcfcfc;
      padding: 100px 20px;
    }
    .process-container {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      gap: 60px;
      align-items: flex-start;
    }
    .process-text-column {
      flex: 1;
      padding-right: 20px;
    }
    .process-title {
      font-size: clamp(1.6rem, 3vw, 2.4rem);
      font-weight: 700;
      color: #333;
      line-height: 1.25;
      margin-bottom: 48px;
    }
    .process-step {
      display: flex;
      margin-bottom: 32px;
      position: relative;
      transition: var(--transition);
    }
    .process-step:not(:last-child)::after {
      content: '';
      position: absolute;
      top: 44px;
      left: 19px;
      bottom: -32px;
      width: 2px;
      background: linear-gradient(to bottom, #e0e0e0, transparent);
    }
    .step-icon {
      width: 40px; height: 40px;
      background: linear-gradient(135deg, #3f5e6a, #2c4a55);
      color: #fff;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      font-weight: 700;
      font-size: 0.85rem;
      flex-shrink: 0;
      z-index: 1;
      box-shadow: 0 4px 14px rgba(63,94,106,0.3);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .process-step:hover .step-icon {
      transform: scale(1.15);
      box-shadow: 0 8px 20px rgba(63,94,106,0.45);
    }
    .step-content { margin-left: 20px; }
    .step-content h3 {
      font-size: 1.1rem;
      font-weight: 600;
      color: #333;
      margin-bottom: 8px;
    }
    .step-content p {
      font-size: 0.93rem;
      color: #666;
      line-height: 1.6;
    }
    .process-image-column { flex: 1.2; }
    .process-main-image {
      width: 100%;
      height: auto;
      border-radius: var(--radius);
      box-shadow: var(--shadow-lg);
      transition: transform 0.5s ease;
    }
    .process-image-column:hover .process-main-image { transform: scale(1.02); }
    .impact-section {
      background: var(--white);
      padding: 100px 20px;
      text-align: center;
    }
    .impact-container { max-width: 1200px; margin: 0 auto; }
    .impact-header { margin-bottom: 60px; }
    .impact-title {
      font-size: clamp(1.8rem, 3vw, 2.5rem);
      font-weight: 700;
      color: #2c2c2c;
      margin-bottom: 16px;
    }
    .impact-description {
      font-size: 1.05rem;
      color: #6e6e6e;
      max-width: 700px;
      margin: 0 auto;
      line-height: 1.7;
    }
    .metrics-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
    }
    .metric-card {
      background: #fcfcfc;
      padding: 32px 24px;
      border-radius: var(--radius);
      text-align: left;
      box-shadow: 0 4px 16px rgba(0,0,0,0.06);
      border: 1px solid #eee;
      transition: transform 0.35s ease, box-shadow 0.35s ease, border-color 0.35s ease;
      position: relative;
      overflow: hidden;
    }
    .metric-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 3px;
      background: linear-gradient(90deg, var(--red), #ff8c42);
      transform: scaleX(0);
      transform-origin: left;
      transition: transform 0.4s ease;
    }
    .metric-card:hover { transform: translateY(-8px); box-shadow: var(--shadow); border-color: transparent; }
    .metric-card:hover::before { transform: scaleX(1); }
    .metric-number {
      font-size: 2.2rem;
      font-weight: 700;
      color: var(--red);
      margin-bottom: 10px;
    }
    .metric-label {
      font-size: 0.97rem;
      font-weight: 600;
      color: #2c2c2c;
      margin-bottom: 12px;
    }
    .metric-text { font-size: 0.88rem; color: #6e6e6e; line-height: 1.6; }
    .metodologia-section {
      background: #f7fcf9;
      padding: 100px 20px;
    }
    .metodologia-container {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      gap: 60px;
      align-items: center;
    }
    .metodologia-images-column {
      flex: 1;
      display: flex;
      justify-content: flex-end;
    }
    .image-wrapper {
      position: relative;
      width: 100%;
      max-width: 420px;
      height: 480px;
    }
    .image-main {
      width: 60%; height: 90%;
      object-fit: cover;
      position: absolute;
      top: 0; left: 0;
      z-index: 5;
      border-radius: var(--radius);
      box-shadow: 0 6px 20px rgba(0,0,0,0.12);
      transition: transform 0.5s ease;
    }
    .image-overlay {
      width: 60%; height: 80%;
      object-fit: cover;
      position: absolute;
      bottom: 0; right: 0;
      z-index: 10;
      border-radius: var(--radius);
      box-shadow: 0 12px 28px rgba(0,0,0,0.2);
      transition: transform 0.5s ease;
    }
    .metodologia-images-column:hover .image-main  { transform: translate(-6px, -6px); }
    .metodologia-images-column:hover .image-overlay { transform: translate(6px, 6px); }
    .metodologia-content-column { flex: 1.5; }
    .metodologia-title {
      font-size: clamp(1.6rem, 3vw, 2.2rem);
      font-weight: 700;
      color: #333;
      margin-bottom: 20px;
    }
    .metodologia-description {
      font-size: 1.02rem;
      color: #555;
      line-height: 1.7;
    }

    .main-footer {
      background: #121c1a;
      color: #ccc;
      padding-top: 50px;
    }
    .footer-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px 40px;
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
    }
    .footer-column { width: 20%; min-width: 150px; margin-bottom: 30px; }
    .description-column { width: 25%; min-width: 200px; padding-right: 20px; }
    .footer-column h4 { color: #fff; font-size: 0.9rem; font-weight: 700; margin-bottom: 20px; letter-spacing: 0.5px; }
    .footer-column ul { list-style: none; }
    .footer-column li { margin-bottom: 10px; }
    .footer-column a { color: #ccc; font-size: 0.9rem; transition: color 0.25s; }
    .footer-column a:hover { color: #fff; }
    .footer-bottom {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
      border-top: 1px solid #333;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.8rem;
    }
    .footer-bottom a { color: #ccc; }
    @media (max-width: 768px) {
      .nav-links { display: none; }
      .hamburger { display: flex; }

      .process-container,
      .metodologia-container {
        flex-direction: column;
        gap: 40px;
      }
      .process-text-column { padding-right: 0; }
      .metodologia-images-column { justify-content: center; }
      .image-wrapper { height: 360px; max-width: 340px; }

      .metrics-grid { grid-template-columns: 1fr 1fr; }

      .footer-container { flex-direction: column; }
      .footer-column, .description-column { width: 100%; }
      .footer-bottom { flex-direction: column; gap: 8px; text-align: center; }
    }

    @media (max-width: 480px) {
      .metrics-grid { grid-template-columns: 1fr; }
      .image-wrapper { height: 280px; max-width: 260px; }
      .hero-tennis { height: auto; padding: 120px 0 60px; }
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
        <a href="programa.php" class="active">Programa</a>
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


    <section class="hero-tennis">
      <div class="hero-content">
        <div class="hero-tag">Educação de Qualidade</div>
        <h1>Nosso Programa Educacional</h1>
        <p>
          Nosso programa educacional foi desenvolvido para estimular o raciocínio, a criatividade, a imaginação e o desenvolvimento emocional de forma natural e divertida.
          Cada atividade é pensada para transformar o aprendizado em uma aventura encantadora!
        </p>
      </div>
    </section>

    <section class="process-section">
      <div class="process-container">
        <div class="process-text-column reveal-left">
          <h2 class="process-title">Estrutura do Programa (por idade ou turma)</h2>

          <div class="process-step reveal" style="transition-delay:0.1s">
            <div class="step-icon">01</div>
            <div class="step-content">
              <h3>Jardim I (3 a 4 anos)</h3>
              <p>Nesta fase, as crianças começam a explorar o mundo à sua volta por meio de jogos, músicas, histórias e descobertas sensoriais.</p>
            </div>
          </div>

          <div class="process-step reveal" style="transition-delay:0.2s">
            <div class="step-icon">02</div>
            <div class="step-content">
              <h3>Jardim II (4 a 5 anos)</h3>
              <p>Aqui, o aprendizado ganha mais estrutura. Trabalhamos com contação de histórias, expressão corporal, noções de número e forma, e atividades artísticas. O foco é fortalecer a curiosidade e o gosto por aprender.</p>
            </div>
          </div>

          <div class="process-step reveal" style="transition-delay:0.3s">
            <div class="step-icon">03</div>
            <div class="step-content">
              <h3>Pré-escola (5 a 6 anos)</h3>
              <p>Nesta etapa, as crianças são preparadas para o ensino primário, através de atividades lúdicas com letras, números, experiências científicas simples e projetos temáticos. Incentivamos a autonomia, o trabalho em grupo e a resolução de problemas.</p>
            </div>
          </div>
        </div>

        <div class="process-image-column reveal-right">
          <img src="../imagens/img4.png" alt="Crianças em atividade educacional" class="process-main-image">
        </div>
      </div>
    </section>
    <section class="impact-section">
      <div class="impact-container">
        <div class="impact-header reveal">
          <h2 class="impact-title">Áreas de desenvolvimento</h2>
          <p class="impact-description">
            Trabalhamos de forma integrada todas as dimensões do desenvolvimento infantil, garantindo que cada criança cresça de maneira equilibrada, segura e preparada para os desafios do futuro.
          </p>
        </div>
        <div class="metrics-grid">
          <div class="metric-card reveal">
            <h3 class="metric-number">1</h3>
            <p class="metric-label">Desenvolvimento Cognitivo</p>
            <p class="metric-text">jogos de lógica, histórias, experiências simples e curiosidades sobre o mundo.</p>
          </div>
          <div class="metric-card reveal reveal-delay-1">
            <h3 class="metric-number">2</h3>
            <p class="metric-label">Linguagem e Comunicação</p>
            <p class="metric-text">rodas de conversa, dramatizações, leitura de livros ilustrados.</p>
          </div>
          <div class="metric-card reveal reveal-delay-2">
            <h3 class="metric-number">3</h3>
            <p class="metric-label">Socialização e Valores</p>
            <p class="metric-text">cooperação, respeito, amizade e empatia nas atividades diárias.</p>
          </div>
          <div class="metric-card reveal reveal-delay-3">
            <h3 class="metric-number">4</h3>
            <p class="metric-label">Expressão Artística</p>
            <p class="metric-text">pintura, modelagem, música e dança.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="metodologia-section">
      <div class="metodologia-container">
        <div class="metodologia-images-column reveal-left">
          <div class="image-wrapper">
            <img src="../imagens/img3.jpeg" class="image-main" alt="Metodologia de ensino">
            <img src="../imagens/img2.jpg"  class="image-overlay" alt="Crianças aprendendo">
          </div>
        </div>
        <div class="metodologia-content-column reveal-right">
          <h2 class="metodologia-title">Metodologia de Ensino</h2>
          <p class="metodologia-description">
            Utilizamos uma metodologia lúdica e participativa, onde o brincar é o principal meio de aprendizado.
            Cada atividade é planejada para despertar a curiosidade natural da criança e promover o desenvolvimento integral — físico, emocional, social e intelectual.
            Nossos professores atuam como guias e parceiros na descoberta do conhecimento.
          </p>
        </div>
      </div>
    </section>

  </main>

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
    }, { threshold: 0.12 });
    allReveals.forEach(el => observer.observe(el));
  </script>
</body>
</html>