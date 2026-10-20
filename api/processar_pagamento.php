<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se está logado
if (!isset($_SESSION['aluno_logado'])) {
    header('Location: ../acesso_perfil.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../perfil_aluno.php');
    exit;
}

$aluno_id = $_SESSION['aluno_logado']['id'];
$tipo_pagamento = $_POST['tipo_pagamento'] ?? '';
$item_id = $_POST['item_id'] ?? null;

$db = getDB();

try {
    $db->beginTransaction();
    
    if ($tipo_pagamento === 'matricula') {
        $stmt = $db->prepare("UPDATE pagamentos_matricula SET status = 'confirmado', data_pagamento = CURDATE() WHERE aluno_id = ? AND tipo = 'matricula'");
        $stmt->execute([$aluno_id]);
        
        $stmt = $db->prepare("UPDATE alunos SET status_matricula = 'confirmada', status_pagamento = 'aprovado' WHERE id = ?");
        $stmt->execute([$aluno_id]);
        
        $_SESSION['mensagem_sucesso'] = "Pagamento da matrícula confirmado com sucesso!";
        
    } elseif ($tipo_pagamento === 'mensalidade' && $item_id) {
        $stmt = $db->prepare("UPDATE mensalidades SET status = 'paga', data_pagamento = CURDATE() WHERE id = ? AND aluno_id = ?");
        $stmt->execute([$item_id, $aluno_id]);
        
        $_SESSION['mensagem_sucesso'] = "Pagamento da mensalidade confirmado com sucesso!";
    }
    
    $db->commit();
    
} catch (Exception $e) {
    $db->rollBack();
    $_SESSION['mensagem_erro'] = "Erro ao processar pagamento: " . $e->getMessage();
}

header('Location: ../perfil_aluno.php');
exit;
?>