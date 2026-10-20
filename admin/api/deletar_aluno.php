<?php
require_once '../config/admin_config.php';
verificarAdmin();

$db = getDB();
$id = $_POST['id'] ?? 0;

$stmt = $db->prepare("DELETE FROM alunos WHERE id = ?");
$success = $stmt->execute([$id]);

echo json_encode(['success' => $success]);
?>