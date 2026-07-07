<?php

require_once __DIR__ . '/app/Middleware/auth.php';
require_once __DIR__ . '/app/Controllers/AuthController.php';

$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'login';

function responderRotaNaoEncontrada($mensagem) {
    http_response_code(404);
    echo $mensagem;
    exit;
}

if ($controller === 'auth') {
    $authController = new AuthController();
    switch ($action) {
        case 'login':
            $authController->exibirLogin();
            break;
        case 'entrar':
            $authController->entrar();
            break;
        case 'dashboard':
            exigirAutenticacao();
            $authController->dashboard();
            break;
        case 'logout':
            $authController->logout();
            break;
        default:
            responderRotaNaoEncontrada('Ação de autenticação não encontrada.');
            break;
    }
    exit;
}

switch ($controller) {
    case 'frontend':
        require_once __DIR__ . '/app/Controllers/FrontendController.php';
        $frontendController = new FrontendController();
        if (!method_exists($frontendController, $action)) {
            responderRotaNaoEncontrada('Página não encontrada.');
        }
        $frontendController->$action();
        break;

    case 'pessoas':
        exigirAutenticacao();
        require_once __DIR__ . '/app/Controllers/PessoasController.php';
        $pessoasController = new PessoasController();
        if (!method_exists($pessoasController, $action)) {
            responderRotaNaoEncontrada('Ação de pessoas não encontrada.');
        }
        $pessoasController->$action();
        break;

    case 'tipos':
        exigirAutenticacao();
        require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
        $tiposController = new TiposAtendimentosController();
        switch ($action) {
            case 'listar':
                $tiposController->listar();
                break;
            case 'buscarPorId':
            case 'buscar':
                $tiposController->buscar();
                break;
            case 'criar':
                $tiposController->criar();
                break;
            case 'atualizar':
                $tiposController->atualizar();
                break;
            case 'inativar':
                $tiposController->inativar();
                break;
            default:
                responderRotaNaoEncontrada('Ação de tipos de atendimento não encontrada.');
                break;
        }
        break;

    case 'atendimentos':
        exigirAutenticacao();
        require_once __DIR__ . '/app/Controllers/AtendimentosController.php';
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
            case 'alterarStatus':
            case 'atualizarStatus':
                $atendimentosController->alterarStatus();
                break;
            case 'opcoesFormulario':
                $atendimentosController->opcoesFormulario();
                break;
            default:
                responderRotaNaoEncontrada(
                    'Ação de atendimentos não encontrada.'
                );
                break;
        }
        break;

    case 'usuarios':
        exigirAutenticacao();
        require_once __DIR__ . '/app/Controllers/UsuariosController.php';
        $usuariosController = new UsuariosController();
        if (!method_exists($usuariosController, $action)) {
            responderRotaNaoEncontrada('Ação de usuários não encontrada.');
        }
        $usuariosController->$action();
        break;

    default:
        responderRotaNaoEncontrada('Controller não encontrado.');
        break;
}