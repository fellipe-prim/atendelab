<?php

// o middleware centraliza o início da sessão, a verificação do usuário autenticado e o
// redirecionamento para o login quando o acesso não estiver autorizado.

// Evita iniciar a mesma sessão mais de uma vez.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Confirma se existe um usuário válido salvo na sessão.
function usuarioAutenticado(): bool
{
    return isset($_SESSION['usuario'])
        && is_array($_SESSION['usuario']);
}

// Bloqueia o acesso e redireciona para o login.
function exigirAutenticacao(): void
{
    if (!usuarioAutenticado()) {
        $_SESSION['mensagem'] =
            'Faça login para acessar a área restrita.';

        header('location: ?controller=auth&action=login');
        exit;
    }
}

// Disponibiliza os dados do usuário logado para a página interna.
function usuarioAtual(): ?array
{
    return $_SESSION['usuario'] ?? null;
}