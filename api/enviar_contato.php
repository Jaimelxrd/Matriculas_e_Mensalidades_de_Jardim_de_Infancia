<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$assunto = trim($_POST['assunto'] ?? '');
$mensagem = trim($_POST['mensagem'] ?? '');

$erros = [];

if (empty($nome)) {
    $erros[] = "Nome é obrigatório";
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = "Email válido é obrigatório";
}
if (empty($mensagem)) {
    $erros[] = "Mensagem é obrigatória";
}

if (count($erros) > 0) {
    echo json_encode(['success' => false, 'message' => implode(", ", $erros)]);
    exit;
}

try {
    $db = getDB();
    
    $stmt = $db->query("SHOW TABLES LIKE 'mensagens_contato'");
    if ($stmt->rowCount() == 0) {
        $db->exec("
            CREATE TABLE IF NOT EXISTS mensagens_contato (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                assunto VARCHAR(200),
                mensagem TEXT NOT NULL,
                status ENUM('nao_respondido', 'respondido') DEFAULT 'nao_respondido',
                resposta TEXT,
                respondida_por INT,
                resposta_enviada_em TIMESTAMP NULL,
                data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }
    
    $stmt = $db->prepare("
        INSERT INTO mensagens_contato (nome, email, assunto, mensagem, status) 
        VALUES (?, ?, ?, ?, 'nao_respondido')
    ");
    $stmt->execute([$nome, $email, $assunto, $mensagem]);
    
    $assunto_email = "Nova mensagem de contato - Kindergarten";
    $mensagem_email = "
    <html>
    <head>
        <title>Nova mensagem de contato</title>
    </head>
    <body>
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #ff983e;'>Nova mensagem do site</h2>
            <p><strong>Nome:</strong> $nome</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Assunto:</strong> " . ($assunto ?: 'Sem assunto') . "</p>
            <p><strong>Mensagem:</strong><br>" . nl2br($mensagem) . "</p>
            <hr>
            <p style='color: #666; font-size: 12px;'>Responda esta mensagem diretamente pelo painel administrativo.</p>
        </div>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: contato@kindergarten.com" . "\r\n";
    
    @mail('admin@kindergarten.com', $assunto_email, $mensagem_email, $headers);
    
    echo json_encode(['success' => true, 'message' => 'Mensagem enviada com sucesso! Responderemos em breve.']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar mensagem: ' . $e->getMessage()]);
}
exit;
?>