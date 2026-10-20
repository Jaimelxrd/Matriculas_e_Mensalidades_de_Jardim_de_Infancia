<?php
require_once 'config/admin_config.php';

if (isset($_SESSION[SESSION_ADMIN_ID])) {
    registrarLog('logout', "Administrador {$_SESSION[SESSION_ADMIN_NOME]} fez logout");
}

session_destroy();
header('Location: ../index.php');
exit;
?>