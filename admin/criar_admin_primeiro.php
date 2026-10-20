<?php
require_once '../config/database.php';

$db = getDB();

// Verificar se já existe admin
$stmt = $db->query("SELECT COUNT(*) as total FROM administradores");
$total = $stmt->fetch()['total'];

if ($total == 0) {
    // Criar admin padrão
    $nome = "Administrador Master";
    $email = "admin@kindergarten.com";
    $usuario = "admin";
    $senha = password_hash("admin123", PASSWORD_DEFAULT);
    $nivel = "super_admin";
    
    $stmt = $db->prepare("INSERT INTO administradores (nome, email, usuario, senha, nivel) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nome, $email, $usuario, $senha, $nivel]);
    
    echo "Administrador criado com sucesso!<br>";
    echo "Usuário: admin<br>";
    echo "Senha: admin123<br>";
    echo "<a href='login.php'>Ir para login</a>";
} else {
    echo "Já existem administradores cadastrados.";
}
?>