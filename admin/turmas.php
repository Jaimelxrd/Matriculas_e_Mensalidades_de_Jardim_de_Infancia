<?php
require_once 'config/admin_config.php';
verificarAdmin();

$db = getDB();

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        switch ($_POST['acao']) {
            case 'criar':
                $stmt = $db->prepare("INSERT INTO turmas (nome, faixa_etaria, periodo, capacidade, descricao) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['nome'],
                    $_POST['faixa_etaria'],
                    $_POST['periodo'],
                    $_POST['capacidade'],
                    $_POST['descricao'] ?? null
                ]);
                $_SESSION['mensagem'] = "Turma criada com sucesso!";
                registrarLog('criar_turma', "Criou turma: {$_POST['nome']}");
                break;
                
            case 'editar':
                $stmt = $db->prepare("UPDATE turmas SET nome = ?, faixa_etaria = ?, periodo = ?, capacidade = ?, descricao = ? WHERE id = ?");
                $stmt->execute([
                    $_POST['nome'],
                    $_POST['faixa_etaria'],
                    $_POST['periodo'],
                    $_POST['capacidade'],
                    $_POST['descricao'] ?? null,
                    $_POST['id']
                ]);
                $_SESSION['mensagem'] = "Turma atualizada com sucesso!";
                registrarLog('editar_turma', "Editou turma ID: {$_POST['id']}");
                break;
                
            case 'excluir':
                // Verificar se tem alunos antes de excluir
                $stmt = $db->prepare("SELECT COUNT(*) as total FROM alunos WHERE turma_id = ?");
                $stmt->execute([$_POST['id']]);
                $total = $stmt->fetch()['total'];
                
                if ($total == 0) {
                    $stmt = $db->prepare("DELETE FROM turmas WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                    $_SESSION['mensagem'] = "Turma excluída com sucesso!";
                    registrarLog('excluir_turma', "Excluiu turma ID: {$_POST['id']}");
                } else {
                    $_SESSION['erro'] = "Não é possível excluir a turma pois existem $total aluno(s) matriculado(s) nela.";
                }
                break;
        }
        header('Location: turmas.php');
        exit;
    }
}

// Buscar todas as turmas
$stmt = $db->query("
    SELECT t.*, 
           (SELECT COUNT(*) FROM alunos WHERE turma_id = t.id) as total_alunos 
    FROM turmas t 
    ORDER BY t.id
");
$turmas = $stmt->fetchAll();

$mensagem = $_SESSION['mensagem'] ?? null;
$erro = $_SESSION['erro'] ?? null;
unset($_SESSION['mensagem'], $_SESSION['erro']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Turmas - Kindergarten Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .modal { 
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(0,0,0,0.5); 
        }
        .modal-content { 
            background: white; 
            margin: 5% auto; 
            width: 90%; 
            max-width: 500px; 
            border-radius: 10px; 
        }
        .modal-header { 
            padding: 20px; 
            background: linear-gradient(135deg, #ff983e, #ff4d4d); 
            color: white; 
            border-radius: 10px 10px 0 0; 
        }
        .modal-body { 
            padding: 20px; 
            max-height: 60vh;
            overflow-y: auto;
        }
        .modal-footer { 
            padding: 20px; 
            border-top: 1px solid #ddd; 
            display: flex; 
            justify-content: flex-end; 
            gap: 10px; 
        }
        .close { 
            float: right; 
            font-size: 28px; 
            cursor: pointer; 
            color: white; 
        }
        .close:hover { opacity: 0.8; }
        
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
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
            min-height: 80px;
        }
        
        .turma-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .turma-card:hover { transform: translateY(-5px); }
        .turma-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #ff983e;
            padding-bottom: 10px;
        }
        .turma-nome { 
            font-size: 1.3rem; 
            font-weight: bold; 
            color: #ff983e; 
        }
        .turma-info { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 15px; 
            margin: 15px 0; 
        }
        .info-item { 
            display: flex; 
            align-items: flex-start; 
            gap: 10px; 
        }
        .info-item i { 
            width: 30px; 
            color: #ff983e; 
            margin-top: 3px;
        }
        .info-item strong { 
            color: #333; 
            display: block;
            font-size: 0.85rem;
        }
        .info-item div {
            flex: 1;
        }
        .info-item .info-text {
            font-size: 0.95rem;
            color: #666;
        }
        .alunos-count { 
            background: #e8f0fe; 
            padding: 5px 10px; 
            border-radius: 20px; 
            font-size: 0.9rem; 
        }
        .btn-group { 
            display: flex; 
            gap: 10px; 
            margin-top: 15px; 
        }
        .btn-sm { 
            padding: 5px 12px; 
            font-size: 0.85rem; 
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
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
            <li class="active"><a href="turmas.php"><i class="fas fa-chalkboard"></i> Turmas</a></li>
            <li><a href="pagamentos.php"><i class="fas fa-credit-card"></i> Pagamentos</a></li>
            <li><a href="mensagens.php"><i class="fas fa-envelope"></i> Mensagens</a></li>
            <li><a href="comunicados.php"><i class="fas fa-bullhorn"></i> Comunicados</a></li>
            <li><a href="relatorios.php"><i class="fas fa-chart-line"></i> Relatórios</a></li>
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
                <h3><i class="fas fa-plus-circle"></i> Nova Turma</h3>
                <button class="btn btn-primary" onclick="abrirModalNova()">
                    <i class="fas fa-plus"></i> Criar Turma
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> Turmas Cadastradas</h3>
            </div>
            <div class="card-body">
                <?php if (count($turmas) > 0): ?>
                    <?php foreach($turmas as $turma): ?>
                    <div class="turma-card">
                        <div class="turma-header">
                            <span class="turma-nome">
                                <i class="fas fa-chalkboard-user"></i> <?= htmlspecialchars($turma['nome']) ?>
                            </span>
                            <span class="alunos-count">
                                <i class="fas fa-users"></i> <?= (int)($turma['total_alunos'] ?? 0) ?> alunos
                            </span>
                        </div>
                        <div class="turma-info">
                            <div class="info-item">
                                <i class="fas fa-calendar"></i>
                                <div>
                                    <strong>Faixa Etária</strong>
                                    <div class="info-text"><?= htmlspecialchars($turma['faixa_etaria'] ?? 'Não definida') ?></div>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <strong>Período</strong>
                                    <div class="info-text"><?= htmlspecialchars($turma['periodo'] ?? 'Não definido') ?></div>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-chair"></i>
                                <div>
                                    <strong>Capacidade</strong>
                                    <div class="info-text"><?= (int)($turma['capacidade'] ?? 0) ?> alunos</div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($turma['descricao'])): ?>
                        <div class="info-item" style="margin-top: 10px;">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>Descrição</strong>
                                <div class="info-text"><?= htmlspecialchars($turma['descricao']) ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="btn-group">
                            <button class="btn btn-warning btn-sm" onclick="editarTurma(<?= htmlspecialchars(json_encode($turma)) ?>)">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <?php if(($turma['total_alunos'] ?? 0) == 0): ?>
                            <button class="btn btn-danger btn-sm" onclick="excluirTurma(<?= $turma['id'] ?>, '<?= htmlspecialchars($turma['nome']) ?>')">
                                <i class="fas fa-trash"></i> Excluir
                            </button>
                            <?php else: ?>
                            <button class="btn btn-secondary btn-sm" disabled title="Não é possível excluir turmas com alunos matriculados">
                                <i class="fas fa-lock"></i> Tem Alunos
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px;">
                        <i class="fas fa-chalkboard" style="font-size: 3rem; color: #ccc;"></i>
                        <p style="margin-top: 20px; color: #666;">Nenhuma turma cadastrada ainda.</p>
                        <button class="btn btn-primary" onclick="abrirModalNova()">
                            <i class="fas fa-plus"></i> Criar primeira turma
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal para Nova/Editar Turma -->
    <div id="turmaModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h3 id="modalTitle"><i class="fas fa-plus"></i> Nova Turma</h3>
            </div>
            <form method="POST" id="turmaForm">
                <div class="modal-body">
                    <input type="hidden" name="acao" id="acao" value="criar">
                    <input type="hidden" name="id" id="turmaId">
                    
                    <div class="form-group">
                        <label for="nome">Nome da Turma *</label>
                        <input type="text" name="nome" id="nome" required class="form-control" placeholder="Ex: Mini Kids, Júnior, Pré-Escolar">
                    </div>
                    
                    <div class="form-group">
                        <label for="faixa_etaria">Faixa Etária *</label>
                        <input type="text" name="faixa_etaria" id="faixa_etaria" placeholder="Ex: 4 a 5 anos" required class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="periodo">Período *</label>
                        <select name="periodo" id="periodo" required class="form-control">
                            <option value="Meio período">Meio período</option>
                            <option value="Integral">Integral</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="capacidade">Capacidade Máxima *</label>
                        <input type="number" name="capacidade" id="capacidade" value="20" required class="form-control" min="1" max="100">
                    </div>
                    
                    <div class="form-group">
                        <label for="descricao">Descrição</label>
                        <textarea name="descricao" id="descricao" rows="3" class="form-control" placeholder="Informações adicionais sobre a turma..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="fecharModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('turmaModal');
        const span = document.getElementsByClassName('close')[0];
        
        function abrirModalNova() {
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus"></i> Nova Turma';
            document.getElementById('acao').value = 'criar';
            document.getElementById('turmaForm').reset();
            document.getElementById('turmaId').value = '';
            modal.style.display = 'block';
        }
        
        function editarTurma(turma) {
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> Editar Turma';
            document.getElementById('acao').value = 'editar';
            document.getElementById('turmaId').value = turma.id;
            document.getElementById('nome').value = turma.nome || '';
            document.getElementById('faixa_etaria').value = turma.faixa_etaria || '';
            document.getElementById('periodo').value = turma.periodo || 'Meio período';
            document.getElementById('capacidade').value = turma.capacidade || 20;
            document.getElementById('descricao').value = turma.descricao || '';
            modal.style.display = 'block';
        }
        
        function excluirTurma(id, nome) {
            if(confirm(`⚠️ Tem certeza que deseja excluir a turma "${nome}"?\n\nEsta ação não pode ser desfeita.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="acao" value="excluir">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function fecharModal() {
            modal.style.display = 'none';
        }
        
        // Fechar modal ao clicar no X
        if (span) {
            span.onclick = function() { fecharModal(); }
        }
        
        // Fechar modal ao clicar fora
        window.onclick = function(event) { 
            if (event.target == modal) {
                fecharModal();
            }
        }
        
        // Menu toggle para mobile
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        if (menuToggle) {
            menuToggle.addEventListener('click', () => { 
                sidebar.classList.toggle('active'); 
            });
        }
    </script>
</body>
</html>