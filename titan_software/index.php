<?php
//iniciar a sessão e definir o fuso horário local
session_start();
date_default_timezone_set('America/Sao_Paulo');

//controllers
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/ServicoController.php';

//pega os parâmetros da url e instancia em variaveis especificas
$acao   = $_GET['acao'] ?? '';
$pagina = $_GET['pagina'] ?? '';

//instancia os controllers
$authController    = new AuthController();
$servicoController = new ServicoController();

//executar as açoes solicitadas
if ($acao === 'login') {
    $authController->login();
    exit;
}

//cadastrar usuario novo
if ($acao === 'cadastrar_usuario') {
    $authController->cadastrar();
    exit;
}

//deslogar do sistema
if ($acao === 'logout') {
    $authController->logout();
    exit;
}

//insert de novo serviço
if ($acao === 'cadastrar_servico') {
    $servicoController->cadastrar();
    exit;
}

//edição do serviço
if ($acao === 'atualizar_servico') {
    $servicoController->atualizar();
    exit;
}

//excluir o serviço
if ($acao === 'excluir_servico') {
    $servicoController->excluir();
    exit;
}

//finalizar serviço
if ($acao === 'finalizar_servico') {
    $servicoController->finalizar();
    exit;
}

//carrega as paginas solicitadas de acordo com a pagina
if ($pagina === 'cadastro_usuario') {
    require_once __DIR__ . '/views/cadastro_usuario.php';
    exit;
}

if ($pagina === 'operacoes_servico') {
    $servico = null;
    $id = (int) ($_GET['id'] ?? 0);

    //se solicita edição, busca o serviço
    if ($id) {
        $servico = $servicoController->buscarPorId($id);

        if (!$servico) {
            header('Location: /titan_software/index.php?erro=Serviço não encontrado');
            exit;
        }
    }

    require_once __DIR__ . '/views/operacoes_servico.php';
    exit;
}

//verificar se o usuario ta logado
if (isset($_SESSION['gbl_id'])) {
    //carrega os dados utilizados no dashboard
    $servicos      = $servicoController->listar();
    $totalUsuario  = $servicoController->totalPorUsuario($_SESSION['gbl_id']);
    $totalUsuarioF = $servicoController->totalFinalizadoPorUsuario($_SESSION['gbl_id']);
    $pendentes     = $servicoController->pendentesPorUsuario($_SESSION['gbl_id']);

    //se logado carrega a pagina do dashboard
    require_once __DIR__ . '/views/dashboard.php';
} else {
    //se usuario não logado, manda para o form de login
    require_once __DIR__ . '/views/login.php';
}