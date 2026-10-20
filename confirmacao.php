<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['ultima_matricula'])) {
    header('Location: index.php');
    exit;
}

$matricula = $_SESSION['ultima_matricula'];

$db = getDB();
$stmt = $db->prepare("SELECT * FROM alunos WHERE id = ?");
$stmt->execute([$matricula['aluno_id']]);
$aluno = $stmt->fetch();

if (!$aluno) {
    header('Location: index.php');
    exit;
}

$data_formatada = formatarDataHora($aluno['data_matricula']);
$valor_matricula = 1500.00;
$valor_mensalidade = 4500.00;
$referencia_matricula = gerarReferenciaPagamento($aluno['id'], 'matricula');
$referencia_mensalidade = gerarReferenciaPagamento($aluno['id'], 'mensalidade');

?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <title>Confirmação de Matrícula — Kindergarten</title>
  <style>
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
    :root {
      --red: #ff4d4d; --orange: #ff983e; --green: #28a745;
      --dark: #1a1a2e; --text: #333; --text-light: #666;
      --transition: 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      --radius: 14px; --shadow: 0 8px 32px rgba(0,0,0,0.10);
    }
    body { font-family: "Poppins", sans-serif; background: #fffaf5; }
    
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
    main { padding: 60px 20px 80px; max-width: 860px; margin: 0 auto; }
    
    .success-banner {
      background: linear-gradient(135deg, #e8f5e9 0%, #f0fff4 100%);
      border: 1.5px solid #a8d5b0;
      border-radius: var(--radius);
      padding: 32px 36px;
      display: flex;
      align-items: center;
      gap: 20px;
      margin-bottom: 32px;
      animation: slideIn 0.6s ease both;
    }
    @keyframes slideIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: none; }
    }
    .success-icon {
      width: 60px; height: 60px; border-radius: 50%;
      background: var(--green);
      display: flex; align-items: center; justify-content: center;
    }
    .success-icon i { color: #fff; font-size: 1.6rem; }
    .success-text h2 { font-size: 1.3rem; font-weight: 700; color: #1b5e20; margin-bottom: 4px; }
    .success-text p { color: #2e7d32; font-size: 0.95rem; }
    
    .card {
      background: #fff; border-radius: var(--radius);
      box-shadow: var(--shadow);
      margin-bottom: 24px;
      overflow: hidden;
      animation: fadeUp 0.6s ease both;
    }
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(24px); }
      to { opacity: 1; transform: none; }
    }
    .card-header {
      padding: 18px 28px;
      display: flex; align-items: center; gap: 12px;
      border-bottom: 1px solid #f0f0f0;
    }
    .card-header i { font-size: 1.1rem; }
    .card-header h3 { font-size: 1rem; font-weight: 700; color: var(--text); }
    .card-body { padding: 28px; }
    
    .card-header.blue { background: #e8f0fe; }
    .card-header.blue i { color: #007bff; }
    .card-header.orange { background: #fff3e0; }
    .card-header.orange i { color: var(--orange); }
    
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .info-item label {
      font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
      color: var(--text-light); display: block; margin-bottom: 4px;
    }
    .info-item .value {
      font-size: 1rem; font-weight: 500; color: var(--text);
    }
    .info-item .value.code {
      font-size: 1.4rem; color: var(--orange);
      letter-spacing: 2px;
    }
    
    .access-note {
      background: linear-gradient(135deg, #fff8e1 0%, #fffde7 100%);
      border: 1.5px solid #ffe082;
      border-radius: 10px;
      padding: 16px 20px;
      display: flex; gap: 12px; align-items: flex-start;
      margin-top: 24px;
    }
    .access-note p { font-size: 0.88rem; color: #5d4037; line-height: 1.6; }
    .access-note strong { color: #e65100; }
    
    .payment-table { width: 100%; border-collapse: collapse; }
    .payment-table th, .payment-table td {
      padding: 12px 16px; text-align: left;
      border-bottom: 1px solid #f0f0f0; font-size: 0.92rem;
    }
    .payment-table th { background: #fafafa; font-weight: 700; color: var(--text-light); }
    .payment-table td.amount { font-weight: 700; color: var(--orange); }
    .ref-badge {
      display: inline-block;
      background: #e8f0fe; color: #007bff;
      font-weight: 700; font-size: 1rem; letter-spacing: 3px;
      padding: 6px 14px; border-radius: 8px;
    }
    
    .actions-row {
      display: flex; gap: 14px; flex-wrap: wrap; margin-top: 8px;
    }
    .btn {
      display: inline-flex; align-items: center; gap: 10px;
      padding: 13px 26px; border-radius: 999px;
      font-size: 0.95rem; font-weight: 700;
      text-decoration: none; cursor: pointer;
      transition: var(--transition);
    }
    .btn-orange {
      background: var(--orange); color: #fff;
      box-shadow: 0 6px 20px rgba(255,152,62,0.35);
    }
    .btn-orange:hover { background: #e8821a; transform: translateY(-2px); }
    .btn-outline {
      background: transparent; color: var(--text);
      border: 1.5px solid #ddd;
    }
    .btn-outline:hover { border-color: var(--orange); color: var(--orange); }
    .btn-print { background: var(--dark); color: #fff; }
    .btn-print:hover { background: #2d2d4e; transform: translateY(-2px); }
    
    @media (max-width: 600px) {
      .info-grid { grid-template-columns: 1fr; }
      .success-banner { flex-direction: column; text-align: center; }
    }
    
    @media print {
      header, .actions-row, footer { display: none; }
      body { background: #fff; }
      main { padding: 0; }
    }
  </style>
</head>
<body>

<header>
  <div class="nav-inner">
    <a href="index.php" class="logo">
      <i class="fas fa-graduation-cap" style="color:var(--orange)"></i>
      <span>Kindergarten</span>
    </a>
  </div>
</header>

<main>
  <div class="success-banner">
    <div class="success-icon"><i class="fas fa-check"></i></div>
    <div class="success-text">
      <h2>Pré-matrícula submetida com sucesso!</h2>
      <p>Os dados foram registados. Aguarde a confirmação após o pagamento.</p>
    </div>
  </div>

  <div class="card">
    <div class="card-header blue">
      <h3>Dados do Aluno</h3>
    </div>
    <div class="card-body">
      <div class="info-grid">
        <div class="info-item">
          <label>Código do Aluno</label>
          <div class="value code" id="codigoAluno"><?= htmlspecialchars($aluno['codigo_aluno']) ?></div>
        </div>
        <div class="info-item">
          <label>Estado da Matrícula</label>
          <div class="value">
            <span class="status-badge"> Pendente — Aguarda pagamento</span>
          </div>
        </div>
        <div class="info-item">
          <label>Nome do Aluno</label>
          <div class="value"><?= htmlspecialchars($aluno['nome_completo']) ?></div>
        </div>
        <div class="info-item">
          <label>Data de Nascimento</label>
          <div class="value"><?= formatarData($aluno['data_nascimento']) ?></div>
        </div>
        <div class="info-item">
          <label>Data da Matrícula</label>
          <div class="value"><?= $data_formatada['data'] ?></div>
        </div>
        <div class="info-item">
          <label>Hora da Matrícula</label>
          <div class="value"><?= $data_formatada['hora'] ?></div>
        </div>
      </div>

      <div class="access-note">
        <p>
          <strong>Guarde estes dados!</strong> O <strong>Código do Aluno</strong> e a
          <strong>Data de Nascimento</strong> serão usados para aceder ao perfil do aluno,
          onde o encarregado poderá consultar mensalidades, estado da matrícula e todas as
          informações do seu filho.
        </p>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header orange">
      <h3>Informações de Pagamento</h3>
    </div>
    <div class="card-body">
      <div class="payment-disclaimer" style="background: #f3f3f3; border-radius: 8px; padding: 12px 16px; margin-bottom: 24px; display: flex; gap: 8px;">
        Realize o pagamento. Após confirmação bancária, a secretaria aprovará a matrícula em até 48 horas úteis.
      </div>
      <table class="payment-table">
        <thead>
          <tr><th>Descrição</th><th>Entidade</th><th>Referência</th><th>Banco</th><th>Valor (MZN)</th></tr>
        </thead>
        <tbody>
          <tr>
            <td>Taxa de Matrícula</td>
            <td><span class="entity-badge">11547</span></td>
            <td><span class="ref-badge"><?= $referencia_matricula ?></span></td>
            <td><span class="bank-name">BCI — Millennium</span></td>
            <td class="amount"><?= formatarMoeda($valor_matricula) ?></td>
          </tr>
          <tr>
            <td>1.ª Mensalidade</td>
            <td><span class="entity-badge">11547</span></td>
            <td><span class="ref-badge"><?= $referencia_mensalidade ?></span></td>
            <td><span class="bank-name">BCI — Millennium</span></td>
            <td class="amount"><?= formatarMoeda($valor_mensalidade) ?></td>
          </tr>
        </tbody>
      </table>

      <div style="margin-top:18px; padding:14px 18px; background:#f8f9fa; border-radius:8px;">
        Conta bancária para depósito:<br>
        Banco: BIM — Millennium Bim | NIB: 0008 0000 9827 1035 101 35 | Titular: <strong>Kindergarten, Lda</strong>
      </div>
    </div>
  </div>

  <div class="actions-row">
    <button class="btn btn-print" onclick="window.print()">
       Imprimir Recibo
    </button>
    <a href="perfil_aluno.php?codigo=<?= urlencode($aluno['codigo_aluno']) ?>&data=<?= urlencode($aluno['data_nascimento']) ?>" class="btn btn-orange">
      <i class="fas fa-user-circle"></i> Aceder ao Perfil do Aluno
    </a>
    <a href="index.php" class="btn btn-outline">
      <i class="fas fa-arrow-left"></i> Voltar às Matrículas
    </a>
  </div>
</main>

<footer style="background: #121c1a; color: #ccc; padding: 30px 20px; text-align: center;">
  <p>© 2025 Kindergarten, Maputo — Todos os direitos reservados.</p>
</footer>

</body>
</html>