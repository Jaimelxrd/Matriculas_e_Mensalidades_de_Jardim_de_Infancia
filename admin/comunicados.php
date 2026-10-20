<?php
require_once 'config/admin_config.php';
verificarAdmin();

$db = getDB();
$admin_id = $_SESSION[SESSION_ADMIN_ID];

// Processar criação de comunicado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar_comunicado'])) {
    $titulo = trim($_POST['titulo']);
    $mensagem = trim($_POST['mensagem']);
    $tipo = $_POST['tipo'];
    $data_expiracao = !empty($_POST['data_expiracao']) ? $_POST['data_expiracao'] : null;
    
    if ($titulo && $mensagem) {
        $stmt = $db->prepare("
            INSERT INTO comunicados (titulo, mensagem, tipo, admin_id, data_expiracao, ativo) 
            VALUES (?, ?, ?, ?, ?, 1)
        ");
        $stmt->execute([$titulo, $mensagem, $tipo, $admin_id, $data_expiracao]);
        $_SESSION['mensagem'] = "Comunicado enviado com sucesso!";
        registrarLog('criar_comunicado', "Criou comunicado: $titulo");
    } else {
        $_SESSION['erro'] = "Preencha todos os campos obrigatórios";
    }
    header('Location: comunicados.php');
    exit;
}

// Processar exclusão de comunicado
if (isset($_GET['excluir']) && $_GET['excluir'] == '1' && isset($_GET['id'])) {
    $id_comunicado = intval($_GET['id']);
    
    try {
        // Verificar se o comunicado existe
        $check = $db->prepare("SELECT id FROM comunicados WHERE id = ?");
        $check->execute([$id_comunicado]);
        
        if ($check->rowCount() > 0) {
            // Excluir primeiro os registros relacionados (se houver chave estrangeira)
            $stmt1 = $db->prepare("DELETE FROM comunicados_lidos WHERE comunicado_id = ?");
            $stmt1->execute([$id_comunicado]);
            
            // Depois excluir o comunicado
            $stmt2 = $db->prepare("DELETE FROM comunicados WHERE id = ?");
            $stmt2->execute([$id_comunicado]);
            
            $_SESSION['mensagem'] = "Comunicado excluído com sucesso!";
            registrarLog('excluir_comunicado', "Excluiu comunicado ID: $id_comunicado");
        } else {
            $_SESSION['erro'] = "Comunicado não encontrado!";
        }
    } catch (PDOException $e) {
        $_SESSION['erro'] = "Erro ao excluir comunicado: " . $e->getMessage();
    }
    
    header('Location: comunicados.php');
    exit;
}

// Processar toggle ativo/inativo
if (isset($_GET['toggle']) && isset($_GET['id'])) {
    $id_comunicado = intval($_GET['id']);
    $stmt = $db->prepare("UPDATE comunicados SET ativo = NOT ativo WHERE id = ?");
    $stmt->execute([$id_comunicado]);
    header('Location: comunicados.php');
    exit;
}

// Buscar comunicados - CORRIGIDO (removido o status da consulta)
$stmt = $db->prepare("
    SELECT c.*, 
           COUNT(DISTINCT cl.id) as total_lidos,
           (SELECT COUNT(*) FROM alunos) as total_alunos
    FROM comunicados c
    LEFT JOIN comunicados_lidos cl ON c.id = cl.comunicado_id
    GROUP BY c.id
    ORDER BY c.data_envio DESC
");
$stmt->execute();
$comunicados = $stmt->fetchAll();

$mensagem = $_SESSION['mensagem'] ?? null;
$erro = $_SESSION['erro'] ?? null;
unset($_SESSION['mensagem'], $_SESSION['erro']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunicados - Kindergarten Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .comunicado-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
            position: relative;
        }
        .comunicado-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .comunicado-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        .comunicado-titulo {
            font-size: 1.2rem;
            font-weight: bold;
            color: #ff983e;
        }
        .comunicado-data {
            font-size: 0.85rem;
            color: #999;
        }
        .comunicado-mensagem {
            color: #333;
            line-height: 1.6;
            margin: 15px 0;
        }
        .comunicado-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            padding-top: 10px;
            border-top: 1px solid #f0f0f0;
        }
        .tipo-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .tipo-geral { background: #17a2b8; color: white; }
        .tipo-urgente { background: #dc3545; color: white; }
        .tipo-evento { background: #28a745; color: white; }
        .tipo-aviso { background: #ffc107; color: #333; }
        
        .form-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;
        }
        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }
        .btn-criar {
            background: linear-gradient(135deg, #ff983e, #ff4d4d);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn-criar:hover {
            transform: translateY(-2px);
        }
        .btn-excluir {
            background: #dc3545;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s;
        }
        .btn-excluir:hover {
            background: #c82333;
            transform: translateY(-1px);
        }
        .progress-bar {
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            width: 150px;
        }
        .progress-fill {
            background: #28a745;
            height: 8px;
            border-radius: 10px;
            transition: width 0.3s ease;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #28a745;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .stats-info {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        @media (max-width: 768px) {
            .comunicado-footer {
                flex-direction: column;
                align-items: stretch;
            }
            .stats-info {
                justify-content: space-between;
            }
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
            <li class="active"><a href="comunicados.php"><i class="fas fa-bullhorn"></i> Comunicados</a></li>
            <li><a href="relatorios.php"><i class="fas fa-chart-line"></i> Relatórios</a></li>
            <li><a href="administradores.php"><i class="fas fa-user-cog"></i> Administradores</a></li>
        </ul>
    </div>

    <div class="admin-main">
        <?php if ($mensagem): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($mensagem) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($erro): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-bullhorn"></i> Comunicados</h3>
            </div>
            <div class="card-body">
                <div class="form-container">
                    <h4><i class="fas fa-plus-circle"></i> Novo Comunicado</h4>
                    <form method="POST" id="formComunicado">
                        <div class="form-group">
                            <label>Título *</label>
                            <input type="text" name="titulo" required class="form-control" placeholder="Digite o título do comunicado">
                        </div>
                        <div class="form-group">
                            <label>Tipo</label>
                            <select name="tipo" class="form-control">
                                <option value="geral">📢 Geral</option>
                                <option value="urgente">⚠️ Urgente</option>
                                <option value="evento">🎉 Evento</option>
                                <option value="aviso">ℹ️ Aviso</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Mensagem *</label>
                            <textarea name="mensagem" required class="form-control" placeholder="Digite o conteúdo do comunicado..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>Data de Expiração (opcional)</label>
                            <input type="date" name="data_expiracao" class="form-control">
                            <small>Deixe em branco para não expirar</small>
                        </div>
                        <button type="submit" name="criar_comunicado" class="btn-criar">
                            <i class="fas fa-paper-plane"></i> Publicar Comunicado
                        </button>
                    </form>
                </div>

                <h4><i class="fas fa-list"></i> Comunicados Enviados (<?= count($comunicados) ?>)</h4>
                <?php if (count($comunicados) > 0): ?>
                    <?php foreach($comunicados as $com): 
                        $percentual_lidos = $com['total_alunos'] > 0 ? round(($com['total_lidos'] / $com['total_alunos']) * 100, 1) : 0;
                    ?>
                        <div class="comunicado-card" id="comunicado-<?= $com['id'] ?>">
                            <div class="comunicado-header">
                                <div>
                                    <span class="comunicado-titulo">
                                        <i class="fas fa-tag"></i> <?= htmlspecialchars($com['titulo']) ?>
                                    </span>
                                    <span class="tipo-badge tipo-<?= $com['tipo'] ?>" style="margin-left: 10px;">
                                        <?php
                                        $tipos = ['geral' => 'Geral', 'urgente' => 'Urgente', 'evento' => 'Evento', 'aviso' => 'Aviso'];
                                        echo $tipos[$com['tipo']];
                                        ?>
                                    </span>
                                </div>
                                <div class="btn-group">
                                    <label class="switch">
                                        <input type="checkbox" onchange="toggleComunicado(<?= $com['id'] ?>)" <?= $com['ativo'] ? 'checked' : '' ?>>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="comunicado-mensagem">
                                <?= nl2br(htmlspecialchars($com['mensagem'])) ?>
                            </div>
                            <div class="comunicado-footer">
                                <div class="stats-info">
                                    <div>
                                        <i class="fas fa-eye"></i> 
                                        <strong><?= $com['total_lidos'] ?></strong> de <strong><?= $com['total_alunos'] ?></strong> alunos
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?= $percentual_lidos ?>%"></div>
                                    </div>
                                    <div>
                                        <small><?= $percentual_lidos ?>% de leitura</small>
                                    </div>
                                </div>
                                <div>
                                    <small>
                                        <i class="fas fa-clock"></i> <?= date('d/m/Y H:i', strtotime($com['data_envio'])) ?>
                                    </small>
                                    <?php if($com['data_expiracao']): ?>
                                        <small style="margin-left: 10px;">
                                            <i class="fas fa-calendar-times"></i> Expira: <?= date('d/m/Y', strtotime($com['data_expiracao'])) ?>
                                        </small>
                                    <?php endif; ?>
                                    <button onclick="excluirComunicado(<?= $com['id'] ?>)" class="btn-excluir">
                                        <i class="fas fa-trash"></i> Excluir
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; padding: 40px; color: #666;">
                        <i class="fas fa-bullhorn" style="font-size: 3rem;"></i><br>
                        Nenhum comunicado enviado ainda.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Função para toggle (ligar/desligar) comunicado
        function toggleComunicado(id) {
            if (confirm('Deseja alterar o status deste comunicado?')) {
                window.location.href = 'comunicados.php?toggle=1&id=' + id;
            } else {
                // Recarregar a página para desfazer a mudança visual
                window.location.reload();
            }
        }
        
        // Função aprimorada para excluir comunicado
        function excluirComunicado(id) {
            if (confirm('⚠️ Tem certeza que deseja excluir este comunicado?\n\nEsta ação não pode ser desfeita e todos os registros de leitura serão removidos.')) {
                // Mostrar loading
                const btn = event.target.closest('button');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Excluindo...';
                btn.disabled = true;
                
                // Redirecionar para exclusão
                window.location.href = 'comunicados.php?excluir=1&id=' + id;
            }
        }
        
        // Menu toggle para sidebar responsiva
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        if (menuToggle) {
            menuToggle.addEventListener('click', () => { 
                sidebar.classList.toggle('active'); 
            });
        }
        
        // Auto-fechar alertas após 5 segundos
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
        
        // Prevenir envio duplicado do formulário
        const form = document.getElementById('formComunicado');
        if (form) {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Publicando...';
                }
            });
        }
    </script>
</body>
</html>