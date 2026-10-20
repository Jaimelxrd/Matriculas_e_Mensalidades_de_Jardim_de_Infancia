<?php
require_once '../config/admin_config.php';
verificarAdmin();

$db = getDB();
$tipo = $_POST['tipo'] ?? '';
$id = $_POST['id'] ?? 0;

try {
    if ($tipo === 'matricula') {
        $stmt = $db->prepare("UPDATE pagamentos_matricula SET status = 'confirmado', data_pagamento = CURDATE() WHERE id = ?");
        $stmt->execute([$id]);
        
        // Atualizar status do aluno
        $stmt = $db->prepare("
            UPDATE alunos a 
            SET a.status_matricula = 'confirmada', a.status_pagamento = 'aprovado' 
            WHERE a.id = (SELECT p.aluno_id FROM pagamentos_matricula p WHERE p.id = ?)
        ");
        $stmt->execute([$id]);
        
    } elseif ($tipo === 'mensalidade') {
        $stmt = $db->prepare("UPDATE mensalidades SET status = 'paga', data_pagamento = CURDATE() WHERE id = ?");
        $stmt->execute([$id]);
    }
    
    $_SESSION['mensagem'] = "Pagamento confirmado com sucesso!";
} catch (Exception $e) {
    $_SESSION['erro'] = "Erro ao confirmar pagamento";
}

header('Location: ../dashboard.php');
exit;
?>