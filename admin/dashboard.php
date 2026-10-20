<?php
require_once 'config/admin_config.php';
verificarAdmin(); 

$db = getDB();

$stmt = $db->prepare("SELECT * FROM administradores WHERE id = ?");
$stmt->execute([$_SESSION[SESSION_ADMIN_ID]]);
$admin = $stmt->fetch();

$stmt = $db->query("SELECT COUNT(*) as total FROM alunos");
$total_alunos = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM alunos WHERE status_matricula = 'pendente'");
$matriculas_pendentes = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM pagamentos_matricula WHERE status = 'pendente'");
$pagamentos_pendentes = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM mensalidades WHERE status = 'pendente'");
$mensalidades_pendentes = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM mensagens_contato WHERE status = 'nao_respondido'");
$mensagens_nao_lidas = $stmt->fetch()['total'];

// Últimos logs
$stmt = $db->query("SELECT l.*, a.nome as admin_nome FROM admin_logs l LEFT JOIN administradores a ON l.admin_id = a.id ORDER BY l.data_hora DESC LIMIT 10");
$ultimos_logs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Kindergarten Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
     <link
  href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css"
  rel="stylesheet"
/> 
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <div class="admin-header">
        <div class="header-content">
            <div class="logo">
                <span>Kindergarten Admin</span>
            </div>
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="user-info">
                <div class="user-name">
                    <i class="fas fa-user-shield"></i>
                    <span>Olá, <?= htmlspecialchars($admin['nome']) ?></span>
                    <span class="badge" style="background: #ff983e; color: white; margin-left: 10px;">
                        <?= ucfirst($admin['nivel']) ?>
                    </span>
                </div>
                <a href="../index.php" class="btn-sair" style="background-color: blue;">ver site</a>
                <a href="logout.php" class="btn-sair">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>
    </div>

    <div class="admin-sidebar" id="sidebar">
        <ul class="sidebar-menu">
            <li class="active"><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="alunos.php"><i class="fas fa-users"></i> Alunos</a></li>
            <li><a href="turmas.php"><i class="fas fa-chalkboard"></i> Turmas</a></li>
            <li><a href="pagamentos.php"><i class="fas fa-credit-card"></i> Pagamentos</a></li>
            <li><a href="mensagens.php"><i class="fas fa-envelope"></i> Mensagens</a></li>
            <li><a href="comunicados.php"><i class="fas fa-bullhorn"></i> Comunicados</a></li>
            <li><a href="relatorios.php"><i class="fas fa-chart-line"></i> Relatórios</a></li>
            <li><a href="materiais.php"><i class="fas fa-book"></i> Biblioteca</a></li>
            <li><a href="../index.php"><i class="ri-graduation-cap-line"></i> Kindergarten</a></li>
            <?php if ($admin['nivel'] == 'super_admin'): ?>
            <li><a href="administradores.php"><i class="fas fa-user-cog"></i> Administradores</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="admin-main">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            Bem-vindo ao painel administrativo, <?= htmlspecialchars($admin['nome']) ?>! Hoje é <?= date('d/m/Y') ?>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #ff983e, #ff4d4d);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?= $total_alunos ?></div>
                <div class="stat-label">Total de Alunos</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #ffc107, #ff9800);">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value"><?= $matriculas_pendentes ?></div>
                <div class="stat-label">Matrículas Pendentes</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #28a745, #20c997);">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="stat-value"><?= $pagamentos_pendentes ?></div>
                <div class="stat-label">Pagamentos Pendentes</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #17a2b8, #007bff);">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="stat-value"><?= $mensagens_nao_lidas ?></div>
                <div class="stat-label">Mensagens Não Lidas</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-history"></i> Últimas Atividades</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr><th>Data/Hora</th><th>Administrador</th><th>Ação</th><th>Detalhes</th><th>IP</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach($ultimos_logs as $log): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i:s', strtotime($log['data_hora'])) ?></td>
                                <td><?= htmlspecialchars($log['admin_nome'] ?? 'Sistema') ?></td>
                                <td><?= htmlspecialchars($log['acao']) ?></td>
                                <td><?= htmlspecialchars($log['detalhes'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($log['ip_address'] ?? '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    </script>
</body>
</html>