<?php
require_once 'config/admin_config.php';
verificarAdmin();

$db = getDB();

// Buscar alunos com filtros
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';

$sql = "SELECT a.*, t.nome as turma_nome 
        FROM alunos a 
        LEFT JOIN turmas t ON a.turma_id = t.id 
        WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (a.nome_completo LIKE ? OR a.codigo_aluno LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($status) {
    $sql .= " AND a.status_matricula = ?";
    $params[] = $status;
}

$sql .= " ORDER BY a.data_matricula DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$alunos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alunos - Kindergarten Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
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
                    <span>Admin</span>
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
            <li class="active"><a href="alunos.php"><i class="fas fa-users"></i> Alunos</a></li>
            <li><a href="turmas.php"><i class="fas fa-chalkboard"></i> Turmas</a></li>
            <li><a href="pagamentos.php"><i class="fas fa-credit-card"></i> Pagamentos</a></li>
            <li><a href="mensagens.php"><i class="fas fa-envelope"></i> Mensagens</a></li>
            <li><a href="comunicados.php"><i class="fas fa-bullhorn"></i> Comunicados</a></li>
            <li><a href="relatorios.php"><i class="fas fa-chart-line"></i> Relatórios</a></li>
        </ul>
    </div>

    <div class="admin-main">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-users"></i> Gerenciar Alunos</h3>
                <div>
                    <input type="text" id="searchInput" placeholder="Buscar aluno..." class="btn-sm" style="padding: 8px;">
                    <select id="statusFilter" class="btn-sm" style="padding: 8px;">
                        <option value="">Todos</option>
                        <option value="confirmada">Confirmados</option>
                        <option value="pendente">Pendentes</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="data-table" id="alunosTable">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nome</th>
                                <th>Turma</th>
                                <th>Data Matrícula</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($alunos as $aluno): ?>
                            <tr>
                                <td><?= htmlspecialchars($aluno['codigo_aluno']) ?></td>
                                <td><?= htmlspecialchars($aluno['nome_completo']) ?></td>
                                <td><?= htmlspecialchars($aluno['turma_nome'] ?? 'Não atribuída') ?></td>
                                <td><?= date('d/m/Y', strtotime($aluno['data_matricula'])) ?></td>
                                <td>
                                    <span class="badge <?= $aluno['status_matricula'] == 'confirmada' ? 'badge-success' : 'badge-warning' ?>">
                                        <?= $aluno['status_matricula'] == 'confirmada' ? 'Confirmada' : 'Pendente' ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="editar_aluno.php?id=<?= $aluno['id'] ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-success btn-sm" onclick="aprovarMatricula(<?= $aluno['id'] ?>)">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deletarAluno(<?= $aluno['id'] ?>, '<?= htmlspecialchars($aluno['nome_completo']) ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Filtro de busca
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const search = this.value.toLowerCase();
            const rows = document.querySelectorAll('#alunosTable tbody tr');
            
            rows.forEach(row => {
                const nome = row.cells[1].textContent.toLowerCase();
                const codigo = row.cells[0].textContent.toLowerCase();
                row.style.display = nome.includes(search) || codigo.includes(search) ? '' : 'none';
            });
        });
        
        function aprovarMatricula(id) {
            if(confirm('Confirmar esta matrícula?')) {
                fetch('api/aprovar_matricula.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + id
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        location.reload();
                    } else {
                        alert('Erro ao aprovar matrícula');
                    }
                });
            }
        }
        
        function deletarAluno(id, nome) {
            if(confirm(`Tem certeza que deseja deletar o aluno ${nome}?`)) {
                fetch('api/deletar_aluno.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + id
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        location.reload();
                    } else {
                        alert('Erro ao deletar aluno');
                    }
                });
            }
        }
    </script>
</body>
</html>