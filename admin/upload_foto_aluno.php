<?php
require_once 'config/admin_config.php';
verificarAdmin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
    exit;
}

$aluno_id = intval($_POST['aluno_id'] ?? 0);
if (!$aluno_id) {
    echo json_encode(['success' => false, 'error' => 'ID do aluno não informado']);
    exit;
}

if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== 0) {
    echo json_encode(['success' => false, 'error' => 'Arquivo não enviado']);
    exit;
}

$arquivo = $_FILES['foto'];
$extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
$extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];

if (!in_array($extensao, $extensoes_permitidas)) {
    echo json_encode(['success' => false, 'error' => 'Formato não permitido. Use JPG, PNG ou GIF']);
    exit;
}

// Criar diretório se não existir
$diretorio = '../assets/uploads/alunos/';
if (!file_exists($diretorio)) {
    mkdir($diretorio, 0777, true);
}

$nome_arquivo = 'aluno_' . $aluno_id . '_' . time() . '.' . $extensao;
$caminho_completo = $diretorio . $nome_arquivo;

if (move_uploaded_file($arquivo['tmp_name'], $caminho_completo)) {
    $db = getDB();
    
    // Atualizar o campo de foto na tabela alunos
    $stmt = $db->prepare("UPDATE alunos SET foto_perfil = ? WHERE id = ?");
    $stmt->execute(['assets/uploads/alunos/' . $nome_arquivo, $aluno_id]);
    
    echo json_encode([
        'success' => true, 
        'foto_url' => '../assets/uploads/alunos/' . $nome_arquivo
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Erro ao salvar arquivo']);
}
?>