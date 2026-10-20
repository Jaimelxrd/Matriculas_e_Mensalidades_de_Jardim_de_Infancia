<?php
session_start();

function gerarReferenciaPagamento($aluno_id, $tipo) {
    return sprintf("%06d%02d", $aluno_id, ($tipo == 'matricula' ? 1 : 2));
}

function formatarData($data, $formato = 'd/m/Y') {
    if (!$data) return '';
    $date = new DateTime($data);
    return $date->format($formato);
}

function formatarDataHora($timestamp) {
    $date = new DateTime($timestamp);
    return [
        'data' => $date->format('d \\d\\e F \\d\\e Y'),
        'hora' => $date->format('H:i')
    ];
}

function formatarMoeda($valor) {
    return number_format($valor, 2, ',', '.') . ' MT';
}

function gerarMensalidades($aluno_id, $ano = null) {
    $db = getDB();
    $ano = $ano ?? date('Y');
    
    $meses = [
        'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
    ];
    
    $valor_mensalidade = 4500.00;
    
    foreach ($meses as $indice => $mes) {
        $data_vencimento = date("Y-m-d", strtotime("$ano-" . ($indice + 1) . "-10"));
        
        $stmt = $db->prepare("INSERT INTO mensalidades (aluno_id, mes_referencia, ano, valor, data_vencimento, status) 
                               VALUES (?, ?, ?, ?, ?, 'pendente')");
        $stmt->execute([$aluno_id, $mes, $ano, $valor_mensalidade, $data_vencimento]);
    }
}

function uploadArquivo($file, $diretorio = 'uploads/') {
    $diretorio_completo = __DIR__ . '/../assets/' . $diretorio;
    
    if (!file_exists($diretorio_completo)) {
        mkdir($diretorio_completo, 0777, true);
    }
    
    $extensao = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $nome_arquivo = uniqid() . '.' . $extensao;
    $caminho_completo = $diretorio_completo . $nome_arquivo;
    
    if (move_uploaded_file($file['tmp_name'], $caminho_completo)) {
        return $diretorio . $nome_arquivo;
    }
    
    return null;
}

function verificarAcessoAluno($codigo, $data_nascimento) {
    $db = getDB();
    
    $stmt = $db->prepare("SELECT * FROM alunos WHERE codigo_aluno = ? AND data_nascimento = ?");
    $stmt->execute([$codigo, $data_nascimento]);
    
    return $stmt->fetch();
}

function getInformacoesPagamento($aluno_id) {
    $db = getDB();
    
    $stmt = $db->prepare("SELECT * FROM pagamentos_matricula WHERE aluno_id = ? ORDER BY created_at DESC");
    $stmt->execute([$aluno_id]);
    
    return $stmt->fetchAll();
}
?>