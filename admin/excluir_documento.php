<?php
require_once 'config/admin_config.php';
verificarAdmin();

$db = getDB();
$admin_id = $_SESSION[SESSION_ADMIN_ID];

// Verificar se ID do aluno foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['erro'] = "ID do aluno não informado!";
    header('Location: alunos.php');
    exit;
}

$aluno_id = intval($_GET['id']);

// Buscar dados do aluno - Certifique-se de incluir TODOS os campos
$stmt = $db->prepare("
    SELECT a.*, t.nome as turma_nome, t.id as turma_id 
    FROM alunos a 
    LEFT JOIN turmas t ON a.turma_id = t.id 
    WHERE a.id = ?
");
$stmt->execute([$aluno_id]);
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$aluno) {
    $_SESSION['erro'] = "Aluno não encontrado!";
    header('Location: alunos.php');
    exit;
}

// Buscar encarregado
$stmt = $db->prepare("SELECT * FROM encarregados WHERE aluno_id = ?");
$stmt->execute([$aluno_id]);
$encarregado = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar turmas para o select
$stmt = $db->prepare("SELECT * FROM turmas ORDER BY nome");
$stmt->execute();
$turmas = $stmt->fetchAll();

// Buscar documentos do aluno
$stmt = $db->prepare("SELECT * FROM documentos WHERE aluno_id = ?");
$stmt->execute([$aluno_id]);
$documentos = $stmt->fetchAll();

// Buscar matrícula
$stmt = $db->prepare("SELECT * FROM matriculas WHERE aluno_id = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$aluno_id]);
$matricula = $stmt->fetch(PDO::FETCH_ASSOC);

// Processar atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db->beginTransaction();
        
        // Atualizar dados do aluno
        $stmt = $db->prepare("
            UPDATE alunos SET 
                nome_completo = ?,
                data_nascimento = ?,
                sexo = ?,
                turma_id = ?,
                info_saude = ?,
                observacoes = ?,
                periodo = ?,
                como_soube = ?,
                status = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $_POST['nome_completo'],
            $_POST['data_nascimento'],
            $_POST['sexo'],
            !empty($_POST['turma_id']) ? $_POST['turma_id'] : null,
            $_POST['info_saude'],
            $_POST['observacoes'],
            $_POST['periodo'],
            $_POST['como_soube'],
            $_POST['status'],
            $aluno_id
        ]);
        
        // Atualizar dados do encarregado
        if ($encarregado) {
            $stmt = $db->prepare("
                UPDATE encarregados SET 
                    nome_completo = ?,
                    telefone = ?,
                    email = ?,
                    relacao_crianca = ?,
                    endereco = ?
                WHERE aluno_id = ?
            ");
            $stmt->execute([
                $_POST['enc_nome_completo'],
                $_POST['enc_telefone'],
                $_POST['enc_email'],
                $_POST['enc_relacao'],
                $_POST['enc_endereco'],
                $aluno_id
            ]);
        } else {
            $stmt = $db->prepare("
                INSERT INTO encarregados (nome_completo, telefone, email, relacao_crianca, endereco, aluno_id, senha_pin) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $senha_pin = password_hash('123456', PASSWORD_DEFAULT);
            $stmt->execute([
                $_POST['enc_nome_completo'],
                $_POST['enc_telefone'],
                $_POST['enc_email'],
                $_POST['enc_relacao'],
                $_POST['enc_endereco'],
                $aluno_id,
                $senha_pin
            ]);
        }
        
        // Atualizar status da matrícula se necessário
        if (isset($_POST['status_matricula'])) {
            $stmt = $db->prepare("
                UPDATE matriculas SET 
                    status_matricula = ?,
                    turma_desejada = ?
                WHERE aluno_id = ? AND status_matricula != 'concluida'
                ORDER BY id DESC LIMIT 1
            ");
            
            $stmt->execute([
                $_POST['status_matricula'],
                $_POST['turma_desejada'],
                $aluno_id
            ]);
        }
        
        // Se aprovou a matrícula, registrar data de aprovação
        if ($_POST['status'] == 'aprovado' && $aluno['status'] != 'aprovado') {
            $stmt = $db->prepare("
                UPDATE matriculas SET 
                    data_aprovacao = CURRENT_DATE(),
                    aprovado_por = ?
                WHERE aluno_id = ? AND status_matricula = 'ativa'
            ");
            $stmt->execute([$_SESSION[SESSION_ADMIN_NOME], $aluno_id]);
        }
        
        $db->commit();
        
        $_SESSION['mensagem'] = "Dados do aluno atualizados com sucesso!";
        registrarLog('editar_aluno', "Editou dados do aluno ID: $aluno_id - " . $_POST['nome_completo']);
        
        header('Location: alunos.php');
        exit;
        
    } catch (PDOException $e) {
        $db->rollBack();
        $_SESSION['erro'] = "Erro ao atualizar dados: " . $e->getMessage();
    }
}

// Buscar logs do aluno
try {
    $stmt = $db->prepare("
        SELECT * FROM logs 
        WHERE descricao LIKE ? 
        ORDER BY data_hora DESC 
        LIMIT 20
    ");
    $stmt->execute(["%aluno ID: $aluno_id%"]);
    $logs = $stmt->fetchAll();
} catch (PDOException $e) {
    $logs = [];
}

// Variável segura para o código do estudante
$codigo_estudante = isset($aluno['codigo_estudante']) ? $aluno['codigo_estudante'] : 'Não disponível';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Aluno - Kindergarten Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .form-section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-section h3 {
            color: #ff983e;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .form-group label i {
            margin-right: 8px;
            color: #ff983e;
        }
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            transition: all 0.3s;
        }
        .form-control:focus {
            outline: none;
            border-color: #ff983e;
            box-shadow: 0 0 0 3px rgba(255, 152, 62, 0.1);
        }
        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }
        .btn-save {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        .btn-cancel {
            background: #6c757d;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            margin-left: 10px;
            transition: all 0.3s;
        }
        .btn-cancel:hover {
            background: #5a6268;
        }
        .student-code-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .student-code-box strong {
            font-size: 20px;
            font-family: monospace;
            letter-spacing: 1px;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .form-actions {
            margin-top: 30px;
            text-align: right;
        }
        @media (max-width: 768px) {
            .form-section {
                padding: 15px;
            }
            .form-actions {
                text-align: center;
            }
            .btn-cancel {
                margin-left: 0;
                margin-top: 10px;
            }
            .student-code-box {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="header-content">
            <div class="logo">
                <span>Kindergarten Admin</span>
            </div>
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="user-info">
                <div class="user-name">
                    <i class="fas fa-user-shield"></i>
                    <span><?= htmlspecialchars($_SESSION[SESSION_ADMIN_NOME]) ?></span>
                </div>
                <a href="logout.php" class="btn-sair">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>
    </div>

    <div class="admin-sidebar" id="sidebar">
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="alunos.php"><i class="fas fa-users"></i> Alunos</a></li>
            <li><a href="turmas.php"><i class="fas fa-chalkboard"></i> Turmas</a></li>
            <li><a href="pagamentos.php"><i class="fas fa-credit-card"></i> Pagamentos</a></li>
            <li><a href="mensagens.php"><i class="fas fa-envelope"></i> Mensagens</a></li>
            <li><a href="comunicados.php"><i class="fas fa-bullhorn"></i> Comunicados</a></li>
            <li><a href="relatorios.php"><i class="fas fa-chart-line"></i> Relatórios</a></li>
            <li><a href="administradores.php"><i class="fas fa-user-cog"></i> Administradores</a></li>
        </ul>
    </div>

    <div class="admin-main">
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['mensagem']) ?></div>
            <?php unset($_SESSION['mensagem']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['erro'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['erro']) ?></div>
            <?php unset($_SESSION['erro']); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-edit"></i> Editar Aluno</h3>
            </div>
            <div class="card-body">
                <!-- Código do Estudante - Versão CORRIGIDA sem erro -->
                <div class="student-code-box">
                    <div>
                        <i class="fas fa-id-card" style="font-size: 24px;"></i>
                        <span style="margin-left: 10px;">Código do Estudante:</span>
                    </div>
                    <strong><?= $codigo_estudante ?></strong>
                </div>
                
                <form method="POST">
                    <!-- Dados do Aluno -->
                    <div class="form-section">
                        <h3><i class="fas fa-child"></i> Dados do Aluno</h3>
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label><i class="fas fa-user"></i> Nome Completo *</label>
                                <input type="text" name="nome_completo" class="form-control" required 
                                       value="<?= htmlspecialchars($aluno['nome_completo'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-calendar"></i> Data de Nascimento</label>
                                <input type="date" name="data_nascimento" class="form-control" 
                                       value="<?= $aluno['data_nascimento'] ?? '' ?>">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-venus-mars"></i> Sexo</label>
                                <select name="sexo" class="form-control">
                                    <option value="Masculino" <?= ($aluno['sexo'] ?? '') == 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                                    <option value="Feminino" <?= ($aluno['sexo'] ?? '') == 'Feminino' ? 'selected' : '' ?>>Feminino</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-chalkboard"></i> Turma</label>
                                <select name="turma_id" class="form-control">
                                    <option value="">Selecione uma turma</option>
                                    <?php foreach($turmas as $turma): ?>
                                        <option value="<?= $turma['id'] ?>" <?= ($aluno['turma_id'] ?? '') == $turma['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($turma['nome']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-clock"></i> Período</label>
                                <select name="periodo" class="form-control">
                                    <option value="Meio período" <?= ($aluno['periodo'] ?? '') == 'Meio período' ? 'selected' : '' ?>>Meio período</option>
                                    <option value="Integral" <?= ($aluno['periodo'] ?? '') == 'Integral' ? 'selected' : '' ?>>Integral</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-check-circle"></i> Status</label>
                                <select name="status" class="form-control">
                                    <option value="pendente" <?= ($aluno['status'] ?? '') == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                    <option value="aprovado" <?= ($aluno['status'] ?? '') == 'aprovado' ? 'selected' : '' ?>>Aprovado</option>
                                    <option value="rejeitado" <?= ($aluno['status'] ?? '') == 'rejeitado' ? 'selected' : '' ?>>Rejeitado</option>
                                    <option value="matriculado" <?= ($aluno['status'] ?? '') == 'matriculado' ? 'selected' : '' ?>>Matriculado</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-question-circle"></i> Como soube?</label>
                                <input type="text" name="como_soube" class="form-control" 
                                       value="<?= htmlspecialchars($aluno['como_soube'] ?? '') ?>">
                            </div>
                            <div class="form-group full-width">
                                <label><i class="fas fa-heartbeat"></i> Informações de Saúde</label>
                                <textarea name="info_saude" class="form-control" rows="3"><?= htmlspecialchars($aluno['info_saude'] ?? '') ?></textarea>
                            </div>
                            <div class="form-group full-width">
                                <label><i class="fas fa-comment"></i> Observações</label>
                                <textarea name="observacoes" class="form-control" rows="3"><?= htmlspecialchars($aluno['observacoes'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Dados do Encarregado -->
                    <div class="form-section">
                        <h3><i class="fas fa-parent"></i> Dados do Encarregado</h3>
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label><i class="fas fa-user"></i> Nome Completo *</label>
                                <input type="text" name="enc_nome_completo" class="form-control" required 
                                       value="<?= htmlspecialchars($encarregado['nome_completo'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-phone"></i> Telefone *</label>
                                <input type="text" name="enc_telefone" class="form-control" required 
                                       value="<?= htmlspecialchars($encarregado['telefone'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-envelope"></i> Email *</label>
                                <input type="email" name="enc_email" class="form-control" required 
                                       value="<?= htmlspecialchars($encarregado['email'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-handshake"></i> Relação</label>
                                <select name="enc_relacao" class="form-control">
                                    <option value="Pai" <?= ($encarregado['relacao_crianca'] ?? '') == 'Pai' ? 'selected' : '' ?>>Pai</option>
                                    <option value="Mae" <?= ($encarregado['relacao_crianca'] ?? '') == 'Mae' ? 'selected' : '' ?>>Mãe</option>
                                    <option value="Outro" <?= ($encarregado['relacao_crianca'] ?? '') == 'Outro' ? 'selected' : '' ?>>Outro</option>
                                </select>
                            </div>
                            <div class="form-group full-width">
                                <label><i class="fas fa-map-marker-alt"></i> Endereço *</label>
                                <input type="text" name="enc_endereco" class="form-control" required 
                                       value="<?= htmlspecialchars($encarregado['endereco'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Dados da Matrícula -->
                    <div class="form-section">
                        <h3><i class="fas fa-file-signature"></i> Dados da Matrícula</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label><i class="fas fa-chalkboard"></i> Turma Desejada</label>
                                <select name="turma_desejada" class="form-control">
                                    <option value="">Selecione</option>
                                    <option value="MiniKids" <?= ($matricula['turma_desejada'] ?? '') == 'MiniKids' ? 'selected' : '' ?>>Mini Kids</option>
                                    <option value="Junior" <?= ($matricula['turma_desejada'] ?? '') == 'Junior' ? 'selected' : '' ?>>Júnior</option>
                                    <option value="PreEscolar" <?= ($matricula['turma_desejada'] ?? '') == 'PreEscolar' ? 'selected' : '' ?>>Pré-Escolar</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-flag-checkered"></i> Status Matrícula</label>
                                <select name="status_matricula" class="form-control">
                                    <option value="ativa" <?= ($matricula['status_matricula'] ?? '') == 'ativa' ? 'selected' : '' ?>>Ativa</option>
                                    <option value="cancelada" <?= ($matricula['status_matricula'] ?? '') == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                                    <option value="trancada" <?= ($matricula['status_matricula'] ?? '') == 'trancada' ? 'selected' : '' ?>>Trancada</option>
                                    <option value="concluida" <?= ($matricula['status_matricula'] ?? '') == 'concluida' ? 'selected' : '' ?>>Concluída</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="form-actions">
                        <a href="alunos.php" class="btn-cancel">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save"></i> Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Menu toggle
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        if (menuToggle) {
            menuToggle.addEventListener('click', () => { sidebar.classList.toggle('active'); });
        }
        
        // Auto-fechar alertas
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>