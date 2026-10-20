<?php
require_once 'config/admin_config.php';
verificarAdmin();

$db = getDB();

// Confirmar pagamento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_pagamento'])) {
    $tipo = $_POST['tipo'];
    $id = $_POST['id'];
    
    try {
        if ($tipo === 'matricula') {
            $stmt = $db->prepare("UPDATE pagamentos_matricula SET status = 'confirmado', data_pagamento = CURDATE() WHERE id = ?");
            $stmt->execute([$id]);
            
            // Atualizar status do aluno
            $stmt = $db->prepare("
                UPDATE alunos a 
                SET a.status_matricula = 'confirmada', a.status_pagamento = 'aprovado' 
                WHERE a.id = (SELECT p.aluno_id FROM pagamentos_matricula p WHERE p.id = ?)
            ");
            $stmt->execute([$id]);
            
        } elseif ($tipo === 'mensalidade') {
            $stmt = $db->prepare("UPDATE mensalidades SET status = 'paga', data_pagamento = CURDATE() WHERE id = ?");
            $stmt->execute([$id]);
        }
        
        $_SESSION['mensagem'] = "Pagamento confirmado com sucesso!";
        registrarLog('confirmar_pagamento', "Confirmou pagamento ID: $id - Tipo: $tipo");
        
    } catch (Exception $e) {
        $_SESSION['erro'] = "Erro ao confirmar pagamento: " . $e->getMessage();
    }
    
    header('Location: pagamentos.php');
    exit;
}

// Buscar pagamentos pendentes
$stmt = $db->prepare("
    SELECT p.*, a.nome_completo, a.codigo_aluno 
    FROM pagamentos_matricula p
    JOIN alunos a ON p.aluno_id = a.id
    WHERE p.status = 'pendente'
    ORDER BY p.created_at DESC
");
$stmt->execute();
$pagamentos_pendentes = $stmt->fetchAll();

// Buscar mensalidades pendentes
$stmt = $db->prepare("
    SELECT m.*, a.nome_completo, a.codigo_aluno 
    FROM mensalidades m
    JOIN alunos a ON m.aluno_id = a.id
    WHERE m.status = 'pendente'
    ORDER BY m.data_vencimento ASC
");
$stmt->execute();
$mensalidades_pendentes = $stmt->fetchAll();

// Buscar pagamentos confirmados (histórico)
$stmt = $db->prepare("
    SELECT p.*, a.nome_completo, a.codigo_aluno 
    FROM pagamentos_matricula p
    JOIN alunos a ON p.aluno_id = a.id
    WHERE p.status = 'confirmado'
    ORDER BY p.data_pagamento DESC
    LIMIT 20
");
$stmt->execute();
$pagamentos_confirmados = $stmt->fetchAll();

$mensagem = $_SESSION['mensagem'] ?? null;
$erro = $_SESSION['erro'] ?? null;
unset($_SESSION['mensagem'], $_SESSION['erro']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamentos - Kindergarten Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .pagamento-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .status-pendente { background: #fff3cd; color: #856404; padding: 5px 10px; border-radius: 5px; }
        .status-confirmado { background: #d4edda; color: #155724; padding: 5px 10px; border-radius: 5px; }
        .valor { font-size: 1.2rem; font-weight: bold; color: #28a745; }
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            background: #f0f0f0;
            border-radius: 5px;
        }
        .tab.active {
            background: #ff983e;
            color: white;
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
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
            <li class="active"><a href="pagamentos.php"><i class="fas fa-credit-card"></i> Pagamentos</a></li>
            <li><a href="mensagens.php"><i class="fas fa-envelope"></i> Mensagens</a></li>
            <li><a href="comunicados.php"><i class="fas fa-bullhorn"></i> Comunicados</a></li>
            <li><a href="relatorios.php"><i class="fas fa-chart-line"></i> Relatórios</a></li>
        </ul>
    </div>

    <div class="admin-main">
        <?php if ($mensagem): ?>
            <div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>
        
        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-credit-card"></i> Gerenciar Pagamentos</h3>
            </div>
            <div class="card-body">
                <div class="tabs">
                    <div class="tab active" onclick="mostrarTab('pendentes')">
                        <i class="fas fa-clock"></i> Pendentes 
                        <span class="badge"><?= count($pagamentos_pendentes) + count($mensalidades_pendentes) ?></span>
                    </div>
                    <div class="tab" onclick="mostrarTab('historico')">
                        <i class="fas fa-history"></i> Histórico
                    </div>
                </div>

                <!-- Pagamentos Pendentes -->
                <div id="tab-pendentes" class="tab-content active">
                    <h4><i class="fas fa-file-invoice"></i> Pagamentos de Matrícula Pendentes</h4>
                    <?php if (count($pagamentos_pendentes) > 0): ?>
                        <?php foreach($pagamentos_pendentes as $pagamento): ?>
                        <div class="pagamento-card">
                            <div style="display: flex; justify-content: space-between; align-items: start;">
                                <div>
                                    <h4><?= htmlspecialchars($pagamento['nome_completo']) ?></h4>
                                    <p><strong>Código:</strong> <?= htmlspecialchars($pagamento['codigo_aluno']) ?></p>
                                    <p><strong>Valor:</strong> <span class="valor"><?= number_format($pagamento['valor'], 2, ',', '.') ?> MT</span></p>
                                    <p><strong>Data Solicitação:</strong> <?= date('d/m/Y H:i', strtotime($pagamento['created_at'])) ?></p>
                                    <p><strong>Tipo:</strong> Taxa de Matrícula</p>
                                </div>
                                <form method="POST" style="margin: 0;">
                                    <input type="hidden" name="tipo" value="matricula">
                                    <input type="hidden" name="id" value="<?= $pagamento['id'] ?>">
                                    <button type="submit" name="confirmar_pagamento" class="btn btn-success">
                                        <i class="fas fa-check"></i> Confirmar Pagamento
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p><i class="fas fa-check-circle"></i> Nenhum pagamento de matrícula pendente.</p>
                    <?php endif; ?>

                    <h4 style="margin-top: 30px;"><i class="fas fa-calendar"></i> Mensalidades Pendentes</h4>
                    <?php if (count($mensalidades_pendentes) > 0): ?>
                        <?php foreach($mensalidades_pendentes as $mensalidade): ?>
                        <div class="pagamento-card">
                            <div style="display: flex; justify-content: space-between; align-items: start;">
                                <div>
                                    <h4><?= htmlspecialchars($mensalidade['nome_completo']) ?></h4>
                                    <p><strong>Código:</strong> <?= htmlspecialchars($mensalidade['codigo_aluno']) ?></p>
                                    <p><strong>Mês/Ano:</strong> <?= $mensalidade['mes_referencia'] ?> <?= $mensalidade['ano'] ?></p>
                                    <p><strong>Valor:</strong> <span class="valor"><?= number_format($mensalidade['valor'], 2, ',', '.') ?> MT</span></p>
                                    <p><strong>Vencimento:</strong> <?= date('d/m/Y', strtotime($mensalidade['data_vencimento'])) ?></p>
                                    <?php if(strtotime($mensalidade['data_vencimento']) < time()): ?>
                                        <p><span class="status-pendente"><i class="fas fa-exclamation-triangle"></i> Atrasado</span></p>
                                    <?php endif; ?>
                                </div>
                                <form method="POST" style="margin: 0;">
                                    <input type="hidden" name="tipo" value="mensalidade">
                                    <input type="hidden" name="id" value="<?= $mensalidade['id'] ?>">
                                    <button type="submit" name="confirmar_pagamento" class="btn btn-success">
                                        <i class="fas fa-check"></i> Confirmar Pagamento
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p><i class="fas fa-check-circle"></i> Todas as mensalidades estão em dia!</p>
                    <?php endif; ?>
                </div>

                <!-- Histórico de Pagamentos -->
                <div id="tab-historico" class="tab-content">
                    <h4><i class="fas fa-history"></i> Pagamentos Confirmados</h4>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr><th>Data</th><th>Aluno</th><th>Código</th><th>Tipo</th><th>Valor</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach($pagamentos_confirmados as $pagamento): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($pagamento['data_pagamento'] ?? $pagamento['created_at'])) ?></td>
                                    <td><?= htmlspecialchars($pagamento['nome_completo']) ?></td>
                                    <td><?= htmlspecialchars($pagamento['codigo_aluno']) ?></td>
                                    <td><?= $pagamento['tipo'] == 'matricula' ? 'Matrícula' : 'Mensalidade' ?></td>
                                    <td><?= number_format($pagamento['valor'], 2, ',', '.') ?> MT</td>
                                    <td><span class="status-confirmado"><i class="fas fa-check"></i> Confirmado</span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function mostrarTab(tab) {
            // Esconder todos os tabs
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab').forEach(el => el.classList.remove('active'));
            
            // Mostrar o tab selecionado
            document.getElementById(`tab-${tab}`).classList.add('active');
            event.target.closest('.tab').classList.add('active');
        }
        
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        menuToggle.addEventListener('click', () => { sidebar.classList.toggle('active'); });
    </script>
</body>
</html>