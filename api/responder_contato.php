<?php
require_once '../config/admin_config.php';
verificarAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin/mensagens.php');
    exit;
}

$mensagem_id = $_POST['mensagem_id'] ?? 0;
$assunto = trim($_POST['assunto'] ?? '');
$resposta_texto = trim($_POST['resposta'] ?? '');
$admin_id = $_SESSION[SESSION_ADMIN_ID];

if (empty($resposta_texto)) {
    $_SESSION['erro'] = "Digite uma resposta";
    header('Location: ../admin/mensagens.php');
    exit;
}

$db = getDB();

$stmt = $db->prepare("SELECT nome, email, assunto FROM mensagens_contato WHERE id = ?");
$stmt->execute([$mensagem_id]);
$mensagem = $stmt->fetch();

if (!$mensagem) {
    $_SESSION['erro'] = "Mensagem não encontrada";
    header('Location: ../admin/mensagens.php');
    exit;
}

$email_destino = $mensagem['email'];
$assunto_email = $assunto ?: "Resposta: " . ($mensagem['assunto'] ?: "Contato Kindergarten");
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: contato@kindergarten.com" . "\r\n";
$headers .= "Reply-To: contato@kindergarten.com" . "\r\n";

$email_html = "
<!DOCTYPE html>
<html>
<head>
    <title>Resposta do Kindergarten</title>
</head>
<body>
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
        <div style='text-align: center; padding: 20px; background: linear-gradient(135deg, #ff983e, #ff4d4d); color: white; border-radius: 10px 10px 0 0;'>
            <h2>Kindergarten</h2>
            <p>Resposta ao seu contato</p>
        </div>
        <div style='background: #f9f9f9; padding: 20px; border-radius: 0 0 10px 10px;'>
            <p>Olá <strong>" . htmlspecialchars($mensagem['nome']) . "</strong>,</p>
            <div style='background: white; padding: 15px; border-radius: 8px; margin: 15px 0;'>
                " . nl2br(htmlspecialchars($resposta_texto)) . "
            </div>
            <hr>
            <p style='color: #666; font-size: 12px;'>
                Este é um email automático do sistema do Kindergarten.<br>
                Para mais informações, entre em contato pelo telefone: +258 84 785 2208
            </p>
        </div>
    </div>
</body>
</html>
";

$email_enviado = mail($email_destino, $assunto_email, $email_html, $headers);

if ($email_enviado) {
    $stmt = $db->prepare("
        UPDATE mensagens_contato 
        SET status = 'respondido', 
            resposta = ?, 
            respondida_por = ?, 
            resposta_enviada_em = NOW() 
        WHERE id = ?
    ");
    $stmt->execute([$resposta_texto, $admin_id, $mensagem_id]);
    
    registrarLog('responder_contato', "Respondeu mensagem de: {$mensagem['nome']}");
    $_SESSION['mensagem'] = "Resposta enviada com sucesso para " . htmlspecialchars($mensagem['nome']);
} else {
    $_SESSION['erro'] = "Erro ao enviar email. Tente novamente.";
}

header('Location: ../admin/mensagens.php');
exit;
?>