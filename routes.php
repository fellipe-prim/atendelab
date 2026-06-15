<?php

require_once __DIR__ . '/Controllers/UsuarioController.php';
require_once __DIR__ . '/Controllers/PessoasController.php';
require_once __DIR__ . '/Controllers/AtendimentosController.php';
require_once __DIR__ . '/Controllers/TiposAtendimentosController.php';

$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

if ($controller === 'usuarios') {

    $usuariosController = new UsuariosController();

    switch ($action) {
        case 'listar':
            $usuariosController->listar();
            break;

        case 'buscar':
            $usuariosController->buscarPorId();
            break;

        case 'criar':
            $usuariosController->criar();
            break;

        case 'atualizar':
            $usuariosController->atualizar();
            break;

        case 'excluir':
            $usuariosController->excluir();
            break;

        default:
            echo 'Ação de usuários não encontrada';
            break;
    }

} elseif ($controller === 'pessoas') {

    $pessoasController = new PessoasController();

    switch ($action) {

        case 'listar':
            $pessoasController->listar();
            break;

        case 'buscar':
            $pessoasController->buscarPorId();
            break;

        case 'criar':
            $pessoasController->criar();
            break;

        case 'atualizar':
            $pessoasController->atualizar();
            break;

        case 'excluir':
            $pessoasController->excluir();
            break;

        default:
            echo 'Ação de pessoas não encontrada';
            break;
    }

} 
elseif ($controller === 'atendimentos') {

    $atendimentosController = new AtendimentosController();

    switch ($action) {

        case 'listar':
            $atendimentosController->listar();
            break;

        case 'visualizar':
            $atendimentosController->visualizar();
            break;

        case 'criar':
            $atendimentosController->criar();
            break;

        case 'atualizarStatus':
            $atendimentosController->atualizarStatus();
            break;
    }
}elseif ($controller === 'tiposatendimentos') {
    $controllerObj = new TiposAtendimentosController();

    switch ($action) {
        case 'listar':
            $controllerObj->listar();
            break;

        case 'buscar':
            $controllerObj->buscarPorId();
            break;

        case 'criar':
            $controllerObj->criar();
            break;

        case 'atualizar':
            $controllerObj->atualizar();
            break;

        case 'excluir':
            $controllerObj->excluir();
            break;
    }
}else {

    echo '<h1>Atendelab</h1>';
    echo '<p>Projeto em execução.</p>';

    echo '<p>Usuários:</p>';
    echo '<ul>';
    echo '<li>?controller=usuarios&action=listar</li>';
    echo '</ul>';

    echo '<p>Pessoas:</p>';
    echo '<ul>';
    echo '<li>?controller=pessoas&action=listar</li>';
    echo '</ul>';
}