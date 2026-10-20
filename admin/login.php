<?php
require_once 'config/admin_config.php';

// Se já estiver logado, redirecionar
if (isset($_SESSION[SESSION_ADMIN_ID])) {
    header('Location: dashboard.php');
    exit;
}

$erro = $_SESSION['erro_login'] ?? null;
unset($_SESSION['erro_login']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrativo - Kindergarten</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        /* Background decoration */
        .bg-bubbles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }
        
        .bg-bubbles li {
            position: absolute;
            list-style: none;
            display: block;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.05);
            bottom: -160px;
            animation: square 25s infinite;
            transition-timing-function: linear;
        }
        
        .bg-bubbles li:nth-child(1) { left: 10%; width: 80px; height: 80px; animation-delay: 0s; }
        .bg-bubbles li:nth-child(2) { left: 20%; width: 20px; height: 20px; animation-delay: 2s; animation-duration: 17s; }
        .bg-bubbles li:nth-child(3) { left: 25%; width: 60px; height: 60px; animation-delay: 4s; }
        .bg-bubbles li:nth-child(4) { left: 40%; width: 100px; height: 100px; animation-delay: 0s; animation-duration: 22s; }
        .bg-bubbles li:nth-child(5) { left: 70%; width: 40px; height: 40px; animation-delay: 0s; }
        .bg-bubbles li:nth-child(6) { left: 80%; width: 90px; height: 90px; animation-delay: 3s; }
        .bg-bubbles li:nth-child(7) { left: 32%; width: 30px; height: 30px; animation-delay: 7s; }
        .bg-bubbles li:nth-child(8) { left: 55%; width: 70px; height: 70px; animation-delay: 15s; animation-duration: 40s; }
        .bg-bubbles li:nth-child(9) { left: 25%; width: 50px; height: 50px; animation-delay: 2s; animation-duration: 40s; }
        .bg-bubbles li:nth-child(10) { left: 90%; width: 40px; height: 40px; animation-delay: 11s; }
        
        @keyframes square {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; border-radius: 0; }
            100% { transform: translateY(-1000px) rotate(720deg); opacity: 0; border-radius: 50%; }
        }
        
        .login-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 450px;
            margin: 20px;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: fadeInUp 0.6s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            background: linear-gradient(135deg, #ff983e, #ff4d4d);
            padding: 40px;
            text-align: center;
        }
        
        .login-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .login-icon i {
            font-size: 2.5rem;
            color: white;
        }
        
        .login-header h2 {
            color: white;
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        
        .login-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
        }
        
        .login-body {
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        
        .input-group input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }
        
        .input-group input:focus {
            outline: none;
            border-color: #ff983e;
            box-shadow: 0 0 0 3px rgba(255, 152, 62, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #ff983e, #ff4d4d);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 152, 62, 0.4);
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }
        
        .forgot-password a {
            color: #ff983e;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .forgot-password a:hover {
            text-decoration: underline;
        }
        
        .security-badge {
            text-align: center;
            margin-top: 20px;
            font-size: 0.8rem;
            color: #999;
        }
        
        .security-badge i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <ul class="bg-bubbles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>
    
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-school"></i>
                </div>
                <h2>Kindergarten Admin</h2>
                <p>Área restrita - Acesso autorizado apenas</p>
            </div>
            
            <div class="login-body">
                <?php if ($erro): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><?= htmlspecialchars($erro) ?></span>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="auth.php" id="loginForm">
                    <div class="form-group">
                        <label>Usuário ou Email</label>
                        <div class="input-group">
                            <i class="fas fa-user"></i>
                            <input type="text" name="login" placeholder="Digite seu usuário ou email" required autofocus>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Senha</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="senha" id="senha" placeholder="Digite sua senha" required>
                            <i class="fas fa-eye" style="left: auto; right: 15px; cursor: pointer;" onclick="togglePassword()"></i>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Entrar no Sistema
                    </button>
                </form>
                
                <div class="forgot-password">
                    <a href="recuperar_senha.php">Esqueceu sua senha?</a>
                </div>
                
                <div class="security-badge">
                    <i class="fas fa-shield-alt"></i> Conexão segura com criptografia SSL
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            const senhaInput = document.getElementById('senha');
            const type = senhaInput.getAttribute('type') === 'password' ? 'text' : 'password';
            senhaInput.setAttribute('type', type);
        }
    </script>
</body>
</html>