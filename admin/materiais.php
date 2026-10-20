<?php
require_once 'config/admin_config.php';
verificarAdmin();

$db = getDB();
$admin_id = $_SESSION[SESSION_ADMIN_ID];

// Processar criação/edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['criar_material'])) {
        $titulo = trim($_POST['titulo']);
        $descricao = trim($_POST['descricao']);
        $tipo = $_POST['tipo'];
        $categoria_id = $_POST['categoria_id'] ?: null;
        $autor = trim($_POST['autor']);
        $turma_id = $_POST['turma_id'] ?: null;
        $link_externo = trim($_POST['link_externo']);
        $destaque = isset($_POST['destaque']) ? 1 : 0;
        
        // Upload de arquivo - usando a função do functions.php
        $arquivo_path = null;
        if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
            if (function_exists('uploadArquivo')) {
                $arquivo_path = uploadArquivo($_FILES['arquivo'], $tipo . 's');
            } else {
                // Fallback se função não existir
                $diretorio = '../assets/uploads/' . $tipo . 's/';
                if (!file_exists($diretorio)) mkdir($diretorio, 0777, true);
                $extensao = strtolower(pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION));
                $nome_arquivo = uniqid() . '.' . $extensao;
                if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $diretorio . $nome_arquivo)) {
                    $arquivo_path = 'assets/uploads/' . $tipo . 's/' . $nome_arquivo;
                }
            }
        }
        
        // Upload de capa
        $imagem_capa = null;
        if (isset($_FILES['imagem_capa']) && $_FILES['imagem_capa']['error'] === UPLOAD_ERR_OK) {
            if (function_exists('uploadImagem')) {
                $imagem_capa = uploadImagem($_FILES['imagem_capa']);
            } elseif (function_exists('uploadArquivo')) {
                $imagem_capa = uploadArquivo($_FILES['imagem_capa'], 'capas');
            } else {
                // Fallback
                $diretorio = '../assets/uploads/capas/';
                if (!file_exists($diretorio)) mkdir($diretorio, 0777, true);
                $extensao = strtolower(pathinfo($_FILES['imagem_capa']['name'], PATHINFO_EXTENSION));
                $nome_arquivo = uniqid() . '.' . $extensao;
                if (move_uploaded_file($_FILES['imagem_capa']['tmp_name'], $diretorio . $nome_arquivo)) {
                    $imagem_capa = 'assets/uploads/capas/' . $nome_arquivo;
                }
            }
        }
        
        if ($titulo) {
            $stmt = $db->prepare("
                INSERT INTO materiais (titulo, descricao, tipo, categoria_id, arquivo_path, link_externo, 
                                      imagem_capa, autor, turma_id, publicado_por, destaque, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'ativo')
            ");
            $stmt->execute([
                $titulo, $descricao, $tipo, $categoria_id, $arquivo_path, $link_externo,
                $imagem_capa, $autor, $turma_id, $admin_id, $destaque
            ]);
            $_SESSION['mensagem'] = "Material publicado com sucesso!";
            registrarLog('criar_material', "Publicou material: $titulo");
        }
        header('Location: materiais.php');
        exit;
    }
    
    if (isset($_POST['editar_material'])) {
        $id = $_POST['id'];
        $titulo = trim($_POST['titulo']);
        $descricao = trim($_POST['descricao']);
        $tipo = $_POST['tipo'];
        $categoria_id = $_POST['categoria_id'] ?: null;
        $autor = trim($_POST['autor']);
        $turma_id = $_POST['turma_id'] ?: null;
        $link_externo = trim($_POST['link_externo']);
        $destaque = isset($_POST['destaque']) ? 1 : 0;
        $status = $_POST['status'];
        
        $stmt = $db->prepare("
            UPDATE materiais SET titulo=?, descricao=?, tipo=?, categoria_id=?, autor=?, 
            turma_id=?, link_externo=?, destaque=?, status=? WHERE id=?
        ");
        $stmt->execute([$titulo, $descricao, $tipo, $categoria_id, $autor, $turma_id, $link_externo, $destaque, $status, $id]);
        $_SESSION['mensagem'] = "Material atualizado!";
        registrarLog('editar_material', "Editou material ID: $id");
        header('Location: materiais.php');
        exit;
    }
    
    if (isset($_POST['excluir_material'])) {
        $id = $_POST['id'];
        $stmt = $db->prepare("DELETE FROM materiais WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['mensagem'] = "Material excluído!";
        registrarLog('excluir_material', "Excluiu material ID: $id");
        header('Location: materiais.php');
        exit;
    }
}

// Buscar categorias
$categorias = $db->query("SELECT * FROM categorias ORDER BY nome")->fetchAll();

// Buscar turmas
$turmas = $db->query("SELECT id, nome FROM turmas ORDER BY nome")->fetchAll();

// Buscar materiais
$stmt = $db->prepare("
    SELECT m.*, c.nome as categoria_nome, 
           (SELECT COUNT(*) FROM materiais_acessos WHERE material_id = m.id) as total_acessos
    FROM materiais m
    LEFT JOIN categorias c ON m.categoria_id = c.id
    ORDER BY m.destaque DESC, m.data_publicacao DESC
");
$stmt->execute();
$materiais = $stmt->fetchAll();

$mensagem = $_SESSION['mensagem'] ?? null;
unset($_SESSION['mensagem']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca - Kindergarten Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .material-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        .material-card:hover { transform: translateY(-3px); box-shadow: 0 5px 20px rgba(0,0,0,0.15); }
        .material-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px; }
        .material-titulo { font-size: 1.1rem; font-weight: bold; color: #ff983e; }
        .tipo-badge {
            display: inline-block; padding: 3px 10px; border-radius: 15px; font-size: 0.7rem; font-weight: 600;
        }
        .tipo-livro { background: #17a2b8; color: white; }
        .tipo-artigo { background: #28a745; color: white; }
        .tipo-video { background: #dc3545; color: white; }
        .tipo-apostila { background: #ffc107; color: #333; }
        .tipo-recurso { background: #6c757d; color: white; }
        
        .form-container { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 15px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Poppins', sans-serif; }
        textarea.form-control { resize: vertical; min-height: 100px; }
        
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; margin: 5% auto; width: 90%; max-width: 600px; border-radius: 10px; }
        .modal-header { padding: 20px; background: linear-gradient(135deg, #ff983e, #ff4d4d); color: white; border-radius: 10px 10px 0 0; }
        .modal-body { padding: 20px; max-height: 60vh; overflow-y: auto; }
        .modal-footer { padding: 20px; border-top: 1px solid #ddd; display: flex; justify-content: flex-end; gap: 10px; }
        .close { float: right; font-size: 28px; cursor: pointer; color: white; }
        
        .preview-image { width: 80px; height: 80px; object-fit: cover; border-radius: 5px; }
        .destaque-star { color: #ffc107; }
        .alert-success { background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="header-content">
            <div class="logo"> Kindergarten Admin</div>
            <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
            <div class="user-info">
                <span><?= htmlspecialchars($_SESSION[SESSION_ADMIN_NOME]) ?></span>
                <a href="logout.php" class="btn-sair">Sair</a>
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
            <li class="active"><a href="materiais.php"><i class="fas fa-book"></i> Biblioteca</a></li>
        </ul>
    </div>

    <div class="admin-main">
        <?php if ($mensagem): ?>
            <div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-book"></i> Biblioteca de Recursos</h3>
                <button class="btn btn-primary" onclick="abrirModalNovo()">
                    <i class="fas fa-plus"></i> Novo Material
                </button>
            </div>
            <div class="card-body">
                <div class="form-container">
                    <h4><i class="fas fa-filter"></i> Filtros</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tipo</label>
                            <select id="filtroTipo" class="form-control" onchange="filtrar()">
                                <option value="">Todos</option>
                                <option value="livro">📚 Livro</option>
                                <option value="artigo">📄 Artigo</option>
                                <option value="video">🎥 Vídeo</option>
                                <option value="apostila">📖 Apostila</option>
                                <option value="recurso">📎 Recurso</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Categoria</label>
                            <select id="filtroCategoria" class="form-control" onchange="filtrar()">
                                <option value="">Todas</option>
                                <?php foreach($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select id="filtroStatus" class="form-control" onchange="filtrar()">
                                <option value="">Todos</option>
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="listaMateriais">
                    <?php if (count($materiais) > 0): ?>
                        <?php foreach($materiais as $material): ?>
                            <div class="material-card" data-tipo="<?= $material['tipo'] ?>" data-categoria="<?= $material['categoria_id'] ?>" data-status="<?= $material['status'] ?>">
                                <div class="material-header">
                                    <div>
                                        <span class="material-titulo">
                                            <?php if($material['destaque']): ?><i class="fas fa-star destaque-star"></i> <?php endif; ?>
                                            <?= htmlspecialchars($material['titulo']) ?>
                                        </span>
                                        <span class="tipo-badge tipo-<?= $material['tipo'] ?>">
                                            <?php 
                                            $tipos = ['livro'=>'📚 Livro', 'artigo'=>'📄 Artigo', 'video'=>'🎥 Vídeo', 'apostila'=>'📖 Apostila', 'recurso'=>'📎 Recurso'];
                                            echo $tipos[$material['tipo']];
                                            ?>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="badge <?= $material['status'] == 'ativo' ? 'badge-success' : 'badge-danger' ?>">
                                            <?= ucfirst($material['status']) ?>
                                        </span>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 15px;">
                                    <?php if($material['imagem_capa'] && file_exists('../' . $material['imagem_capa'])): ?>
                                        <img src="../<?= $material['imagem_capa'] ?>" class="preview-image">
                                    <?php else: ?>
                                        <div class="preview-image" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-book" style="font-size: 2rem; color: #ccc;"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div style="flex: 1;">
                                        <p><strong>Autor:</strong> <?= htmlspecialchars($material['autor'] ?: 'Não informado') ?></p>
                                        <p><strong>Categoria:</strong> <?= htmlspecialchars($material['categoria_nome'] ?: 'Sem categoria') ?></p>
                                        <p><strong>Publicado:</strong> <?= date('d/m/Y', strtotime($material['data_publicacao'])) ?></p>
                                        <p><strong>Acessos:</strong> <i class="fas fa-eye"></i> <?= $material['total_acessos'] ?></p>
                                    </div>
                                </div>
                                <div class="btn-group" style="margin-top: 15px;">
                                    <button class="btn btn-warning btn-sm" onclick='editarMaterial(<?= json_encode($material) ?>)'>
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="excluirMaterial(<?= $material['id'] ?>, '<?= htmlspecialchars($material['titulo']) ?>')">
                                        <i class="fas fa-trash"></i> Excluir
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; padding: 40px; color: #666;">
                            <i class="fas fa-book-open" style="font-size: 3rem;"></i><br>
                            Nenhum material cadastrado ainda.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>


    <div id="materialModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="fecharModal()">&times;</span>
                <h3 id="modalTitle"><i class="fas fa-plus"></i> Novo Material</h3>
            </div>
            <form method="POST" enctype="multipart/form-data" id="materialForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="materialId">
                    <input type="hidden" name="editar_material" id="editarFlag" value="0">
                    
                    <div class="form-group">
                        <label>Título *</label>
                        <input type="text" name="titulo" id="titulo" required class="form-control">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tipo *</label>
                            <select name="tipo" id="tipo" required class="form-control">
                                <option value="livro">📚 Livro</option>
                                <option value="artigo">📄 Artigo</option>
                                <option value="video">🎥 Vídeo</option>
                                <option value="apostila">📖 Apostila</option>
                                <option value="recurso">📎 Recurso</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Categoria</label>
                            <select name="categoria_id" id="categoria_id" class="form-control">
                                <option value="">Selecione</option>
                                <?php foreach($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Autor</label>
                            <input type="text" name="autor" id="autor" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Turma (opcional)</label>
                            <select name="turma_id" id="turma_id" class="form-control">
                                <option value="">Todas as turmas</option>
                                <?php foreach($turmas as $turma): ?>
                                    <option value="<?= $turma['id'] ?>"><?= htmlspecialchars($turma['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Descrição</label>
                        <textarea name="descricao" id="descricao" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Link Externo (se aplicável)</label>
                        <input type="url" name="link_externo" id="link_externo" class="form-control" placeholder="https://...">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Arquivo (PDF, DOC, MP4)</label>
                            <input type="file" name="arquivo" id="arquivo" class="form-control">
                            <small>Max 10MB</small>
                        </div>
                        <div class="form-group">
                            <label>Imagem de Capa</label>
                            <input type="file" name="imagem_capa" id="imagem_capa" class="form-control" accept="image/*">
                            <small>JPG, PNG, GIF. Max 2MB</small>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="destaque" id="destaque" value="1"> 
                                <i class="fas fa-star"></i> Destacar este material
                            </label>
                        </div>
                        <div class="form-group" id="statusGroup" style="display: none;">
                            <label>Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="fecharModal()">Cancelar</button>
                    <button type="submit" name="criar_material" id="submitBtn" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModalNovo() {
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus"></i> Novo Material';
            document.getElementById('materialForm').reset();
            document.getElementById('materialId').value = '';
            document.getElementById('editarFlag').value = '0';
            document.getElementById('submitBtn').name = 'criar_material';
            document.getElementById('statusGroup').style.display = 'none';
            document.getElementById('materialModal').style.display = 'block';
        }
        
        function editarMaterial(material) {
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> Editar Material';
            document.getElementById('materialId').value = material.id;
            document.getElementById('editarFlag').value = '1';
            document.getElementById('titulo').value = material.titulo;
            document.getElementById('tipo').value = material.tipo;
            document.getElementById('categoria_id').value = material.categoria_id || '';
            document.getElementById('autor').value = material.autor || '';
            document.getElementById('turma_id').value = material.turma_id || '';
            document.getElementById('descricao').value = material.descricao || '';
            document.getElementById('link_externo').value = material.link_externo || '';
            document.getElementById('destaque').checked = material.destaque == 1;
            document.getElementById('status').value = material.status;
            document.getElementById('statusGroup').style.display = 'block';
            document.getElementById('submitBtn').name = 'editar_material';
            document.getElementById('materialModal').style.display = 'block';
        }
        
        function excluirMaterial(id, titulo) {
            if(confirm(`Tem certeza que deseja excluir "${titulo}"?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = '<input type="hidden" name="id" value="' + id + '"><input type="hidden" name="excluir_material" value="1">';
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function filtrar() {
            const tipo = document.getElementById('filtroTipo').value;
            const categoria = document.getElementById('filtroCategoria').value;
            const status = document.getElementById('filtroStatus').value;
            const cards = document.querySelectorAll('.material-card');
            
            cards.forEach(card => {
                let mostrar = true;
                if (tipo && card.dataset.tipo !== tipo) mostrar = false;
                if (categoria && card.dataset.categoria != categoria) mostrar = false;
                if (status && card.dataset.status !== status) mostrar = false;
                card.style.display = mostrar ? 'block' : 'none';
            });
        }
        
        function fecharModal() { 
            document.getElementById('materialModal').style.display = 'none'; 
        }
        
        // Fechar modal ao clicar fora
        window.onclick = function(event) { 
            if (event.target == document.getElementById('materialModal')) {
                fecharModal();
            }
        }
        
        // Menu toggle para mobile
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        if (menuToggle) {
            menuToggle.addEventListener('click', function() { 
                sidebar.classList.toggle('active'); 
            });
        }
    </script>
</body>
</html>