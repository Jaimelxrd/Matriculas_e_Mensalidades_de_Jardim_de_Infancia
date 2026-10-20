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

// Buscar dados do aluno - Usando a estrutura correta da tabela
try {
    $stmt = $db->prepare("
        SELECT a.*, t.nome as turma_nome, t.id as turma_id 
        FROM alunos a 
        LEFT JOIN turmas t ON a.turma_id = t.id 
        WHERE a.id = ?
    ");
    $stmt->execute([$aluno_id]);
    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$aluno || $aluno === false) {
        $_SESSION['erro'] = "Aluno não encontrado!";
        header('Location: alunos.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['erro'] = "Erro ao buscar aluno: " . $e->getMessage();
    header('Location: alunos.php');
    exit;
}

// Buscar encarregado
try {
    $stmt = $db->prepare("SELECT * FROM encarregados WHERE aluno_id = ?");
    $stmt->execute([$aluno_id]);
    $encarregado = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($encarregado === false) {
        $encarregado = null;
    }
} catch (PDOException $e) {
    $encarregado = null;
}

// Buscar turmas para o select
try {
    $stmt = $db->prepare("SELECT * FROM turmas ORDER BY nome");
    $stmt->execute();
    $turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($turmas === false) {
        $turmas = [];
    }
} catch (PDOException $e) {
    $turmas = [];
}

// Buscar documentos do aluno
try {
    $stmt = $db->prepare("SELECT * FROM documentos WHERE aluno_id = ?");
    $stmt->execute([$aluno_id]);
    $documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($documentos === false) {
        $documentos = [];
    }
} catch (PDOException $e) {
    $documentos = [];
}

// Buscar matrícula
try {
    $stmt = $db->prepare("SELECT * FROM matriculas WHERE aluno_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$aluno_id]);
    $matricula = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($matricula === false) {
        $matricula = null;
    }
} catch (PDOException $e) {
    $matricula = null;
}

// Buscar mensalidades do aluno
try {
    $stmt = $db->prepare("SELECT * FROM mensalidades WHERE aluno_id = ? ORDER BY ano DESC, FIELD(mes_referencia, 'Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro')");
    $stmt->execute([$aluno_id]);
    $mensalidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($mensalidades === false) {
        $mensalidades = [];
    }
} catch (PDOException $e) {
    $mensalidades = [];
}

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
                status_matricula = ?,
                nome_responsavel = ?,
                telefone = ?,
                email = ?,
                relacao = ?,
                endereco = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $_POST['nome_completo'] ?? '',
            $_POST['data_nascimento'] ?? null,
            $_POST['sexo'] ?? 'Masculino',
            !empty($_POST['turma_id']) ? $_POST['turma_id'] : null,
            $_POST['info_saude'] ?? '',
            $_POST['observacoes'] ?? '',
            $_POST['periodo'] ?? 'Integral',
            $_POST['como_soube'] ?? '',
            $_POST['status_matricula'] ?? 'pendente',
            $_POST['nome_responsavel'] ?? '',
            $_POST['telefone'] ?? '',
            $_POST['email'] ?? '',
            $_POST['relacao'] ?? 'Pai',
            $_POST['endereco'] ?? '',
            $aluno_id
        ]);
        
        // Atualizar ou inserir dados do encarregado (tabela separada)
        if ($encarregado && is_array($encarregado)) {
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
                $_POST['enc_nome_completo'] ?? $_POST['nome_responsavel'] ?? '',
                $_POST['enc_telefone'] ?? $_POST['telefone'] ?? '',
                $_POST['enc_email'] ?? $_POST['email'] ?? '',
                $_POST['enc_relacao'] ?? $_POST['relacao'] ?? 'Pai',
                $_POST['enc_endereco'] ?? $_POST['endereco'] ?? '',
                $aluno_id
            ]);
        } else {
            // Inserir na tabela encarregados se não existir
            $stmt = $db->prepare("
                INSERT INTO encarregados (nome_completo, telefone, email, relacao_crianca, endereco, aluno_id, senha_pin) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $senha_pin = password_hash('123456', PASSWORD_DEFAULT);
            $stmt->execute([
                $_POST['enc_nome_completo'] ?? $_POST['nome_responsavel'] ?? '',
                $_POST['enc_telefone'] ?? $_POST['telefone'] ?? '',
                $_POST['enc_email'] ?? $_POST['email'] ?? '',
                $_POST['enc_relacao'] ?? $_POST['relacao'] ?? 'Pai',
                $_POST['enc_endereco'] ?? $_POST['endereco'] ?? '',
                $aluno_id,
                $senha_pin
            ]);
        }
        
        // Atualizar matrícula se necessário
        if (isset($_POST['turma_desejada'])) {
            $stmt = $db->prepare("
                UPDATE matriculas SET 
                    status_matricula = ?,
                    turma_desejada = ?
                WHERE aluno_id = ? 
                ORDER BY id DESC LIMIT 1
            ");
            
            $stmt->execute([
                $_POST['status_matricula'] ?? 'ativa',
                $_POST['turma_desejada'] ?? null,
                $aluno_id
            ]);
        }
        
        $db->commit();
        
        $_SESSION['mensagem'] = "Dados do aluno atualizados com sucesso!";
        
        // Registrar log
        try {
            $stmt = $db->prepare("
                INSERT INTO admin_logs (admin_id, acao, detalhes, ip_address) 
                VALUES (?, 'editar_aluno', ?, ?)
            ");
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $stmt->execute([$admin_id, "Editou dados do aluno ID: $aluno_id - " . ($_POST['nome_completo'] ?? ''), $ip_address]);
        } catch (Exception $e) {
            // Ignorar erro de log
        }
        
        header('Location: alunos.php');
        exit;
        
    } catch (PDOException $e) {
        $db->rollBack();
        $_SESSION['erro'] = "Erro ao atualizar dados: " . $e->getMessage();
    }
}

// Variáveis seguras para exibição
$codigo_aluno = isset($aluno['codigo_aluno']) ? $aluno['codigo_aluno'] : 'Não disponível';
$nome_completo = isset($aluno['nome_completo']) ? $aluno['nome_completo'] : '';
$data_nascimento = isset($aluno['data_nascimento']) ? $aluno['data_nascimento'] : '';
$sexo = isset($aluno['sexo']) ? $aluno['sexo'] : 'Masculino';
$turma_id = isset($aluno['turma_id']) ? $aluno['turma_id'] : '';
$periodo = isset($aluno['periodo']) ? $aluno['periodo'] : 'Integral';
$status_matricula = isset($aluno['status_matricula']) ? $aluno['status_matricula'] : 'pendente';
$como_soube = isset($aluno['como_soube']) ? $aluno['como_soube'] : '';
$info_saude = isset($aluno['info_saude']) ? $aluno['info_saude'] : '';
$observacoes = isset($aluno['observacoes']) ? $aluno['observacoes'] : '';

// Dados do responsável (da tabela alunos)
$nome_responsavel = isset($aluno['nome_responsavel']) ? $aluno['nome_responsavel'] : '';
$telefone = isset($aluno['telefone']) ? $aluno['telefone'] : '';
$email = isset($aluno['email']) ? $aluno['email'] : '';
$relacao = isset($aluno['relacao']) ? $aluno['relacao'] : 'Pai';
$endereco = isset($aluno['endereco']) ? $aluno['endereco'] : '';

// Dados do encarregado (tabela separada)
$enc_nome = isset($encarregado['nome_completo']) && is_array($encarregado) ? $encarregado['nome_completo'] : $nome_responsavel;
$enc_telefone = isset($encarregado['telefone']) && is_array($encarregado) ? $encarregado['telefone'] : $telefone;
$enc_email = isset($encarregado['email']) && is_array($encarregado) ? $encarregado['email'] : $email;
$enc_relacao = isset($encarregado['relacao_crianca']) && is_array($encarregado) ? $encarregado['relacao_crianca'] : $relacao;
$enc_endereco = isset($encarregado['endereco']) && is_array($encarregado) ? $encarregado['endereco'] : $endereco;

// Dados da matrícula
$mat_turma_desejada = isset($matricula['turma_desejada']) && is_array($matricula) ? $matricula['turma_desejada'] : '';
$mat_status = isset($matricula['status_matricula']) && is_array($matricula) ? $matricula['status_matricula'] : $status_matricula;

// Caminho da foto
$foto_path = isset($aluno['foto_perfil']) && !empty($aluno['foto_perfil']) ? $aluno['foto_perfil'] : (isset($aluno['foto_path']) ? $aluno['foto_path'] : 'assets/img/default-avatar.png');
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
        .photo-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ff983e;
            margin-bottom: 10px;
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
                    <span><?= htmlspecialchars($_SESSION[SESSION_ADMIN_NOME] ?? 'Admin') ?></span>
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
                <!-- Código do Aluno -->
                <div class="student-code-box">
                    <div>
                        <i class="fas fa-id-card" style="font-size: 24px;"></i>
                        <span style="margin-left: 10px;">Código do Estudante:</span>
                    </div>
                    <strong><?= htmlspecialchars($codigo_aluno) ?></strong>
                </div>
                
                <!-- Foto do Aluno -->
                <div style="text-align: center; margin-bottom: 20px;">
                    <img src="../<?= htmlspecialchars($foto_path) ?>" alt="Foto do aluno" class="photo-preview" id="fotoPreview">
                    <div>
                        <button type="button" class="btn-upload" onclick="uploadFoto()" style="background: #ff983e; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; margin-top: 10px;">
                            <i class="fas fa-camera"></i> Alterar Foto
                        </button>
                    </div>
                </div>
                
                <form method="POST">
                    <!-- Dados do Aluno -->
                    <div class="form-section">
                        <h3><i class="fas fa-child"></i> Dados do Aluno</h3>
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label><i class="fas fa-user"></i> Nome Completo *</label>
                                <input type="text" name="nome_completo" class="form-control" required 
                                       value="<?= htmlspecialchars($nome_completo) ?>">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-calendar"></i> Data de Nascimento</label>
                                <input type="date" name="data_nascimento" class="form-control" 
                                       value="<?= htmlspecialchars($data_nascimento) ?>">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-venus-mars"></i> Sexo</label>
                                <select name="sexo" class="form-control">
                                    <option value="Masculino" <?= $sexo == 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                                    <option value="Feminino" <?= $sexo == 'Feminino' ? 'selected' : '' ?>>Feminino</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-chalkboard"></i> Turma</label>
                                <select name="turma_id" class="form-control">
                                    <option value="">Selecione uma turma</option>
                                    <?php foreach($turmas as $turma): ?>
                                        <option value="<?= $turma['id'] ?>" <?= $turma_id == $turma['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($turma['nome']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-clock"></i> Período</label>
                                <select name="periodo" class="form-control">
                                    <option value="Meio período" <?= $periodo == 'Meio período' ? 'selected' : '' ?>>Meio período</option>
                                    <option value="Integral" <?= $periodo == 'Integral' ? 'selected' : '' ?>>Integral</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-flag-checkered"></i> Status Matrícula</label>
                                <select name="status_matricula" class="form-control">
                                    <option value="pendente" <?= $status_matricula == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                    <option value="confirmada" <?= $status_matricula == 'confirmada' ? 'selected' : '' ?>>Confirmada</option>
                                    <option value="cancelada" <?= $status_matricula == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-question-circle"></i> Como soube?</label>
                                <input type="text" name="como_soube" class="form-control" 
                                       value="<?= htmlspecialchars($como_soube) ?>">
                            </div>
                            <div class="form-group full-width">
                                <label><i class="fas fa-heartbeat"></i> Informações de Saúde</label>
                                <textarea name="info_saude" class="form-control" rows="3"><?= htmlspecialchars($info_saude) ?></textarea>
                            </div>
                            <div class="form-group full-width">
                                <label><i class="fas fa-comment"></i> Observações</label>
                                <textarea name="observacoes" class="form-control" rows="3"><?= htmlspecialchars($observacoes) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Dados do Responsável/Encarregado -->
                    <div class="form-section">
                        <h3><i class="fas fa-parent"></i> Dados do Encarregado</h3>
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label><i class="fas fa-user"></i> Nome Completo *</label>
                                <input type="text" name="enc_nome_completo" class="form-control" required 
                                       value="<?= htmlspecialchars($enc_nome) ?>">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-phone"></i> Telefone *</label>
                                <input type="text" name="enc_telefone" class="form-control" required 
                                       value="<?= htmlspecialchars($enc_telefone) ?>">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-envelope"></i> Email *</label>
                                <input type="email" name="enc_email" class="form-control" required 
                                       value="<?= htmlspecialchars($enc_email) ?>">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-handshake"></i> Relação</label>
                                <select name="enc_relacao" class="form-control">
                                    <option value="Pai" <?= $enc_relacao == 'Pai' ? 'selected' : '' ?>>Pai</option>
                                    <option value="Mae" <?= $enc_relacao == 'Mae' ? 'selected' : '' ?>>Mãe</option>
                                    <option value="Outro" <?= $enc_relacao == 'Outro' ? 'selected' : '' ?>>Outro</option>
                                </select>
                            </div>
                            <div class="form-group full-width">
                                <label><i class="fas fa-map-marker-alt"></i> Endereço *</label>
                                <input type="text" name="enc_endereco" class="form-control" required 
                                       value="<?= htmlspecialchars($enc_endereco) ?>">
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
                                    <option value="Mini Kids" <?= $mat_turma_desejada == 'Mini Kids' ? 'selected' : '' ?>>Mini Kids</option>
                                    <option value="Júnior" <?= $mat_turma_desejada == 'Júnior' ? 'selected' : '' ?>>Júnior</option>
                                    <option value="Pré-Escolar" <?= $mat_turma_desejada == 'Pré-Escolar' ? 'selected' : '' ?>>Pré-Escolar</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-calendar"></i> Data Matrícula</label>
                                <input type="date" name="data_matricula" class="form-control" 
                                       value="<?= isset($aluno['data_matricula']) ? date('Y-m-d', strtotime($aluno['data_matricula'])) : '' ?>">
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
        
        // Função para upload de foto
        function uploadFoto() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = async (e) => {
                const file = e.target.files[0];
                const formData = new FormData();
                formData.append('foto', file);
                formData.append('aluno_id', <?= $aluno_id ?>);
                
                const response = await fetch('upload_foto_aluno.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                if (result.success) {
                    document.getElementById('fotoPreview').src = result.foto_url + '?' + Date.now();
                    alert('Foto atualizada com sucesso!');
                } else {
                    alert('Erro ao fazer upload: ' + result.error);
                }
            };
            input.click();
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