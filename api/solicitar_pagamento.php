<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    if ($tipo_pagamento === 'matricula') {
        $stmt = $db->prepare("SELECT * FROM pagamentos_matricula WHERE aluno_id = ? AND tipo = 'matricula' AND status = 'pendente'");
        $stmt->execute([$aluno_id]);
        $pendente = $stmt->fetch();
        
        if ($pendente) {
            $_SESSION['mensagem_erro'] = "Você já possui uma solicitação de pagamento de matrícula pendente. Aguarde a confirmação do administrador.";
        } else {
            $stmt = $db->prepare("INSERT INTO pagamentos_matricula (aluno_id, valor, tipo, status) VALUES (?, 1500.00, 'matricula', 'pendente')");
            $stmt->execute([$aluno_id]);
            $_SESSION['mensagem_sucesso'] = "Solicitação de pagamento da matrícula enviada com sucesso! Realize o pagamento e aguarde confirmação.";
        }
        
    } elseif ($tipo_pagamento === 'mensalidade' && $item_id) {
        $stmt = $db->prepare("SELECT status FROM mensalidades WHERE id = ? AND aluno_id = ?");
        $stmt->execute([$item_id, $aluno_id]);
        $mensalidade = $stmt->fetch();
        
        if ($mensalidade && $mensalidade['status'] == 'pendente') {
            $_SESSION['mensagem_sucesso'] = "Solicitação de pagamento da mensalidade enviada com sucesso! Realize o pagamento e aguarde confirmação.";
        } else {
            $_SESSION['mensagem_erro'] = "Esta mensalidade já foi paga ou não existe.";
        }
    }
    
} catch (Exception $e) {
    $_SESSION['mensagem_erro'] = "Erro ao processar solicitação: " . $e->getMessage();
}

header('Location: ../perfil_aluno.php');
exit;
?>