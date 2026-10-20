<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

try {
    $db = getDB();
    $db->beginTransaction();
    
    $certidao_path = isset($_FILES['certidao']) && $_FILES['certidao']['error'] === UPLOAD_ERR_OK 
        ? uploadArquivo($_FILES['certidao'], 'documentos/') : null;
    
    $bi_path = isset($_FILES['bi_responsavel']) && $_FILES['bi_responsavel']['error'] === UPLOAD_ERR_OK 
        ? uploadArquivo($_FILES['bi_responsavel'], 'documentos/') : null;
    
    $foto_path = isset($_FILES['foto_passe']) && $_FILES['foto_passe']['error'] === UPLOAD_ERR_OK 
        ? uploadArquivo($_FILES['foto_passe'], 'fotos/') : null;
    
    $sql = "INSERT INTO alunos (
        nome_completo, data_nascimento, sexo, turma_id, info_saude,
        nome_responsavel, telefone, email, relacao, endereco,
        certidao_path, bi_responsavel_path, foto_passe_path,
        aceite_termos, como_soube, observacoes, periodo
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        $_POST['nome_crianca'],
        $_POST['data_nascimento'],
        $_POST['sexo'],
        $_POST['turma_id'],
        $_POST['info_saude'],
        $_POST['nome_responsavel'],
        $_POST['telefone'],
        $_POST['email'],
        $_POST['relacao'],
        $_POST['endereco'],
        $certidao_path,
        $bi_path,
        $foto_path,
        isset($_POST['aceite_termos']) ? 1 : 0,
        $_POST['como_soube'],
        $_POST['observacoes'],
        $_POST['periodo']
    ]);
    
    $aluno_id = $db->lastInsertId();
    
    $stmt = $db->prepare("SELECT codigo_aluno FROM alunos WHERE id = ?");
    $stmt->execute([$aluno_id]);
    $aluno = $stmt->fetch();
    
    $stmt = $db->prepare("INSERT INTO pagamentos_matricula (aluno_id, valor, tipo, status) VALUES (?, 1500.00, 'matricula', 'pendente')");
    $stmt->execute([$aluno_id]);
    
    gerarMensalidades($aluno_id);
    
    $db->commit();
    
    $_SESSION['ultima_matricula'] = [
        'aluno_id' => $aluno_id,
        'codigo' => $aluno['codigo_aluno'],
        'nome' => $_POST['nome_crianca'],
        'data_nascimento' => $_POST['data_nascimento'],
        'data_matricula' => date('Y-m-d H:i:s')
    ];
    
    header('Location: ../confirmacao.php');
    exit;
    
} catch (Exception $e) {
    if (isset($db)) $db->rollBack();
    $_SESSION['erro_matricula'] = "Erro ao processar matrícula: " . $e->getMessage();
    header('Location: ../index.php#fazer-matricula');
    exit;
}
?>