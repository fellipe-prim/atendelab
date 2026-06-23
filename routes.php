<?php

require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';
require_once __DIR__ . '/app/Middleware/auth.php';

$controller = $_GET['controller'] ?? 'auth';
$action     = $_GET['action']     ?? 'login';

if ($controller === 'auth') {

    $c = new AuthController();
    switch ($action) {
        case 'login':     $c->exibirLogin(); break;
        case 'entrar':    $c->entrar();      break;
        case 'dashboard': $c->dashboard();   break;
        case 'logout':    $c->logout();      break;
        default: echo 'Ação não encontrada.';
    }

} elseif ($controller === 'usuarios') {
    $c = new UsuariosController();
    switch ($action) {
        case 'listar':    $c->listar();      break;
        case 'buscar':    $c->buscarPorId(); break;
        case 'criar':     $c->criar();       break;
        case 'atualizar': $c->atualizar();   break;
        case 'excluir':   $c->excluir();     break;
        default: echo 'Ação não encontrada.';
    }

} elseif ($controller === 'pessoas') {

    exigirAutenticacao();
    $c = new PessoasController();
    switch ($action) {
        case 'listar':    $c->listar();      break;
        case 'buscar':    $c->buscarPorId(); break;
        case 'criar':     $c->criar();       break;
        case 'atualizar': $c->atualizar();   break;
        case 'excluir':   $c->excluir();     break;
        default: echo 'Ação não encontrada.';
    }

} elseif ($controller === 'tipos_atendimentos') {

    exigirAutenticacao();
    $c = new TiposAtendimentosController();
    switch ($action) {
        case 'listar':    $c->listar();      break;
        case 'buscar':    $c->buscarPorId(); break;
        case 'criar':     $c->criar();       break;
        case 'atualizar': $c->atualizar();   break;
        case 'excluir':   $c->excluir();     break;
        default: echo 'Ação não encontrada.';
    }

} elseif ($controller === 'atendimentos') {

    exigirAutenticacao();
    $c = new AtendimentosController();
    switch ($action) {
        case 'listar':           $c->listar();          break;
        case 'buscar':           $c->buscarPorId();     break;
        case 'criar':            $c->criar();           break;
        case 'atualizar_status': $c->atualizarStatus(); break;
        default: echo 'Ação não encontrada.';
    }

} else {
    echo '<h1>AtendeLab</h1>';
    echo '<p>Use ?controller=auth&action=login para acessar.</p>';
}