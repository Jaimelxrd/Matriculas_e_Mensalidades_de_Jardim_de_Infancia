<?php
require_once '../config/admin_config.php';
verificarAdmin();

$db = getDB();
$id = $_POST['id'] ?? 0;

$stmt = $db->prepare("UPDATE alunos SET status_matricula = 'confirmada', status_pagamento = 'aprovado' WHERE id = ?");
$success = $stmt->execute([$id]);

echo json_encode(['success' => $success]);
?>