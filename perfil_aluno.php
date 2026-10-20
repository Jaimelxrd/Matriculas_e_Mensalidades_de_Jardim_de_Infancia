<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['aluno_logado']) && (!isset($_GET['codigo']) || !isset($_GET['data']))) {
    header('Location: acesso_perfil.php');
    exit;
}

$db = getDB();

if (isset($_SESSION['aluno_logado'])) {
    $aluno_id = $_SESSION['aluno_logado']['id'];
    $stmt = $db->prepare("SELECT * FROM alunos WHERE id = ?");
    $stmt->execute([$aluno_id]);
    $aluno = $stmt->fetch();
} else {
    $codigo = $_GET['codigo'];
    $data = $_GET['data'];
    $aluno = verificarAcessoAluno($codigo, $data);
    if ($aluno) {
        $_SESSION['aluno_logado'] = [
            'id' => $aluno['id'],
            'codigo' => $aluno['codigo_aluno'],
            'nome' => $aluno['nome_completo'],
            'data_nascimento' => $aluno['data_nascimento']
        ];
        $aluno_id = $aluno['id'];
    } else {
        header('Location: acesso_perfil.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['responder_mensagem'])) {
    $mensagem_id = $_POST['mensagem_id'];
    $resposta = trim($_POST['resposta']);
    
    if ($resposta) {
        $stmt = $db->prepare("SELECT remetente_id FROM mensagens WHERE id = ?");
        $stmt->execute([$mensagem_id]);
        $msg_original = $stmt->fetch();
        
        if ($msg_original) {
            $stmt = $db->prepare("
                INSERT INTO mensagens (remetente_id, destinatario_id, remetente_tipo, assunto, mensagem) 
                VALUES (?, ?, 'aluno', ?, ?)
            ");
            $assunto = "Resposta: " . $_POST['assunto_original'];
            $stmt->execute([$aluno['id'], $msg_original['remetente_id'], $assunto, $resposta]);
            $_SESSION['mensagem_sucesso'] = "Resposta enviada com sucesso!";
        }
    }
    header('Location: perfil_aluno.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['solicitar_pagamento'])) {
    $tipo = $_POST['tipo_pagamento'];
    $item_id = $_POST['item_id'] ?? null;
    
    if ($tipo == 'matricula') {
        $stmt = $db->prepare("SELECT * FROM pagamentos_matricula WHERE aluno_id = ? AND tipo = 'matricula' AND status = 'pendente'");
        $stmt->execute([$aluno['id']]);
        $pendente = $stmt->fetch();
        
        if (!$pendente) {
            $stmt = $db->prepare("INSERT INTO pagamentos_matricula (aluno_id, valor, tipo, status) VALUES (?, 1500, 'matricula', 'pendente')");
            $stmt->execute([$aluno['id']]);
            $_SESSION['mensagem_sucesso'] = "Solicitação de pagamento da matrícula enviada! Aguarde a confirmação do admin.";
        } else {
            $_SESSION['mensagem_erro'] = "Você já possui uma solicitação pendente para a matrícula.";
        }
    } elseif ($tipo == 'mensalidade' && $item_id) {
        
        $stmt = $db->prepare("SELECT status FROM mensalidades WHERE id = ? AND aluno_id = ?");
        $stmt->execute([$item_id, $aluno['id']]);
        $mensalidade = $stmt->fetch();
        
        if ($mensalidade && $mensalidade['status'] == 'pendente') {
            
            $_SESSION['mensagem_sucesso'] = "Solicitação de pagamento enviada! O administrador será notificado.";
        } else {
            $_SESSION['mensagem_erro'] = "Esta mensalidade já foi paga ou não existe.";
        }
    }
    header('Location: perfil_aluno.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_perfil'])) {
    if ($_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $foto = $_FILES['foto_perfil'];
        $extensoes_validas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extensao = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
        
        if (in_array($extensao, $extensoes_validas) && $foto['size'] < 5 * 1024 * 1024) {
            $diretorio = 'assets/uploads/alunos/';
            if (!file_exists($diretorio)) {
                mkdir($diretorio, 0777, true);
            }
            

            if (!empty($aluno['foto_perfil']) && $aluno['foto_perfil'] != 'assets/uploads/default-avatar.png' && file_exists($aluno['foto_perfil'])) {
                unlink($aluno['foto_perfil']);
            }
            
            $nome_arquivo = 'aluno_' . $aluno['id'] . '_' . time() . '.' . $extensao;
            $caminho = $diretorio . $nome_arquivo;
            
            if (move_uploaded_file($foto['tmp_name'], $caminho)) {
                $stmt = $db->prepare("UPDATE alunos SET foto_perfil = ? WHERE id = ?");
                $stmt->execute([$caminho, $aluno['id']]);
                $_SESSION['mensagem_sucesso'] = "Foto de perfil atualizada!";
                header('Location: perfil_aluno.php');
                exit;
            }
        } else {
            $_SESSION['mensagem_erro'] = "Formato inválido ou arquivo muito grande (máx 5MB)";
        }
    } else {
        $_SESSION['mensagem_erro'] = "Erro no upload da foto. Tente novamente.";
    }
    header('Location: perfil_aluno.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM turmas WHERE id = ?");
$stmt->execute([$aluno['turma_id']]);
$turma = $stmt->fetch();

$stmt = $db->prepare("SELECT * FROM pagamentos_matricula WHERE aluno_id = ? AND tipo = 'matricula' ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$aluno['id']]);
$pagamento_matricula = $stmt->fetch();

$status_matricula = $aluno['status_matricula'];
$status_pagamento = $pagamento_matricula['status'] ?? 'pendente';

$stmt = $db->prepare("
    SELECT m.*, 'admin' as tipo 
    FROM mensagens m
    WHERE m.destinatario_id = ? AND m.remetente_tipo = 'admin'
    ORDER BY m.data_envio DESC
");
$stmt->execute([$aluno['id']]);
$mensagens_admin = $stmt->fetchAll();

$stmt = $db->prepare("
    SELECT c.*, 
           (SELECT COUNT(*) FROM comunicados_lidos WHERE comunicado_id = c.id AND aluno_id = ?) as ja_leu
    FROM comunicados c
    WHERE c.ativo = 1 
    AND (c.data_expiracao IS NULL OR c.data_expiracao >= CURDATE())
    ORDER BY c.data_envio DESC
");
$stmt->execute([$aluno['id']]);
$comunicados = $stmt->fetchAll();

if (isset($_GET['marcar_lido']) && isset($_GET['comunicado_id'])) {
    $comunicado_id = $_GET['comunicado_id'];
    $stmt = $db->prepare("INSERT IGNORE INTO comunicados_lidos (comunicado_id, aluno_id) VALUES (?, ?)");
    $stmt->execute([$comunicado_id, $aluno['id']]);
    header('Location: perfil_aluno.php');
    exit;
}

$mes_atual_num = date('n');
$ano_atual = date('Y');
$meses_nomes = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];
$mes_atual_nome = $meses_nomes[$mes_atual_num];

$stmt = $db->prepare("
    SELECT * FROM mensalidades 
    WHERE aluno_id = ? AND mes_referencia = ? AND ano = ? AND status = 'pendente'
");
$stmt->execute([$aluno['id'], $mes_atual_nome, $ano_atual]);
$mensalidade_mes_atual = $stmt->fetch();

$mensalidades_atrasadas = []; 

$divida_total = 0;
$tem_matricula_pendente = false;

if ($status_pagamento != 'confirmado') {
    $divida_total += 1500;
    $tem_matricula_pendente = true;
}

if ($mensalidade_mes_atual) {
    $divida_total += $mensalidade_mes_atual['valor'];
}
foreach ($mensalidades_atrasadas as $mensalidade) {
    $divida_total += $mensalidade['valor'];
}

$foto_perfil = !empty($aluno['foto_perfil']) && file_exists($aluno['foto_perfil']) ? $aluno['foto_perfil'] : 'assets/uploads/default-avatar.png';

if (!file_exists('assets/uploads/default-avatar.png')) {
    $dir = 'assets/uploads/';
    if (!file_exists($dir)) mkdir($dir, 0777, true);
    $svg = '<svg width="150" height="150" xmlns="http://www.w3.org/2000/svg"><circle cx="75" cy="75" r="75" fill="#ff983e"/><text x="75" y="95" font-size="50" text-anchor="middle" fill="white" font-family="Arial">👶</text></svg>';
    file_put_contents($dir . 'default-avatar.png', $svg);
}

$mensagem_sucesso = $_SESSION['mensagem_sucesso'] ?? null;
$mensagem_erro = $_SESSION['mensagem_erro'] ?? null;
unset($_SESSION['mensagem_sucesso'], $_SESSION['mensagem_erro']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Aluno - Kindergarten</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #f4f6fb; color: #333; }
        
        .header {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .logo { font-family: 'Fredoka One', cursive; font-size: 1.5rem; }
        .logo i { color: #ff983e; }
        .btn-sair {
            background: #dc3545;
            color: white;
            padding: 8px 20px;
            border-radius: 8px;
            text-decoration: none;
        }
        .btn-sair:hover { background: #c82333; }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 30px 20px; }
        .profile-grid { display: grid; grid-template-columns: 320px 1fr; gap: 30px; }
        
        .sidebar-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .profile-image {
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, #ff983e, #ff4d4d);
        }
        .profile-image img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
        }
        .profile-image h3 { color: white; margin-top: 15px; font-size: 1.2rem; }
        .profile-image p { color: rgba(255,255,255,0.9); font-size: 0.85rem; }
        .upload-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 20px;
            margin-top: 10px;
            cursor: pointer;
            display: inline-block;
        }
        .upload-btn:hover { background: rgba(255,255,255,0.3); }
        
        .info-section { padding: 20px; }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        .info-item:last-child { border-bottom: none; }
        .info-label { color: #666; font-size: 0.85rem; }
        .info-value { font-weight: 600; color: #333; }
        
        .divida-card {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
        }
        .divida-value { font-size: 1.5rem; font-weight: bold; color: #856404; }
        
        .main-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #ff983e;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ff983e;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .mensagem-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .mensagem-item.nao-lida { background: #fffaf5; border-left: 4px solid #ff983e; }
        .mensagem-header { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .mensagem-assunto { font-weight: bold; color: #ff983e; }
        .mensagem-data { font-size: 0.8rem; color: #999; }
        .mensagem-conteudo { color: #555; line-height: 1.5; margin-bottom: 10px; }
        .btn-responder {
            background: #ff983e;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .resposta-form { display: none; margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd; }
        .resposta-textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        
        .comunicado-item {
            background: #e8f0fe;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #17a2b8;
        }
        .comunicado-item.urgente { background: #f8d7da; border-left-color: #dc3545; }
        .comunicado-item.evento { background: #d4edda; border-left-color: #28a745; }
        .comunicado-titulo { font-weight: bold; margin-bottom: 5px; }
        .comunicado-data { font-size: 0.75rem; color: #666; margin-bottom: 10px; }
        .badge-lido {
            background: #28a745;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.7rem;
            margin-left: 10px;
            text-decoration: none;
        }
        
        .divida-table { width: 100%; border-collapse: collapse; }
        .divida-table th, .divida-table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        .divida-table th { background: #f8f9fa; color: #666; }
        .status-pendente {
            background: #fff3cd;
            color: #856404;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            display: inline-block;
        }
        .status-atrasado {
            background: #f8d7da;
            color: #721c24;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            display: inline-block;
        }
        
        .alert-success { background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; }
        .alert-danger { background: #f8d7da; color: #721c24; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; }
        
        .btn-pagar {
            background: #28a745;
            color: white;
            border: none;
            padding: 6px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .material-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s;
            cursor: pointer;
        }
        .material-item:hover { transform: translateX(5px); background: #f0f0f0; }
        .material-tipo-icon { font-size: 1.5rem; margin-right: 15px; }
        .material-titulo { font-weight: bold; color: #ff983e; }
        .material-descricao { color: #666; font-size: 0.85rem; margin-top: 5px; }
        .material-meta { font-size: 0.75rem; color: #999; margin-top: 8px; }
        .btn-acessar {
            background: #ff983e;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .destaque-badge {
            background: #ffc107;
            color: #333;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.7rem;
            margin-left: 10px;
        }
        
        @media (max-width: 768px) { .profile-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo"><i class="fas fa-graduation-cap"></i> Kindergarten</div>
        <div>
            <a href="#biblioteca" style="color: white; margin-right: 20px;"><i class="fas fa-book"></i> Biblioteca</a>
            <span style="margin-right: 15px;"><i class="fas fa-user"></i> <?= htmlspecialchars($aluno['nome_completo']) ?></span>
            <a href="index.php" class="btn-sair" style="background-color: blue;">voltar</a>
            <a href="logout.php" class="btn-sair"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </div>
    </div>
    
    <div class="container">
        <?php if ($mensagem_sucesso): ?>
            <div class="alert-success"><?= htmlspecialchars($mensagem_sucesso) ?></div>
        <?php endif; ?>
        
        <?php if ($mensagem_erro): ?>
            <div class="alert-danger"><?= htmlspecialchars($mensagem_erro) ?></div>
        <?php endif; ?>
        
        <div class="profile-grid">
            <div>
                <div class="sidebar-card">
                    <div class="profile-image">
                        <img src="<?= $foto_perfil ?>" alt="Foto de Perfil" 
                             onerror="this.onerror=null; this.src='assets/uploads/default-avatar.png';">
                        <h3><?= htmlspecialchars($aluno['nome_completo']) ?></h3>
                        <p>Código: <?= htmlspecialchars($aluno['codigo_aluno']) ?></p>
                        <form method="POST" enctype="multipart/form-data" id="formFoto">
                            <label class="upload-btn">
                                <i class="fas fa-camera"></i> Alterar Foto
                                <input type="file" name="foto_perfil" accept="image/*" style="display: none;" 
                                       onchange="document.getElementById('formFoto').submit()">
                            </label>
                        </form>
                    </div>
                    <div class="info-section">
                        <div class="info-item"><span class="info-label"><i class="fas fa-calendar"></i> Data Nascimento</span><span class="info-value"><?= formatarData($aluno['data_nascimento']) ?></span></div>
                        <div class="info-item"><span class="info-label"><i class="fas fa-chalkboard"></i> Turma</span><span class="info-value"><?= htmlspecialchars($turma['nome'] ?? 'A definir') ?></span></div>
                        <div class="info-item"><span class="info-label"><i class="fas fa-clock"></i> Período</span><span class="info-value"><?= htmlspecialchars($aluno['periodo']) ?></span></div>
                        <div class="info-item"><span class="info-label"><i class="fas fa-user-friends"></i> Encarregado</span><span class="info-value"><?= htmlspecialchars($aluno['nome_responsavel']) ?></span></div>
                        <div class="info-item"><span class="info-label"><i class="fas fa-phone"></i> Telefone</span><span class="info-value"><?= htmlspecialchars($aluno['telefone']) ?></span></div>
                        <div class="info-item"><span class="info-label"><i class="fas fa-envelope"></i> Email</span><span class="info-value"><?= htmlspecialchars($aluno['email']) ?></span></div>
                    </div>
                </div>
                
                <div class="sidebar-card">
                    <div class="info-section">
                        <div class="divida-card">
                            <div class="divida-value"><?= formatarMoeda($divida_total) ?></div>
                            <div style="font-size: 0.8rem;">Valor Total em Dívida</div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div>
          
                <div class="main-card">
                    <div class="card-title">
                    Mensagens da Direção
                        <span style="background: #ff983e; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.7rem;"><?= count($mensagens_admin) ?></span>
                    </div>
                    
                    <?php if (count($mensagens_admin) > 0): ?>
                        <?php foreach($mensagens_admin as $msg): ?>
                            <div class="mensagem-item <?= !$msg['lida'] ? 'nao-lida' : '' ?>">
                                <div class="mensagem-header">
                                    <span class="mensagem-assunto"> <?= htmlspecialchars($msg['assunto']) ?></span>
                                    <span class="mensagem-data"><?= date('d/m/Y H:i', strtotime($msg['data_envio'])) ?></span>
                                </div>
                                <div class="mensagem-conteudo"><?= nl2br(htmlspecialchars($msg['mensagem'])) ?></div>
                                <button class="btn-responder" onclick="mostrarResposta(<?= $msg['id'] ?>)"></i> Responder</button>
                                <div id="resposta-<?= $msg['id'] ?>" class="resposta-form">
                                    <form method="POST">
                                        <input type="hidden" name="mensagem_id" value="<?= $msg['id'] ?>">
                                        <input type="hidden" name="assunto_original" value="<?= htmlspecialchars($msg['assunto']) ?>">
                                        <textarea name="resposta" class="resposta-textarea" rows="3" placeholder="Digite sua resposta..."></textarea>
                                        <button type="submit" name="responder_mensagem" class="btn-responder">Enviar Resposta</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #999; padding: 30px;"><i class="fas fa-inbox" style="font-size: 3rem;"></i><br>Nenhuma mensagem recebida</p>
                    <?php endif; ?>
                </div>
                
                <div class="main-card">
                    <div class="card-title">
                        Comunicados
                        <span style="background: #17a2b8; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.7rem;"><?= count($comunicados) ?></span>
                    </div>
                    
                    <?php if (count($comunicados) > 0): ?>
                        <?php foreach($comunicados as $com): ?>
                            <div class="comunicado-item <?= $com['tipo'] == 'urgente' ? 'urgente' : ($com['tipo'] == 'evento' ? 'evento' : '') ?>">
                                <div class="comunicado-titulo">
                                    <i class="fas fa-<?= $com['tipo'] == 'urgente' ? 'exclamation-triangle' : ($com['tipo'] == 'evento' ? 'calendar-alt' : 'info-circle') ?>"></i>
                                    <?= htmlspecialchars($com['titulo']) ?>
                                    <?php if (!$com['ja_leu']): ?>
                                        <a href="?marcar_lido=1&comunicado_id=<?= $com['id'] ?>" class="badge-lido">Marcar como lido</a>
                                    <?php else: ?>
                                        <span class="badge-lido" style="background: #28a745;"><i class="fas fa-check"></i> Lido</span>
                                    <?php endif; ?>
                                </div>
                                <div class="comunicado-data"><i class="fas fa-clock"></i> <?= date('d/m/Y H:i', strtotime($com['data_envio'])) ?></div>
                                <div><?= nl2br(htmlspecialchars($com['mensagem'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #999; padding: 30px;"><i class="fas fa-bullhorn" style="font-size: 3rem;"></i><br>Nenhum comunicado no momento</p>
                    <?php endif; ?>
                </div>
                
                <div class="main-card" id="biblioteca">
                    <div class="card-title">
                         Biblioteca Digital
                        <span >Recursos Educativos</span>
                    </div>
                    
                    <?php
                    $stmt = $db->prepare("
                        SELECT m.*, c.nome as categoria_nome 
                        FROM materiais m
                        LEFT JOIN categorias c ON m.categoria_id = c.id
                        WHERE m.status = 'ativo' 
                        AND (m.turma_id IS NULL OR m.turma_id = ?)
                        ORDER BY m.destaque DESC, m.data_publicacao DESC
                        LIMIT 10
                    ");
                    $stmt->execute([$aluno['turma_id']]);
                    $materiais = $stmt->fetchAll();
                    ?>
                    
                    <?php if (count($materiais) > 0): ?>
                        <?php foreach($materiais as $material): ?>
                            <div class="material-item" onclick="abrirMaterial(<?= $material['id'] ?>, '<?= htmlspecialchars($material['link_externo']) ?>', '<?= htmlspecialchars($material['arquivo_path']) ?>')">
                                <div style="display: flex; align-items: start; gap: 15px;">
                                    <div class="material-tipo-icon">
                                        <?php $icones = ['livro' => '', 'artigo' => '📄', 'video' => '🎥', 'apostila' => '📖', 'recurso' => '📎'];
                                        echo $icones[$material['tipo']] ?? '📚'; ?>
                                    </div>
                                    <div style="flex: 1;">
                                        <div>
                                            <span class="material-titulo"><?= htmlspecialchars($material['titulo']) ?></span>
                                            <?php if($material['destaque']): ?>
                                                <span class="destaque-badge"><i class="fas fa-star"></i> Destaque</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="material-descricao"><?= htmlspecialchars(substr($material['descricao'] ?? '', 0, 100)) ?>...</div>
                                        <div class="material-meta">
                                            <i class="fas fa-user"></i> <?= htmlspecialchars($material['autor'] ?? 'Equipe Kindergarten') ?> |
                                            <i class="fas fa-tag"></i> <?= htmlspecialchars($material['categoria_nome'] ?? 'Geral') ?> |
                                            <i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($material['data_publicacao'])) ?>
                                        </div>
                                    </div>
                                    <div>
                                        <button class="btn-acessar" onclick="event.stopPropagation(); abrirMaterial(<?= $material['id'] ?>, '<?= htmlspecialchars($material['link_externo']) ?>', '<?= htmlspecialchars($material['arquivo_path']) ?>')">
                                            <i class="fas fa-arrow-right"></i> Acessar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #999; padding: 30px;"><i class="fas fa-book-open" style="font-size: 3rem;"></i><br>Nenhum material disponível no momento.</p>
                    <?php endif; ?>
                </div>

<div class="main-card">
    <div class="card-title">
        <i class="fas fa-credit-card"></i> Situação Financeira
    </div>
    
    <?php if ($divida_total > 0): ?>
        <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div>
                    <strong><i class="fas fa-exclamation-triangle"></i> Você possui débito pendente!</strong>
                    <div style="font-size: 0.85rem;">Regularize sua situação enviando a solicitação de pagamento.</div>
                </div>
                <div style="font-size: 1.3rem; font-weight: bold; color: #856404;"><?= formatarMoeda($divida_total) ?></div>
            </div>
        </div>
    <?php else: ?>
        <div style="background: #d4edda; border: 1px solid #28a745; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
            <strong><i class="fas fa-check-circle"></i> Situação Regularizada!</strong>
            <div style="font-size: 0.85rem;">Você está com todas as obrigações em dia.</div>
        </div>
    <?php endif; ?>
    
    <table class="divida-table">
        <thead>
            <tr><th>Descrição</th><th>Vencimento</th><th>Valor</th><th>Status</th><th>Ação</th></tr>
        </thead>
        <tbody>
            <?php if ($status_pagamento != 'confirmado'): ?>
            <tr style="background: #fff3cd;">
                <td><strong>Taxa de Matrícula</strong></td>
                <td><?= date('d/m/Y', strtotime($aluno['data_matricula'])) ?></td>
                <td><strong><?= formatarMoeda(1500) ?></strong></td>
                <td><span class="status-pendente"><i class="fas fa-clock"></i> Pendente</span></td>
                <td>
                    <form method="POST" style="margin: 0;">
                        <input type="hidden" name="tipo_pagamento" value="matricula">
                        <button type="submit" name="solicitar_pagamento" class="btn-pagar" 
                                onclick="return confirm('Enviar solicitação de pagamento da matrícula? O administrador irá confirmar.')">
                            <i class="fas fa-paper-plane"></i> Solicitar Pagamento
                        </button>
                    </form>
                </td>
            </tr>
            <?php endif; ?>
 
            <?php if ($mensalidade_mes_atual): ?>
            <tr>
                <td>Mensalidade - <?= $mensalidade_mes_atual['mes_referencia'] ?> <?= $mensalidade_mes_atual['ano'] ?>
                    <span style="background: #ffc107; color: #333; padding: 2px 6px; border-radius: 10px; font-size: 0.65rem; margin-left: 8px;">
                        <i class="fas fa-calendar"></i> Mês Atual
                    </span>
                </td>
                <td><?= date('d/m/Y', strtotime($mensalidade_mes_atual['data_vencimento'])) ?></td>
                <td><?= formatarMoeda($mensalidade_mes_atual['valor']) ?></td>
                <td><span class="status-pendente"><i class="fas fa-clock"></i> Pendente</span></td>
                <td>
                    <form method="POST" style="margin: 0;">
                        <input type="hidden" name="tipo_pagamento" value="mensalidade">
                        <input type="hidden" name="item_id" value="<?= $mensalidade_mes_atual['id'] ?>">
                        <button type="submit" name="solicitar_pagamento" class="btn-pagar"
                                onclick="return confirm('Enviar solicitação de pagamento da mensalidade atual? O administrador irá confirmar.')">
                            <i class="fas fa-paper-plane"></i> Solicitar Pagamento
                        </button>
                    </form>
                </td>
            </tr>
            <?php endif; ?>

            <?php if (!$tem_matricula_pendente && !$mensalidade_mes_atual): ?>
            <tr>
                <td colspan="5" style="text-align: center; color: #28a745; padding: 30px;">
                    <i class="fas fa-check-circle fa-2x"></i>
                    <p style="margin-top: 10px;">Parabéns! Você está com todas as obrigações em dia!</p>
                    <p style="font-size: 0.85rem;">Não há débitos pendentes no momento.</p>
                 </td>
             </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div style="margin-top: 15px; padding: 10px; background: #e8f0fe; border-radius: 8px; font-size: 0.8rem;">
        <i class="fas fa-info-circle"></i> 
        <strong>Como funciona?</strong> Clique em "Solicitar Pagamento" para cada item. 
        O administrador irá validar e confirmar o pagamento. Você será notificado quando for aprovado.
        <br><br>
        <strong>Nota:</strong> Apenas a mensalidade do mês atual é exibida. Mensalidades de meses anteriores não são cobradas retroativamente.
    </div>
</div>
            </div>
        </div>
    </div>
    
    <script>
        function mostrarResposta(id) {
            const div = document.getElementById(`resposta-${id}`);
            div.style.display = div.style.display === 'none' || div.style.display === '' ? 'block' : 'none';
        }
        
        function abrirMaterial(id, linkExterno, arquivoPath) {
            fetch('api/registrar_acesso.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'material_id=' + id
            }).catch(err => console.log('Erro:', err));
            
            if (linkExterno && linkExterno !== '') {
                window.open(linkExterno, '_blank');
            } else if (arquivoPath && arquivoPath !== '') {
                let caminhoCorrigido = arquivoPath;
                if (caminhoCorrigido.startsWith('../')) caminhoCorrigido = caminhoCorrigido.substring(3);
                if (!caminhoCorrigido.startsWith('assets/')) caminhoCorrigido = 'assets/' + caminhoCorrigido;
                window.open(caminhoCorrigido, '_blank');
            } else {
                alert('Material não disponível.');
            }
        }
    </script>
</body>
</html>