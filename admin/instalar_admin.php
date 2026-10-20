<?php
// Caminho absoluto
$root_path = dirname(__DIR__);
require_once $root_path . '/config/database.php';

$db = getDB();

// Verificar se a tabela existe
try {
    $db->query("SELECT 1 FROM administradores LIMIT 1");
    $tabela_existe = true;
} catch (PDOException $e) {
    $tabela_existe = false;
}

if (!$tabela_existe) {
    // Criar tabela
    $sql = "
    CREATE TABLE IF NOT EXISTS administradores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        usuario VARCHAR(50) UNIQUE NOT NULL,
        senha VARCHAR(255) NOT NULL,
        nivel ENUM('admin', 'super_admin', 'secretaria') DEFAULT 'admin',
        foto_path VARCHAR(255),
        ultimo_acesso TIMESTAMP NULL,
        status ENUM('ativo', 'inativo') DEFAULT 'ativo',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );
    
    CREATE TABLE IF NOT EXISTS admin_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        admin_id INT,
        acao VARCHAR(255),
        detalhes TEXT,
        ip_address VARCHAR(45),
        data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (admin_id) REFERENCES administradores(id) ON DELETE SET NULL
    );
    ";
    
    $db->exec($sql);
    echo "Tabelas criadas com sucesso!<br>";
}

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
    
    echo "✅ Administrador criado com sucesso!<br>";
    echo "📧 Usuário: <strong>admin</strong><br>";
    echo "🔑 Senha: <strong>admin123</strong><br>";
} else {
    echo "⚠️ Já existem administradores cadastrados.<br>";
}

echo "<br><a href='login.php'>Ir para página de login →</a>";
?>