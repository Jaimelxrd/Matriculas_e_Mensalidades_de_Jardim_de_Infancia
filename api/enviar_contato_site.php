<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Pegar dados do POST
$nome = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$mensagem = trim($_POST['message'] ?? '');

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
        INSERT INTO mensagens_contato (nome, email, mensagem, status) 
        VALUES (?, ?, ?, 'nao_respondido')
    ");
    $stmt->execute([$nome, $email, $mensagem]);
    
    echo json_encode(['success' => true, 'message' => 'Mensagem enviada com sucesso! Responderemos em breve.']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao enviar mensagem: ' . $e->getMessage()]);
}
exit;
?>