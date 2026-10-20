<?php
// Caminhos absolutos usando __DIR__
$root_path = dirname(dirname(__DIR__)); // Isso vai para a pasta 'new'

require_once $root_path . '/config/database.php';
require_once $root_path . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Constantes de sessão
define('SESSION_ADMIN_ID', 'admin_id');
define('SESSION_ADMIN_NOME', 'admin_nome');
define('SESSION_ADMIN_NIVEL', 'admin_nivel');

// Verificar se está logado
function verificarAdmin() {
    if (!isset($_SESSION[SESSION_ADMIN_ID])) {
        header('Location: login.php');
        exit;
    }
}

// Verificar nível de acesso
function verificarNivel($nivel_necessario) {
    verificarAdmin();
    
    $niveis = ['secretaria' => 1, 'admin' => 2, 'super_admin' => 3];
    
    if ($niveis[$_SESSION[SESSION_ADMIN_NIVEL]] < $niveis[$nivel_necessario]) {
        header('Location: dashboard.php?erro=permissao');
        exit;
    }
}

// Registrar log de atividade
function registrarLog($acao, $detalhes = null) {
    $db = getDB();
    $admin_id = $_SESSION[SESSION_ADMIN_ID] ?? null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    
    try {
        $stmt = $db->prepare("INSERT INTO admin_logs (admin_id, acao, detalhes, ip_address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$admin_id, $acao, $detalhes, $ip]);
    } catch (Exception $e) {
        // Log error silently
    }
}
?>