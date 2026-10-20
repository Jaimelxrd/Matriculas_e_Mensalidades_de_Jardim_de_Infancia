<?php
require_once 'config/admin_config.php';
verificarAdmin();

$db = getDB();
$admin_id = $_SESSION[SESSION_ADMIN_ID];

// Processar envio de mensagem
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_mensagem'])) {
    $destinatario_id = $_POST['destinatario_id'];
    $assunto = trim($_POST['assunto']);
    $mensagem = trim($_POST['mensagem']);
    
    if ($destinatario_id && $assunto && $mensagem) {
        $stmt = $db->prepare("
            INSERT INTO mensagens (remetente_id, destinatario_id, remetente_tipo, assunto, mensagem) 
            VALUES (?, ?, 'admin', ?, ?)
        ");
        $stmt->execute([$admin_id, $destinatario_id, $assunto, $mensagem]);
        $_SESSION['mensagem'] = "Mensagem enviada com sucesso!";
        registrarLog('enviar_mensagem', "Enviou mensagem para aluno ID: $destinatario_id");
    } else {
        $_SESSION['erro'] = "Preencha todos os campos obrigatórios";
    }
    header('Location: mensagens.php');
    exit;
}

// Buscar alunos para o select
$alunos = $db->query("SELECT id, nome_completo, codigo_aluno FROM alunos ORDER BY nome_completo")->fetchAll();

// Buscar mensagens enviadas
$stmt = $db->prepare("
    SELECT m.*, 
           a.nome_completo as destinatario_nome,
           a.codigo_aluno as destinatario_codigo
    FROM mensagens m
    JOIN alunos a ON m.destinatario_id = a.id
    WHERE m.remetente_id = ? AND m.remetente_tipo = 'admin'
    ORDER BY m.data_envio DESC
");
$stmt->execute([$admin_id]);
$mensagens_enviadas = $stmt->fetchAll();

// Buscar mensagens recebidas (respostas dos alunos)
$stmt = $db->prepare("
    SELECT m.*, 
           a.nome_completo as remetente_nome,
           a.codigo_aluno as remetente_codigo
    FROM mensagens m
    JOIN alunos a ON m.remetente_id = a.id
    WHERE m.destinatario_id = ? AND m.remetente_tipo = 'aluno'
    ORDER BY m.data_envio DESC
");
$stmt->execute([$admin_id]);
$mensagens_recebidas = $stmt->fetchAll();

// Buscar mensagens de contato do site
$stmt = $db->prepare("
    SELECT * FROM mensagens_contato 
    ORDER BY data_envio DESC
");
$stmt->execute();
$mensagens_contato = $stmt->fetchAll();

// Marcar mensagens como lidas
if (isset($_GET['marcar_lida']) && isset($_GET['id'])) {
    $stmt = $db->prepare("UPDATE mensagens SET lida = TRUE, lida_em = NOW() WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    header('Location: mensagens.php');
    exit;
}

$mensagem = $_SESSION['mensagem'] ?? null;
$erro = $_SESSION['erro'] ?? null;
unset($_SESSION['mensagem'], $_SESSION['erro']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagens - Kindergarten Admin</title>
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
            flex-wrap: wrap;
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
        
        .mensagem-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        .mensagem-card.nao-lida {
            border-left: 4px solid #ff983e;
            background: #fffaf5;
        }
        .mensagem-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            flex-wrap: wrap;
            gap: 10px;
        }
        .mensagem-assunto {
            font-weight: bold;
            color: #ff983e;
            font-size: 1.1rem;
        }
        .mensagem-data {
            font-size: 0.85rem;
            color: #999;
        }
        .mensagem-destinatario {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 5px;
        }
        .mensagem-conteudo {
            color: #333;
            line-height: 1.5;
            margin: 10px 0;
        }
        .mensagem-actions {
            margin-top: 10px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .badge-nao-lida {
            background: #ff983e;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.7rem;
        }
        .badge-respondido {
            background: #28a745;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.7rem;
        }
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
        select.form-control {
            cursor: pointer;
        }
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        .btn-enviar {
            background: linear-gradient(135deg, #ff983e, #ff4d4d);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn-enviar:hover {
            transform: translateY(-2px);
        }
        .responder-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .resposta-form {
            display: none;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #ddd;
        }
        
        /* Modal styles */
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
            max-width: 600px;
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
            <li class="active"><a href="mensagens.php"><i class="fas fa-envelope"></i> Mensagens</a></li>
            <li><a href="comunicados.php"><i class="fas fa-bullhorn"></i> Comunicados</a></li>
            <li><a href="relatorios.php"><i class="fas fa-chart-line"></i> Relatórios</a></li>
            <li><a href="administradores.php"><i class="fas fa-user-cog"></i> Administradores</a></li>
            <li><a href="materiais.php"><i class="fas fa-book"></i> Biblioteca</a></li>
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
                <h3><i class="fas fa-envelope"></i> Central de Mensagens</h3>
            </div>
            <div class="card-body">
                <div class="tabs">
                    <div class="tab active" onclick="mostrarTab('nova')">
                        <i class="fas fa-plus"></i> Nova Mensagem
                    </div>
                    <div class="tab" onclick="mostrarTab('enviadas')">
                        <i class="fas fa-paper-plane"></i> Enviadas
                        <span class="badge"><?= count($mensagens_enviadas) ?></span>
                    </div>
                    <div class="tab" onclick="mostrarTab('recebidas')">
                        <i class="fas fa-inbox"></i> Recebidas
                        <span class="badge"><?= count($mensagens_recebidas) ?></span>
                    </div>
                    <div class="tab" onclick="mostrarTab('contato')">
                        <i class="fas fa-globe"></i> Contato do Site
                        <span class="badge" style="background: #dc3545; color: white;"><?= count($mensagens_contato) ?></span>
                    </div>
                </div>


                <div id="tab-nova" class="tab-content active">
                    <div class="form-container">
                        <form method="POST">
                            <div class="form-group">
                                <label><i class="fas fa-user-graduate"></i> Para (Aluno) *</label>
                                <select name="destinatario_id" required class="form-control">
                                    <option value="">Selecione um aluno</option>
                                    <?php foreach($alunos as $aluno): ?>
                                        <option value="<?= $aluno['id'] ?>">
                                            <?= htmlspecialchars($aluno['nome_completo']) ?> (<?= htmlspecialchars($aluno['codigo_aluno']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-tag"></i> Assunto *</label>
                                <input type="text" name="assunto" required class="form-control" placeholder="Digite o assunto da mensagem">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-comment"></i> Mensagem *</label>
                                <textarea name="mensagem" required class="form-control" placeholder="Digite sua mensagem..."></textarea>
                            </div>
                            <button type="submit" name="enviar_mensagem" class="btn-enviar">
                                <i class="fas fa-paper-plane"></i> Enviar Mensagem
                            </button>
                        </form>
                    </div>
                </div>

                <div id="tab-enviadas" class="tab-content">
                    <?php if (count($mensagens_enviadas) > 0): ?>
                        <?php foreach($mensagens_enviadas as $msg): ?>
                            <div class="mensagem-card">
                                <div class="mensagem-header">
                                    <span class="mensagem-assunto">
                                        <i class="fas fa-tag"></i> <?= htmlspecialchars($msg['assunto']) ?>
                                    </span>
                                    <span class="mensagem-data">
                                        <i class="fas fa-clock"></i> <?= date('d/m/Y H:i', strtotime($msg['data_envio'])) ?>
                                    </span>
                                </div>
                                <div class="mensagem-destinatario">
                                    <i class="fas fa-user"></i> Para: <?= htmlspecialchars($msg['destinatario_nome']) ?> 
                                    (<?= htmlspecialchars($msg['destinatario_codigo']) ?>)
                                </div>
                                <div class="mensagem-conteudo">
                                    <?= nl2br(htmlspecialchars($msg['mensagem'])) ?>
                                </div>
                                <div class="mensagem-actions">
                                    <?php if (!$msg['lida']): ?>
                                        <span class="badge-nao-lida"><i class="fas fa-envelope"></i> Não lida</span>
                                    <?php else: ?>
                                        <span class="badge" style="background: #28a745; color: white;">
                                            <i class="fas fa-check"></i> Lida em <?= date('d/m/Y H:i', strtotime($msg['lida_em'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; padding: 40px; color: #666;">
                            <i class="fas fa-inbox" style="font-size: 3rem;"></i><br>
                            Nenhuma mensagem enviada ainda.
                        </p>
                    <?php endif; ?>
                </div>

                <div id="tab-recebidas" class="tab-content">
                    <?php if (count($mensagens_recebidas) > 0): ?>
                        <?php foreach($mensagens_recebidas as $msg): ?>
                            <div class="mensagem-card <?= !$msg['lida'] ? 'nao-lida' : '' ?>">
                                <div class="mensagem-header">
                                    <span class="mensagem-assunto">
                                        <i class="fas fa-reply"></i> Re: <?= htmlspecialchars($msg['assunto']) ?>
                                    </span>
                                    <span class="mensagem-data">
                                        <i class="fas fa-clock"></i> <?= date('d/m/Y H:i', strtotime($msg['data_envio'])) ?>
                                    </span>
                                </div>
                                <div class="mensagem-destinatario">
                                    <i class="fas fa-user"></i> De: <?= htmlspecialchars($msg['remetente_nome']) ?> 
                                    (<?= htmlspecialchars($msg['remetente_codigo']) ?>)
                                    <?php if (!$msg['lida']): ?>
                                        <span class="badge-nao-lida">Nova</span>
                                    <?php endif; ?>
                                </div>
                                <div class="mensagem-conteudo">
                                    <?= nl2br(htmlspecialchars($msg['mensagem'])) ?>
                                </div>
                                <div class="mensagem-actions">
                                    <a href="?marcar_lida=1&id=<?= $msg['id'] ?>" class="btn btn-success btn-sm">
                                        <i class="fas fa-check"></i> Marcar como lida
                                    </a>
                                    <button class="responder-btn" onclick="mostrarResposta(<?= $msg['id'] ?>, '<?= htmlspecialchars($msg['remetente_nome']) ?>')">
                                        <i class="fas fa-reply"></i> Responder
                                    </button>
                                </div>
                                <div id="resposta-<?= $msg['id'] ?>" class="resposta-form">
                                    <form method="POST">
                                        <input type="hidden" name="destinatario_id" value="<?= $msg['remetente_id'] ?>">
                                        <div class="form-group">
                                            <label>Assunto</label>
                                            <input type="text" name="assunto" value="Re: <?= htmlspecialchars($msg['assunto']) ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Resposta</label>
                                            <textarea name="mensagem" class="form-control" rows="3" placeholder="Digite sua resposta..."></textarea>
                                        </div>
                                        <button type="submit" name="enviar_mensagem" class="btn btn-primary">Enviar Resposta</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; padding: 40px; color: #666;">
                            <i class="fas fa-inbox" style="font-size: 3rem;"></i><br>
                            Nenhuma mensagem recebida ainda.
                        </p>
                    <?php endif; ?>
                </div>

                <div id="tab-contato" class="tab-content">
                    <h4><i class="fas fa-envelope-open-text"></i> Mensagens do Formulário de Contato</h4>
                    
                    <?php if (count($mensagens_contato) > 0): ?>
                        <?php foreach($mensagens_contato as $msg): ?>
                            <div class="mensagem-card <?= $msg['status'] == 'nao_respondido' ? 'nao-lida' : '' ?>">
                                <div class="mensagem-header">
                                    <div>
                                        <span class="mensagem-assunto">
                                            <i class="fas fa-user"></i> <strong><?= htmlspecialchars($msg['nome']) ?></strong> 
                                            (<?= htmlspecialchars($msg['email']) ?>)
                                        </span>
                                        <?php if($msg['assunto']): ?>
                                            <div style="margin-top: 5px;">
                                                <i class="fas fa-tag"></i> <strong>Assunto:</strong> <?= htmlspecialchars($msg['assunto']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <span class="mensagem-data">
                                        <i class="fas fa-clock"></i> <?= date('d/m/Y H:i', strtotime($msg['data_envio'])) ?>
                                    </span>
                                </div>
                                <div class="mensagem-conteudo">
                                    <strong>Mensagem:</strong><br>
                                    <?= nl2br(htmlspecialchars($msg['mensagem'])) ?>
                                </div>
                                <div class="mensagem-actions">
                                    <?php if ($msg['status'] == 'nao_respondido'): ?>
                                        <span class="badge-nao-lida"><i class="fas fa-clock"></i> Aguardando resposta</span>
                                        <button class="responder-btn" onclick="abrirResponderContato(<?= $msg['id'] ?>, '<?= htmlspecialchars($msg['nome']) ?>', '<?= htmlspecialchars($msg['email']) ?>', '<?= htmlspecialchars($msg['assunto']) ?>')">
                                            <i class="fas fa-reply"></i> Responder por Email
                                        </button>
                                    <?php else: ?>
                                        <span class="badge-respondido"><i class="fas fa-check"></i> Respondido em <?= date('d/m/Y H:i', strtotime($msg['resposta_enviada_em'])) ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($msg['resposta']): ?>
                                    <div style="margin-top: 15px; padding: 10px; background: #f0f0f0; border-radius: 8px;">
                                        <strong><i class="fas fa-reply-all"></i> Resposta enviada:</strong>
                                        <p style="margin-top: 8px;"><?= nl2br(htmlspecialchars($msg['resposta'])) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; padding: 40px; color: #666;">
                            <i class="fas fa-inbox" style="font-size: 3rem;"></i><br>
                            Nenhuma mensagem de contato recebida.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div id="responderContatoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="fecharModalResponder()">&times;</span>
                <h3><i class="fas fa-reply"></i> Responder por Email</h3>
            </div>
            <form id="formResponderContato" method="POST" action="api/responder_contato.php">
                <div class="modal-body">
                    <input type="hidden" name="mensagem_id" id="responder_msg_id">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Para:</label>
                        <input type="text" id="responder_nome" class="form-control" readonly>
                        <input type="hidden" id="responder_email" name="email">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Assunto:</label>
                        <input type="text" id="responder_assunto" name="assunto" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-comment"></i> Mensagem de Resposta:</label>
                        <textarea name="resposta" id="resposta_texto" class="form-control" rows="8" required placeholder="Digite sua resposta aqui..."></textarea>
                    </div>
                    <div class="form-group">
                        <small>
                            <i class="fas fa-info-circle"></i> 
                            Esta resposta será enviada diretamente para o email do usuário.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="fecharModalResponder()">Cancelar</button>
                    <button type="submit" name="responder_contato" class="btn btn-primary">Enviar Resposta</button>
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
        
        function mostrarResposta(id, nome) {
            const div = document.getElementById(`resposta-${id}`);
            if (div.style.display === 'none' || div.style.display === '') {
                div.style.display = 'block';
            } else {
                div.style.display = 'none';
            }
        }
        
        function abrirResponderContato(id, nome, email, assunto) {
            document.getElementById('responder_msg_id').value = id;
            document.getElementById('responder_nome').value = nome + ' (' + email + ')';
            document.getElementById('responder_email').value = email;
            document.getElementById('responder_assunto').value = 'Re: ' + (assunto || 'Contato via Site Kindergarten');
            
            const template = `Olá ${nome},

Recebemos sua mensagem e agradecemos pelo contato.

Sua mensagem original:
"${document.querySelector(`#tab-contato .mensagem-card:has(input[value="${id}"]) .mensagem-conteudo`)?.innerText || ''}"

${'-'.repeat(50)}

Atenciosamente,
Equipe Kindergarten

--- 
Este é um email automático do sistema de atendimento.`;
            
            document.getElementById('resposta_texto').value = `Olá ${nome},

Recebemos sua mensagem e agradecemos pelo contato.

Em breve retornaremos com mais informações.

Atenciosamente,
Equipe Kindergarten`;
            
            document.getElementById('responderContatoModal').style.display = 'block';
        }
        
        function fecharModalResponder() {
            document.getElementById('responderContatoModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('responderContatoModal');
            if (event.target == modal) {
                fecharModalResponder();
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