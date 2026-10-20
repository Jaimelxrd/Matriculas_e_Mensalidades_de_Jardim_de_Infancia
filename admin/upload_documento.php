<?php
require_once 'config/admin_config.php';
verificarAdmin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
    exit;
}

$aluno_id = intval($_POST['aluno_id'] ?? 0);
$tipo_documento = $_POST['tipo_documento'] ?? '';

if (!$aluno_id || !$tipo_documento || !isset($_FILES['documento'])) {
    echo json_encode(['success' => false, 'error' => 'Dados incompletos']);
    exit;
}

$arquivo = $_FILES['documento'];
$extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
$extensoes_permitidas = ['pdf', 'jpg', 'jpeg', 'png'];

if (!in_array(strtolower($extensao), $extensoes_permitidas)) {
    echo json_encode(['success' => false, 'error' => 'Tipo de arquivo não permitido']);
    exit;
}

// Buscar código do aluno
$db = getDB();
$stmt = $db->prepare("SELECT codigo_estudante FROM alunos WHERE id = ?");
$stmt->execute([$aluno_id]);
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$aluno) {
    echo json_encode(['success' => false, 'error' => 'Aluno não encontrado']);
    exit;
}

$diretorio = "../uploads/documentos/" . $aluno['codigo_estudante'] . "/";
if (!file_exists($diretorio)) {
    mkdir($diretorio, 0777, true);
}

$nome_arquivo = $tipo_documento . "_" . time() . "." . $extensao;
$caminho_completo = $diretorio . $nome_arquivo;

if (move_uploaded_file($arquivo['tmp_name'], $caminho_completo)) {
    $stmt = $db->prepare("
        INSERT INTO documentos (aluno_id, tipo_documento, nome_arquivo, caminho_arquivo, status_verificacao) 
        VALUES (?, ?, ?, ?, 'pendente')
    ");
    
    if ($stmt->execute([$aluno_id, $tipo_documento, $nome_arquivo, $caminho_completo])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro ao salvar no banco']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Erro ao fazer upload']);
}
?>