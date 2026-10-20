<?php
require_once 'config/admin_config.php';
verificarAdmin();

$db = getDB();

// Buscar estatísticas gerais
$stats = [];

// Total de alunos
$stmt = $db->query("SELECT COUNT(*) as total FROM alunos");
$stats['total_alunos'] = $stmt->fetch()['total'];

// Alunos por turma
$stats['alunos_por_turma'] = $db->query("
    SELECT t.nome, COUNT(a.id) as total 
    FROM turmas t 
    LEFT JOIN alunos a ON t.id = a.turma_id 
    GROUP BY t.id
")->fetchAll();

// Alunos por período
$stats['alunos_por_periodo'] = $db->query("
    SELECT periodo, COUNT(*) as total 
    FROM alunos 
    GROUP BY periodo
")->fetchAll();

// Status de pagamentos
$stmt = $db->query("SELECT COUNT(*) as total FROM pagamentos_matricula WHERE status = 'pendente'");
$stats['pagamentos_pendentes'] = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM pagamentos_matricula WHERE status = 'confirmado'");
$stats['pagamentos_confirmados'] = $stmt->fetch()['total'];

// Mensalidades
$stmt = $db->query("SELECT COUNT(*) as total FROM mensalidades WHERE status = 'pendente'");
$stats['mensalidades_pendentes'] = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM mensalidades WHERE status = 'paga'");
$stats['mensalidades_pagas'] = $stmt->fetch()['total'];

// Receita total
$stmt = $db->query("SELECT SUM(valor) as total FROM pagamentos_matricula WHERE status = 'confirmado'");
$stats['receita_matriculas'] = $stmt->fetch()['total'] ?? 0;

$stmt = $db->query("SELECT SUM(valor) as total FROM mensalidades WHERE status = 'paga'");
$stats['receita_mensalidades'] = $stmt->fetch()['total'] ?? 0;

$stats['receita_total'] = $stats['receita_matriculas'] + $stats['receita_mensalidades'];

// Matrículas por mês
$stats['matriculas_por_mes'] = $db->query("
    SELECT 
        DATE_FORMAT(data_matricula, '%Y-%m') as mes,
        COUNT(*) as total
    FROM alunos
    WHERE data_matricula >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(data_matricula, '%Y-%m')
    ORDER BY mes DESC
")->fetchAll();

// Top alunos com mais mensagens
$stats['top_alunos_mensagens'] = $db->query("
    SELECT a.nome_completo, COUNT(m.id) as total_mensagens
    FROM alunos a
    JOIN mensagens m ON a.id = m.remetente_id
    GROUP BY a.id
    ORDER BY total_mensagens DESC
    LIMIT 10
")->fetchAll();

// Processar exportação
if (isset($_GET['exportar'])) {
    $tipo = $_GET['exportar'];
    $format = $_GET['formato'] ?? 'csv';
    
    switch($tipo) {
        case 'alunos':
            $data = $db->query("SELECT id, codigo_aluno, nome_completo, data_nascimento, nome_responsavel, telefone, email, data_matricula, status_matricula FROM alunos")->fetchAll();
            $filename = "relatorio_alunos_" . date('Y-m-d');
            break;
        case 'pagamentos':
            $data = $db->query("
                SELECT p.*, a.nome_completo, a.codigo_aluno 
                FROM pagamentos_matricula p
                JOIN alunos a ON p.aluno_id = a.id
                ORDER BY p.created_at DESC
            ")->fetchAll();
            $filename = "relatorio_pagamentos_" . date('Y-m-d');
            break;
        case 'mensalidades':
            $data = $db->query("
                SELECT m.*, a.nome_completo, a.codigo_aluno 
                FROM mensalidades m
                JOIN alunos a ON m.aluno_id = a.id
                ORDER BY m.data_vencimento DESC
            ")->fetchAll();
            $filename = "relatorio_mensalidades_" . date('Y-m-d');
            break;
        default:
            die("Tipo de relatório inválido");
    }
    
    if ($format == 'csv') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        
        $output = fopen('php://output', 'w');
        if (!empty($data)) {
            fputcsv($output, array_keys($data[0]));
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }
        fclose($output);
        exit;
    }
}

$mensagem = $_SESSION['mensagem'] ?? null;
unset($_SESSION['mensagem']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - Kindergarten Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #ff983e;
        }
        .stat-card .stat-label {
            color: #666;
            margin-top: 5px;
        }
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .chart-title {
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #ff983e;
            padding-bottom: 10px;
        }
        .bar-chart {
            margin: 15px 0;
        }
        .bar-item {
            margin-bottom: 10px;
        }
        .bar-label {
            display: inline-block;
            width: 150px;
            font-size: 0.9rem;
        }
        .bar-fill {
            display: inline-block;
            height: 25px;
            background: linear-gradient(90deg, #ff983e, #ff4d4d);
            border-radius: 5px;
            color: white;
            line-height: 25px;
            padding-left: 10px;
            font-size: 0.85rem;
            min-width: 30px;
        }
        .export-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .btn-export {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-export:hover {
            background: #1e7e34;
        }
    </style>
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
                    <span><?= htmlspecialchars($_SESSION[SESSION_ADMIN_NOME]) ?></span>
                </div>
                <a href="logout.php" class="btn-sair">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>
    </div>

    <div class="admin-sidebar" id="sidebar">
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="alunos.php"><i class="fas fa-users"></i> Alunos</a></li>
            <li><a href="turmas.php"><i class="fas fa-chalkboard"></i> Turmas</a></li>
            <li><a href="pagamentos.php"><i class="fas fa-credit-card"></i> Pagamentos</a></li>
            <li><a href="mensagens.php"><i class="fas fa-envelope"></i> Mensagens</a></li>
            <li><a href="comunicados.php"><i class="fas fa-bullhorn"></i> Comunicados</a></li>
            <li class="active"><a href="relatorios.php"><i class="fas fa-chart-line"></i> Relatórios</a></li>
            <li><a href="administradores.php"><i class="fas fa-user-cog"></i> Administradores</a></li>
        </ul>
    </div>

    <div class="admin-main">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= $stats['total_alunos'] ?></div>
                <div class="stat-label">Total de Alunos</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= number_format($stats['receita_total'], 2, ',', '.') ?> MT</div>
                <div class="stat-label">Receita Total</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $stats['pagamentos_pendentes'] ?></div>
                <div class="stat-label">Pagamentos Pendentes</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $stats['mensalidades_pendentes'] ?></div>
                <div class="stat-label">Mensalidades Pendentes</div>
            </div>
        </div>

        <div class="chart-container">
            <div class="chart-title"><i class="fas fa-chalkboard"></i> Alunos por Turma</div>
            <?php foreach($stats['alunos_por_turma'] as $turma): ?>
                <div class="bar-item">
                    <span class="bar-label"><?= htmlspecialchars($turma['nome']) ?>:</span>
                    <div class="bar-fill" style="width: <?= min(300, $turma['total'] * 15) ?>px">
                        <?= $turma['total'] ?> alunos
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="chart-container">
            <div class="chart-title"><i class="fas fa-clock"></i> Alunos por Período</div>
            <?php foreach($stats['alunos_por_periodo'] as $periodo): ?>
                <div class="bar-item">
                    <span class="bar-label"><?= htmlspecialchars($periodo['periodo']) ?>:</span>
                    <div class="bar-fill" style="width: <?= min(300, $periodo['total'] * 15) ?>px">
                        <?= $periodo['total'] ?> alunos
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="chart-container">
            <div class="chart-title"><i class="fas fa-chart-line"></i> Matrículas nos Últimos 6 Meses</div>
            <?php foreach($stats['matriculas_por_mes'] as $mes): ?>
                <div class="bar-item">
                    <span class="bar-label"><?= date('m/Y', strtotime($mes['mes'] . '-01')) ?>:</span>
                    <div class="bar-fill" style="width: <?= min(300, $mes['total'] * 15) ?>px">
                        <?= $mes['total'] ?> matrículas
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="chart-container">
            <div class="chart-title"><i class="fas fa-chart-pie"></i> Resumo Financeiro</div>
            <div class="bar-item">
                <span class="bar-label">Matrículas Pagas:</span>
                <div class="bar-fill" style="width: <?= min(300, ($stats['pagamentos_confirmados'] / max(1, $stats['pagamentos_confirmados'] + $stats['pagamentos_pendentes']) * 300)) ?>px">
                    <?= number_format($stats['receita_matriculas'], 2, ',', '.') ?> MT
                </div>
            </div>
            <div class="bar-item">
                <span class="bar-label">Mensalidades Pagas:</span>
                <div class="bar-fill" style="width: <?= min(300, ($stats['mensalidades_pagas'] / max(1, $stats['mensalidades_pagas'] + $stats['mensalidades_pendentes']) * 300)) ?>px">
                    <?= number_format($stats['receita_mensalidades'], 2, ',', '.') ?> MT
                </div>
            </div>
        </div>

        <div class="chart-container">
            <div class="chart-title"><i class="fas fa-download"></i> Exportar Relatórios</div>
            <div class="export-buttons">
                <a href="?exportar=alunos&formato=csv" class="btn-export">
                    <i class="fas fa-users"></i> Exportar Alunos (CSV)
                </a>
                <a href="?exportar=pagamentos&formato=csv" class="btn-export">
                    <i class="fas fa-credit-card"></i> Exportar Pagamentos (CSV)
                </a>
                <a href="?exportar=mensalidades&formato=csv" class="btn-export">
                    <i class="fas fa-calendar"></i> Exportar Mensalidades (CSV)
                </a>
            </div>
            <div style="margin-top: 15px; font-size: 0.85rem; color: #666;">
                <i class="fas fa-info-circle"></i> Os relatórios serão baixados em formato CSV, compatível com Excel.
            </div>
        </div>
    </div>

    <script>
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        if (menuToggle) {
            menuToggle.addEventListener('click', () => { sidebar.classList.toggle('active'); });
        }
    </script>
</body>
</html>