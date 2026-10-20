# Kindergarten - Sistema de Matrículas e Mensalidades

Sistema web completo para gestão de um jardim de infância, com foco em matrícula online, mensalidades, gestão de alunos, turmas, pagamentos, mensagens e comunicação interna com pais e encarregados.

A aplicação foi desenvolvida em PHP com MySQL/MariaDB e oferece uma interface pública para divulgação da escola e um painel administrativo para gestão operacional do estabelecimento.

## Visão geral

O projeto reúne duas partes principais:

- Front-end público: apresentação da escola, informações sobre programas, matrículas, mensalidades, contacto e perfil do aluno
- Painel administrativo: gestão de alunos, turmas, pagamentos, mensagens, comunicados, relatórios e bibliotecas de materiais

Ela foi pensada para uso em ambientes escolares com necessidades de organização, registro e acompanhamento do processo de matrícula e do pagamento mensal.

## Funcionalidades principais

### Portal público
- Página inicial com apresentação da escola e destaques institucionais
- Seção “Sobre nós” com história, valores e diferenciais
- Seção “Programa” para detalhar métodos pedagógicos e atividades
- Formulário de matrícula com coleta de dados do aluno e encarregado
- Página de mensalidades com informações de planos e valores
- Página de contacto para envio de mensagens pela web
- Perfil do aluno para acompanhamento do processo escolar

### Área administrativa
- Login seguro para administradores
- Dashboard com estatísticas gerais
- Gestão de alunos e status de matrícula
- Gestão de turmas e organização pedagógica
- Registo e confirmação de pagamentos de matrícula e mensalidades
- Consulta e resposta de mensagens de contacto
- Publicação e gestão de comunicados
- Relatórios e histórico de atividades
- Gestão de materiais/documentos e conteúdos da escola
- Controle de acessos e logs administrativos

## Tecnologias utilizadas

- PHP 8+
- MySQL / MariaDB
- PDO para conexão com banco de dados
- HTML5
- CSS3
- JavaScript
- XAMPP (ambiente de desenvolvimento local recomendado)

## Estrutura do projeto

```text
Matriculas_e_Mensalidades_de_Jardim_de_Infancia/
├── admin/                     # Painel administrativo
│   ├── api/                  # Endpoints de ações administrativas
│   ├── assets/              # CSS específico do admin
│   ├── config/              # Configuração do painel
│   │   └── admin_config.php # Definições de sessão e permissões
│   ├── administradores.php  # Gestão de administradores
│   ├── alunos.php           # Gestão de alunos
│   ├── dashboard.php        # Dashboard principal
│   ├── login.php            # Login do painel
│   ├── logout.php           # Encerrar sessão
│   ├── materiais.php        # Gestão de materiais
│   ├── mensagens.php        # Mensagens recebidas
│   ├── pagamentos.php       # Gestão de pagamentos
│   ├── relatorios.php       # Relatórios
│   ├── turmas.php           # Gestão de turmas
│   └── ...
├── api/                      # APIs e processamentos do sistema
│   ├── processar_matricula.php
│   ├── processar_pagamento.php
│   ├── solicitar_pagamento.php
│   ├── enviar_contato.php
│   └── ...
├── assets/                   # Arquivos estáticos e uploads
│   ├── documentos/
│   ├── fotos/
│   └── uploads/
├── config/
│   └── database.php          # Configuração da conexão com o banco
├── css/                      # Estilos globais do site público
├── imagens/                  # Imagens e mídias do portal
├── includes/
│   └── functions.php         # Funções auxiliares reutilizáveis
├── index.php                 # Página inicial
├── sobre.php                 # Página sobre a escola
├── programa.php              # Página do programa
├── matriculas.php            # Página e formulário de matrícula
├── mensalidades.php          # Página de mensalidades
├── contact.php               # Página de contacto
├── perfil_aluno.php          # Perfil do aluno
├── confirmacao.php           # Página de confirmação
├── kindergarten_db.sql       # Script SQL do banco de dados
├── logout.php                # Logout do site público
├── .htaccess                 # Apache config (se existir no ambiente)
├── LICENSE                   # Se houver
├── README.md                 # Documentação do projeto
└── readme.txt                # Arquivo auxiliar de uso rápido
```

## Requisitos do ambiente

Antes de instalar e executar o projeto, certifique-se de ter:

- PHP 8.0 ou superior
- Apache ou Nginx
- MySQL / MariaDB 10.4 ou superior
- XAMPP, WAMP ou ambiente equivalente
- Acesso ao banco de dados com usuário root/local ou outro configurado

## Instalação e configuração

### 1. Clonar ou copiar o projeto

Coloque a pasta do projeto dentro do diretório de publicação do servidor web, por exemplo:

```text
C:\xampp\htdocs\Matriculas_e_Mensalidades_de_Jardim_de_Infancia
```

### 2. Iniciar os serviços

No XAMPP, inicie:

- Apache
- MySQL

### 3. Criar/importar o banco de dados

Acesse o phpMyAdmin ou o cliente MySQL e importe o arquivo:

```text
kindergarten_db.sql
```

O script cria o banco de dados `kindergarten_db` e as tabelas necessárias, além de inserir dados iniciais de demonstração.

### 4. Configurar a conexão com o banco

O arquivo de conexão está em:

```php
config/database.php
```

Configuração padrão:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'kindergarten_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

Se o seu ambiente usar credenciais diferentes, ajuste os valores conforme a sua instalação.

### 5. Acessar a aplicação

Abra no navegador:

```text
http://localhost/Matriculas_e_Mensalidades_de_Jardim_de_Infancia/
```

A página inicial do site público será carregada.

## Painel administrativo

Acesse:

```text
http://localhost/Matriculas_e_Mensalidades_de_Jardim_de_Infancia/admin/login.php
```

### Credenciais de demonstração

O projeto inclui administrador de teste para acesso ao painel:

- Usuário: `lxrd`
- Senha: `12345`

Também há um usuário padrão do sistema gerado pelo script de instalação:

- Usuário: `admin`
- Senha: `admin123`

Importante: em ambiente real ou produção, altere as credenciais e configure uma senha forte antes de disponibilizar a aplicação.

## Fluxo principal de uso

### Para pais/en-carregados
1. Acessar a página inicial do site
2. Navegar para a área de matrículas
3. Preencher os dados do aluno e do encarregado
4. Enviar a solicitação de matrícula
5. Acompanhar status e pagamentos
6. Consultar informações de mensalidade e comunicação escolar

### Para administração
1. Fazer login no painel administrativo
2. Visualizar dashboard com indicadores gerais
3. Aprovar ou acompanhar matrículas
4. Confirmar pagamentos
5. Responder mensagens recebidas
6. Publicar comunicados
7. Gerenciar materiais, turmas e relatórios

## Banco de dados

O banco principal é `kindergarten_db`, e o script SQL cria tabelas como:

- `administradores`
- `admin_logs`
- `alunos`
- `encarregados`
- `turmas`
- `matriculas`
- `mensalidades`
- `pagamentos`
- `pagamentos_matricula`
- `mensagens_contato`
- `comunicados`
- `materiais`
- `documentos`
- `configuracoes`

A aplicação usa PDO com `ERRMODE_EXCEPTION`, `FETCH_ASSOC` e `EMULATE_PREPARES` desativado para maior segurança e organização das consultas.

## Segurança

Observações importantes:

- O sistema usa sessões para autenticação administrativa
- As senhas são armazenadas com `password_hash()`
- A conexão com banco é centralizada em `config/database.php`
- O painel administrativo exige autenticação antes de acessar páginas sensíveis

Embora o sistema tenha medidas básicas de segurança, em produção recomenda-se:

- Usar credenciais fortes
- Trocar a senha padrão do banco
- Configurar ambiente em HTTPS
- Limitar acesso à área administrativa
- Ajustar permissões de usuário e arquivos
- Realizar backup periódico do banco

## Personalização

Você pode adaptar a aplicação para a sua instituição alterando:

- textos e imagens de apresentação
- valores e planos de mensalidade
- informações da escola e programas
- logo e identidade visual
- mensagens de contacto e comunicação

Os arquivos com maior destaque para personalização são:

- `index.php`
- `sobre.php`
- `programa.php`
- `matriculas.php`
- `mensalidades.php`
- `contact.php`
- `css/style.css`
- `admin/assets/css/admin.css`

## Solução de problemas comuns

### Erro de conexão com o banco
Verifique:

- Se o MySQL está ativo no XAMPP
- Se o banco `kindergarten_db` foi importado corretamente
- Se as credenciais em `config/database.php` estão corretas

### Página em branco ou erro 500
Confirme:

- Se o PHP está funcionando corretamente
- Se o Apache está rodando
- Se há erros de sintaxe em arquivos PHP
- Se as extensões do PHP necessárias estão habilitadas

### Não consegue acessar o painel
Verifique:

- Se a sessão foi iniciada corretamente
- Se o usuário administrativo existe no banco
- Se a senha foi salva com hash válido

## Observações finais

Este projeto é uma solução prática para gestão escolar de um jardim de infância e foi estruturado para ser facilmente adaptado a diferentes necessidades educacionais.

Ele combina presença digital institucional, processo de matrícula e gestão administrativa em um único sistema, tornando a operação da escola mais organizada e acessível.

## Licença

Este projeto foi desenvolvido para fins educacionais e de gestão escolar. Ajuste a licença conforme a necessidade do seu ambiente de uso.

## Contato e suporte

Para dúvidas sobre instalação, configuração ou customização, consulte a estrutura do projeto e os arquivos de configuração principais.

---

Desenvolvido para apoiar a gestão escolar de matrículas, mensalidades e comunicação com famílias.
