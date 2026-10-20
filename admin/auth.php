<?php
require_once 'config/admin_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$login = $_POST['login'] ?? '';
$senha = $_POST['senha'] ?? '';

if (empty($login) || empty($senha)) {
    $_SESSION['erro_login'] = 'Por favor, preencha todos os campos';
    header('Location: login.php');
    exit;
}

$db = getDB();

// Buscar administrador por usuário ou email
$stmt = $db->prepare("SELECT * FROM administradores WHERE (usuario = ? OR email = ?) AND status = 'ativo'");
$stmt->execute([$login, $login]);
$admin = $stmt->fetch();

if ($admin && password_verify($senha, $admin['senha'])) {
    // Login bem-sucedido
    $_SESSION[SESSION_ADMIN_ID] = $admin['id'];
    $_SESSION[SESSION_ADMIN_NOME] = $admin['nome'];
    $_SESSION[SESSION_ADMIN_NIVEL] = $admin['nivel'];
    $_SESSION['admin_email'] = $admin['email'];
    $_SESSION['admin_usuario'] = $admin['usuario'];
    
    // Atualizar último acesso
    $stmt = $db->prepare("UPDATE administradores SET ultimo_acesso = NOW() WHERE id = ?");
    $stmt->execute([$admin['id']]);
    
    // Registrar log
    registrarLog('login', "Administrador {$admin['nome']} fez login");
    
    header('Location: dashboard.php');
} else {
    // Login falhou
    registrarLog('login_falhou', "Tentativa de login com usuário: $login");
    $_SESSION['erro_login'] = 'Usuário ou senha incorretos';
    header('Location: login.php');
}
exit;
?>