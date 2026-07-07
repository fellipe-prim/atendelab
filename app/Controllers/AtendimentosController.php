<?php

class AtendimentosController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    private function json(array $dados, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    }

    public function listar(): void
    {
        // Ajustado para 'hora_atendimento' para bater com seu banco
        $sql = 'SELECT a.id, p.nome AS pessoa_nome, 
                       t.nome AS tipo_nome, 
                       u.nome AS responsavel_nome,
                       a.descricao, a.status, 
                       a.data_atendimento, a.hora_atendimento,
                       a.observacao_final
                FROM atendimentos a
                INNER JOIN pessoas p ON a.pessoa_id = p.id
                INNER JOIN tipos_atendimentos t ON a.tipo_atendimento_id = t.id
                INNER JOIN usuarios u ON a.usuario_id = u.id
                ORDER BY a.id DESC';
        
        $this->json($this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC));
    }

    public function buscar(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->json(['erro' => 'ID inválido.'], 400);
            return;
        }

        $stmt = $this->pdo->prepare(
            'SELECT a.*, p.nome AS pessoa_nome, 
                    t.nome AS tipo_nome, u.nome AS responsavel_nome
             FROM atendimentos a
             INNER JOIN pessoas p ON a.pessoa_id = p.id
             INNER JOIN tipos_atendimentos t ON a.tipo_atendimento_id = t.id
             INNER JOIN usuarios u ON a.usuario_id = u.id
             WHERE a.id = :id'
        );
        
        $stmt->execute(['id' => $id]);
        $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$atendimento) {
            $this->json(['erro' => 'Atendimento não encontrado.'], 404);
            return;
        }

        $this->json($atendimento);
    }

    public function criar(): void
    {
        $pessoaId = filter_var($_POST['pessoa_id'] ?? null, FILTER_VALIDATE_INT);
        $tipoId = filter_var($_POST['tipo_atendimento_id'] ?? null, FILTER_VALIDATE_INT);
    
        if (session_status() === PHP_SESSION_NONE) session_start();
        $usuarioId = $_SESSION['usuario']['id'] ?? null;
        
        // 3. Pega os outros campos
        $descricao = trim($_POST['descricao'] ?? '');
        $data = $_POST['data_atendimento'] ?? '';
        $hora = $_POST['hora_atendimento'] ?? ''; 
        $status = $_POST['status'] ?? 'aberto';

        if (!$pessoaId || !$tipoId || !$usuarioId || $descricao === '' || $data === '' || $hora === '') {
            $this->json(['erro' => 'Preencha os campos obrigatórios.'], 422);
            return;
        }

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO atendimentos 
                (pessoa_id, tipo_atendimento_id, usuario_id, descricao, 
                 status, data_atendimento, hora_atendimento)
                VALUES 
                (:pessoa_id, :tipo_id, :usuario_id, :descricao, 
                 :status, :data_atendimento, :hora_atendimento)'
            );

            $stmt->execute([
                'pessoa_id' => $pessoaId,
                'tipo_id' => $tipoId,
                'usuario_id' => $usuarioId,
                'descricao' => $descricao,
                'status' => $status,
                'data_atendimento' => $data,
                'hora_atendimento' => $hora,
            ]);

            $this->json(['mensagem' => 'Atendimento registrado com sucesso.'], 201);

        } catch (PDOException $e) {
            $this->json(['erro' => 'Erro no banco: ' . $e->getMessage()], 400);
        }
    }

    public function alterarStatus(): void
    {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? '';
        $observacao = trim($_POST['observacao_final'] ?? '');

        if (!$id || !in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {
            $this->json(['erro' => 'ID ou status inválido.'], 422);
            return;
        }

        if ($status === 'concluido' && $observacao === '') {
            $this->json(['erro' => 'Informe a observação final para concluir.'], 422);
            return;
        }

        try {
            $stmt = $this->pdo->prepare(
                'UPDATE atendimentos SET status = :status, observacao_final = :observacao WHERE id = :id'
            );
            
            $stmt->execute([
                'id' => $id,
                'status' => $status,
                'observacao' => $observacao !== '' ? $observacao : null,
            ]);

            $this->json(['mensagem' => 'Status atualizado com sucesso.']);

        } catch (PDOException $e) {
            $this->json(['erro' => 'Erro no banco: ' . $e->getMessage()], 400);
        }
    }
}