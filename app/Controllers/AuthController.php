<?php

// Importa a conexão com o banco de dados.
require_once __DIR__ . '/../../config/database.php';

// Importa funções auxiliares de autenticação e sessão.
require_once __DIR__ . '/../Middleware/auth.php';

class AuthController
{
    // Armazena a conexão PDO.
    private PDO $pdo;

    public function __construct()
    {
        // Recupera a conexão criada em database.php.
        global $pdo;

        // Disponibiliza a conexão para os métodos da classe.
        $this->pdo = $pdo;
    }

    public function exibirLogin(): void
    {
        // Se o usuário já estiver logado, redireciona para o dashboard.
        if (usuarioAutenticado()) {
            header('Location: ?controller=auth&action=dashboard');
            exit;
        }

        // Recupera mensagens temporárias da sessão.
        $erro = $_SESSION['erro_login'] ?? null;
        $mensagem = $_SESSION['mensagem'] ?? null;

        // Remove as mensagens para que apareçam somente uma vez.
        unset($_SESSION['erro_login'], $_SESSION['mensagem']);

        // Carrega a tela de login.
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function entrar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=auth&action=login');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $senha = trim($_POST['senha'] ?? ''); // Com o trim()

        if ($email === '' || $senha === '') {
            $_SESSION['erro_login'] = 'Informe o e-mail e a senha.';
            header('Location: ?controller=auth&action=login');
            exit;
        }

        $sql = 'SELECT id, nome, email, senha, perfil, status FROM usuarios WHERE email = :email LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Valida usuário, status e senha
        if (!$usuario || $usuario['status'] !== 'ativo' || !password_verify($senha, $usuario['senha'])) {
            $_SESSION['erro_login'] = 'E-mail ou senha inválidos.';
            header('Location: ?controller=auth&action=login');
            exit;
        }

        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email'],
            'perfil' => $usuario['perfil'],
        ];

        header('Location: ?controller=auth&action=dashboard');
        exit;
    }

    public function dashboard(): void
    {
        // Bloqueia o acesso caso o usuário não esteja logado.
        exigirAutenticacao();

        // Recupera os dados do usuário autenticado.
        $usuario = usuarioAtual();

        // Carrega a página interna.
        require __DIR__ . '/../Views/dashboard/index.php';
    }

    public function logout(): void
    {
        // Remove os dados armazenados na sessão.
        $_SESSION = [];

        // Remove o cookie da sessão, caso esteja sendo utilizado.
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Encerra a sessão atual.
        session_destroy();

        // Inicia nova sessão apenas para enviar a mensagem de retorno.
        session_start();

        // Mensagem exibida após o logout.
        $_SESSION['mensagem'] = 'Sessão encerrada com sucesso.';

        // Retorna para a tela de login.
        header('Location: ?controller=auth&action=login');
        exit;
    }
}