<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <title>Aceder ao Perfil — Kindergarten</title>
  <style>
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
    :root {
      --orange: #ff983e; --red: #ff4d4d;
      --dark: #1a1a2e; --text: #333; --text-light: #666;
      --transition: 0.35s cubic-bezier(0.4,0,0.2,1);
      --radius: 14px; --shadow: 0 8px 32px rgba(0,0,0,0.10);
    }
    body { font-family: "Poppins", sans-serif; background: #fffaf5; min-height: 100vh; display: flex; flex-direction: column; }
    header {
      background: rgba(255,255,255,0.96);
      backdrop-filter: blur(14px);
      box-shadow: 0 4px 28px rgba(0,0,0,0.08);
      padding: 4px 0;
    }
    .nav-inner {
      max-width: 1200px; margin: 0 auto;
      padding: 14px 24px;
      display: flex; align-items: center;
    }
    .logo {
      display: flex; align-items: center; gap: 10px;
       font-size: 1.5rem;
      color: var(--dark); text-decoration: none;
    }
    main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 60px 20px; }
    .login-wrapper { width: 100%; max-width: 460px; animation: fadeUp 0.7s ease both; }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: none; } }
    .login-icon {
      width: 72px; height: 72px; border-radius: 50%;
      background: linear-gradient(135deg, var(--orange), var(--red));
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 24px;
      box-shadow: 0 8px 24px rgba(255,152,62,0.35);
    }
    .login-icon i { color: #fff; font-size: 1.8rem; }
    .login-title { text-align: center; margin-bottom: 32px; }
    .login-title h1 { font-size: 1.5rem; font-weight: 700; color: var(--dark); margin-bottom: 6px; }
    .login-title p { font-size: 0.93rem; color: var(--text-light); line-height: 1.6; }
    .login-card {
      background: #fff; border-radius: var(--radius);
      box-shadow: var(--shadow); border: 1px solid #f0f0f0;
      padding: 36px 40px;
    }
    .form-field { margin-bottom: 22px; }
    .form-field label { display: block; font-size: 0.85rem; font-weight: 700; color: var(--text); margin-bottom: 7px; }
    .input-wrap { position: relative; }
    .input-wrap i {
      position: absolute; left: 14px; top: 50%;
      transform: translateY(-50%);
      color: #bbb; font-size: 1rem;
    }
    .input-wrap input {
      width: 100%; padding: 13px 14px 13px 40px;
      border: 1.5px solid #e0e0e0; border-radius: 10px;
      font-size: 0.97rem; font-family: "Poppins", sans-serif;
      background: #f7f8fc; color: var(--text);
      outline: none;
    }
    .input-wrap input:focus { border-color: var(--orange); box-shadow: 0 0 0 3px rgba(255,152,62,0.12); }
    .btn-login {
      width: 100%; padding: 14px;
      background: linear-gradient(135deg, var(--orange), var(--red));
      color: #fff; border: none; border-radius: 10px;
      font-size: 1rem; font-weight: 700;
      cursor: pointer; transition: var(--transition);
    }
    .btn-login:hover { transform: translateY(-2px); }
    .divider { text-align: center; margin: 22px 0; color: #ccc; font-size: 0.85rem; position: relative; }
    .divider::before, .divider::after {
      content: ''; position: absolute; top: 50%; width: 38%; height: 1px; background: #eee;
    }
    .divider::before { left: 0; }
    .divider::after { right: 0; }
    .back-link {
      display: flex; align-items: center; justify-content: center; gap: 8px;
      color: var(--text-light); font-size: 0.88rem; text-decoration: none;
    }
    .back-link:hover { color: var(--orange); }
    .error-msg {
      background: #fdecea; border: 1px solid #f5c6cb;
      color: #c62828; border-radius: 8px;
      padding: 11px 14px; font-size: 0.88rem;
      display: flex; align-items: center; gap: 8px;
      margin-bottom: 18px; display: none;
    }
    footer { background: #121c1a; color: #ccc; padding: 20px; text-align: center; font-size: 0.8rem; }
  </style>
</head>
<body>
  <header>
    <div class="nav-inner">
      <a href="index.php" class="logo">
        <i class="fas fa-graduation-cap" style="color:var(--orange); margin-right:8px"></i>
        Kindergarten
      </a>
    </div>
  </header>

  <main>
    <div class="login-wrapper">
      <div class="login-icon"><i class="fas fa-user-graduate"></i></div>
      <div class="login-title">
        <h1>Aceder ao Perfil do Aluno</h1>
        <p>Introduza o Código do Aluno e a Data de Nascimento para consultar o estado da matrícula e mensalidades.</p>
      </div>

      <div class="login-card">
        <div class="error-msg" id="errorMsg">
          <i class="fas fa-exclamation-triangle"></i>
          <span>Código ou data de nascimento incorrectos. Verifique e tente novamente.</span>
        </div>

        <form method="POST" action="api/verificar_acesso.php">
          <div class="form-field">
            <label for="codigoAluno">Código do Aluno</label>
            <div class="input-wrap">
              <input type="text" id="codigoAluno" name="codigo_aluno" placeholder="Ex: KG-2025-00142" autocomplete="off" required>
              <i class="fas fa-id-badge"></i>
            </div>
            <div class="input-hint"> Fornecido na confirmação da pré-matrícula</div>
          </div>

          <div class="form-field">
            <label for="dataNasc">Data de Nascimento do Aluno</label>
            <div class="input-wrap">
              <input type="date" id="dataNasc" name="data_nascimento" required>
              <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="input-hint">Data conforme a certidão de nascimento</div>
          </div>

          <button type="submit" class="btn-login">
            Entrar no Perfil
          </button>
        </form>

        <div class="divider">ou</div>
        <a href="matriculas.php" class="back-link">
          <i class="fas fa-arrow-left"></i> Fazer nova matrícula
        </a>
      </div>
    </div>
  </main>

  <footer>
    <p>© 2025 Kindergarten, Maputo — Todos os direitos reservados.</p>
  </footer>
</body>
</html>