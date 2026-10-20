<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../acesso_perfil.php');
    exit;
}

$codigo = $_POST['codigo_aluno'] ?? '';
$data_nascimento = $_POST['data_nascimento'] ?? '';

$aluno = verificarAcessoAluno($codigo, $data_nascimento);

if ($aluno) {
    $_SESSION['aluno_logado'] = [
        'id' => $aluno['id'],
        'codigo' => $aluno['codigo_aluno'],
        'nome' => $aluno['nome_completo'],
        'data_nascimento' => $aluno['data_nascimento']
    ];
    
    header('Location: ../perfil_aluno.php');
} else {
    $_SESSION['erro_acesso'] = 'Código ou data de nascimento incorrectos';
    header('Location: ../acesso_perfil.php');
}
exit;
?>