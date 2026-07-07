<?php
// app/Controllers/FrontendController.php

require_once __DIR__ . '/../Middleware/auth.php';

class FrontendController {

    public function __construct() {
        // Garante que só quem está logado acessa as páginas
        exigirAutenticacao();
    }

    public function pessoas() {
        $tituloPagina = 'Gerenciar Pessoas';
        require __DIR__ . '/../Views/pessoas/index.php';
    }

    public function tipos() {
        $tituloPagina = 'Tipos de Atendimento';
        require __DIR__ . '/../Views/tipos-atendimentos/index.php';
    }

    public function atendimentos() {
        $tituloPagina = 'Registrar Atendimentos';
        require __DIR__ . '/../Views/atendimentos/index.php';
    }
}