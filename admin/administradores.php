<?php
require_once 'config/admin_config.php';
verificarNivel('super_admin'); // Apenas super admin pode acessar

$db = getDB();

// Processar criação de novo admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['criar_admin'])) {
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $usuario = trim($_POST['usuario']);
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $nivel = $_POST['nivel'];
        
        // Verificar se usuário ou email já existe
        $stmt = $db->prepare("SELECT id FROM administradores WHERE usuario = ? OR email = ?");
        $stmt->execute([$usuario, $email]);
        
        if ($stmt->fetch()) {
            $_SESSION['erro'] = "Usuário ou email já cadastrado!";
        } else {
            $stmt = $db->prepare("INSERT INTO administradores (nome, email, usuario, senha, nivel) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $usuario, $senha, $nivel]);
            $_SESSION['mensagem'] = "Administrador criado com sucesso!";
            registrarLog('criar_admin', "Criou novo administrador: $usuario");
        }
        header('Location: administradores.php');
        exit;
    }
    
    if (isset($_POST['editar_admin'])) {
        $id = $_POST['id'];
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $nivel = $_POST['nivel'];
        $status = $_POST['status'];
        
        $stmt = $db->prepare("UPDATE administradores SET nome = ?, email = ?, nivel = ?, status = ? WHERE id = ?");
        $stmt->execute([$nome, $email, $nivel, $status, $id]);
        $_SESSION['mensagem'] = "Administrador atualizado!";
        registrarLog('editar_admin', "Editou administrador ID: $id");
        header('Location: administradores.php');
        exit;
    }
    
    if (isset($_POST['resetar_senha'])) {
        $id = $_POST['id'];
        $nova_senha = $_POST['nova_senha'];
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        
        $stmt = $db->prepare("UPDATE administradores SET senha = ? WHERE id = ?");
        $stmt->execute([$senha_hash, $id]);
        $_SESSION['mensagem'] = "Senha resetada com sucesso!";
        registrarLog('resetar_senha', "Resetou senha do admin ID: $id");
        header('Location: administradores.php');
        exit;
    }
    
    if (isset($_POST['excluir_admin'])) {
        $id = $_POST['id'];
        
        // Não permitir excluir o próprio admin
        if ($id == $_SESSION[SESSION_ADMIN_ID]) {
            $_SESSION['erro'] = "Você não pode excluir a si mesmo!";
        } else {
            $stmt = $db->prepare("DELETE FROM administradores WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['mensagem'] = "Administrador excluído!";
            registrarLog('excluir_admin', "Excluiu administrador ID: $id");
        }
        header('Location: administradores.php');
        exit;
    }
}

// Buscar todos administradores
$stmt = $db->query("SELECT * FROM administradores ORDER BY nivel DESC, created_at DESC");
$administradores = $stmt->fetchAll();

// Buscar logs de atividades
$stmt = $db->query("
    SELECT l.*, a.nome as admin_nome 
    FROM admin_logs l 
    LEFT JOIN administradores a ON l.admin_id = a.id 
    ORDER BY l.data_hora DESC 
    LIMIT 50
");
$logs = $stmt->fetchAll();

$mensagem = $_SESSION['mensagem'] ?? null;
$erro = $_SESSION['erro'] ?? null;
unset($_SESSION['mensagem'], $_SESSION['erro']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administradores - Kindergarten Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
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
            transition: all 0.3s;
        }
        .tab.active {
            background: #ff983e;
            color: white;
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
        .admin-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        .admin-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .admin-nome {
            font-size: 1.1rem;
            font-weight: bold;
            color: #ff983e;
        }
        .admin-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin: 15px 0;
        }
        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }
        .info-item i {
            width: 25px;
            color: #ff983e;
        }
        .nivel-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .nivel-super_admin { background: #dc3545; color: white; }
        .nivel-admin { background: #17a2b8; color: white; }
        .nivel-secretaria { background: #28a745; color: white; }
        .status-ativo { color: #28a745; }
        .status-inativo { color: #dc3545; }
        
        .form-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
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
        .btn-primary {
            background: #ff983e;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-primary:hover {
            background: #e8821a;
        }
        .btn-sm {
            padding: 5px 10px;
            font-size: 0.85rem;
        }
        .log-table {
            font-size: 0.85rem;
        }
        .log-table td {
            padding: 8px;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-school"></i>
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
            <li><a href="relatorios.php"><i class="fas fa-chart-line"></i> Relatórios</a></li>
            <li class="active"><a href="administradores.php"><i class="fas fa-user-cog"></i> Administradores</a></li>
        </ul>
    </div>

    <div class="admin-main">
        <?php if ($mensagem): ?>
            <div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>
        
        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <div class="tabs">
            <div class="tab active" onclick="mostrarTab('admins')">
                <i class="fas fa-users"></i> Administradores
            </div>
            <div class="tab" onclick="mostrarTab('novo')">
                <i class="fas fa-user-plus"></i> Novo Admin
            </div>
            <div class="tab" onclick="mostrarTab('logs')">
                <i class="fas fa-history"></i> Logs de Atividades
            </div>
        </div>

        <!-- Lista de Administradores -->
        <div id="tab-admins" class="tab-content active">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-list"></i> Administradores do Sistema</h3>
                </div>
                <div class="card-body">
                    <?php foreach($administradores as $admin): ?>
                        <div class="admin-card">
                            <div class="admin-header">
                                <div>
                                    <span class="admin-nome">
                                        <i class="fas fa-user-circle"></i> <?= htmlspecialchars($admin['nome']) ?>
                                    </span>
                                    <span class="nivel-badge nivel-<?= $admin['nivel'] ?>" style="margin-left: 10px;">
                                        <?php
                                        $niveis = ['super_admin' => 'Super Admin', 'admin' => 'Admin', 'secretaria' => 'Secretaria'];
                                        echo $niveis[$admin['nivel']];
                                        ?>
                                    </span>
                                </div>
                                <span class="<?= $admin['status'] == 'ativo' ? 'status-ativo' : 'status-inativo' ?>">
                                    <i class="fas fa-circle"></i> <?= ucfirst($admin['status']) ?>
                                </span>
                            </div>
                            <div class="admin-info">
                                <div class="info-item">
                                    <i class="fas fa-envelope"></i>
                                    <span><?= htmlspecialchars($admin['email']) ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-user"></i>
                                    <span>Usuário: <?= htmlspecialchars($admin['usuario']) ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>Criado: <?= date('d/m/Y', strtotime($admin['created_at'])) ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-clock"></i>
                                    <span>Último acesso: <?= $admin['ultimo_acesso'] ? date('d/m/Y H:i', strtotime($admin['ultimo_acesso'])) : 'Nunca' ?></span>
                                </div>
                            </div>
                            <div class="btn-group" style="display: flex; gap: 10px; margin-top: 10px;">
                                <button class="btn btn-warning btn-sm" onclick="editarAdmin(<?= htmlspecialchars(json_encode($admin)) ?>)">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button class="btn btn-info btn-sm" onclick="resetarSenha(<?= $admin['id'] ?>, '<?= htmlspecialchars($admin['nome']) ?>')">
                                    <i class="fas fa-key"></i> Resetar Senha
                                </button>
                                <?php if ($admin['id'] != $_SESSION[SESSION_ADMIN_ID]): ?>
                                    <button class="btn btn-danger btn-sm" onclick="excluirAdmin(<?= $admin['id'] ?>, '<?= htmlspecialchars($admin['nome']) ?>')">
                                        <i class="fas fa-trash"></i> Excluir
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Criar Novo Admin -->
        <div id="tab-novo" class="tab-content">
            <div class="form-container">
                <h4><i class="fas fa-user-plus"></i> Criar Novo Administrador</h4>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nome Completo *</label>
                            <input type="text" name="nome" required class="form-control" placeholder="Digite o nome completo">
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" required class="form-control" placeholder="email@exemplo.com">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Usuário *</label>
                            <input type="text" name="usuario" required class="form-control" placeholder="Nome de usuário">
                        </div>
                        <div class="form-group">
                            <label>Senha *</label>
                            <input type="password" name="senha" required class="form-control" placeholder="Senha de acesso">
                        </div>
                        <div class="form-group">
                            <label>Nível *</label>
                            <select name="nivel" required class="form-control">
                                <option value="secretaria">Secretaria</option>
                                <option value="admin">Administrador</option>
                                <option value="super_admin">Super Administrador</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="criar_admin" class="btn-primary">
                        <i class="fas fa-save"></i> Criar Administrador
                    </button>
                </form>
            </div>
        </div>

        <!-- Logs de Atividades -->
        <div id="tab-logs" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-history"></i> Histórico de Atividades</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table log-table">
                            <thead>
                                <tr>
                                    <th>Data/Hora</th>
                                    <th>Administrador</th>
                                    <th>Ação</th>
                                    <th>Detalhes</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($logs as $log): ?>
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
    </div>

    <!-- Modal Editar Admin -->
    <div id="editModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="fecharModal('editModal')">&times;</span>
                <h3><i class="fas fa-edit"></i> Editar Administrador</h3>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" name="nome" id="edit_nome" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="edit_email" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Nível</label>
                        <select name="nivel" id="edit_nivel" class="form-control">
                            <option value="secretaria">Secretaria</option>
                            <option value="admin">Administrador</option>
                            <option value="super_admin">Super Administrador</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit_status" class="form-control">
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="fecharModal('editModal')">Cancelar</button>
                    <button type="submit" name="editar_admin" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Resetar Senha -->
    <div id="resetModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="fecharModal('resetModal')">&times;</span>
                <h3><i class="fas fa-key"></i> Resetar Senha</h3>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="reset_id">
                    <p>Resetar senha do administrador: <strong id="reset_nome"></strong></p>
                    <div class="form-group">
                        <label>Nova Senha</label>
                        <input type="password" name="nova_senha" required class="form-control" placeholder="Digite a nova senha">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="fecharModal('resetModal')">Cancelar</button>
                    <button type="submit" name="resetar_senha" class="btn btn-warning">Resetar Senha</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function mostrarTab(tab) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab').forEach(el => el.classList.remove('active'));
            document.getElementById(`tab-${tab}`).classList.add('active');
            event.target.closest('.tab').classList.add('active');
        }
        
        function editarAdmin(admin) {
            document.getElementById('edit_id').value = admin.id;
            document.getElementById('edit_nome').value = admin.nome;
            document.getElementById('edit_email').value = admin.email;
            document.getElementById('edit_nivel').value = admin.nivel;
            document.getElementById('edit_status').value = admin.status;
            document.getElementById('editModal').style.display = 'block';
        }
        
        function resetarSenha(id, nome) {
            document.getElementById('reset_id').value = id;
            document.getElementById('reset_nome').innerHTML = nome;
            document.getElementById('resetModal').style.display = 'block';
        }
        
        function excluirAdmin(id, nome) {
            if(confirm(`Tem certeza que deseja excluir o administrador "${nome}"?\n\nEsta ação não pode ser desfeita.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `<input type="hidden" name="id" value="${id}"><input type="hidden" name="excluir_admin" value="1">`;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function fecharModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
        
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        if (menuToggle) {
            menuToggle.addEventListener('click', () => { sidebar.classList.toggle('active'); });
        }
    </script>
</body>
</html>