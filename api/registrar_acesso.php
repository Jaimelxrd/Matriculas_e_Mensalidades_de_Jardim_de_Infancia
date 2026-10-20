<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['aluno_logado'])) {
    http_response_code(401);
    exit;
}

$aluno_id = $_SESSION['aluno_logado']['id'];
$material_id = $_POST['material_id'] ?? 0;

if ($material_id) {
    $db = getDB();
    
    $stmt = $db->prepare("INSERT INTO materiais_acessos (material_id, aluno_id, tipo_acesso) VALUES (?, ?, 'visualizacao')");
    $stmt->execute([$material_id, $aluno_id]);
    
    $stmt = $db->prepare("UPDATE materiais SET visualizacoes = visualizacoes + 1 WHERE id = ?");
    $stmt->execute([$material_id]);
}

echo json_encode(['success' => true]);
?>