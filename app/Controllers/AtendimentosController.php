<?php

class AtendimentosController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    public function listar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $sql = 'SELECT
                    a.id,
                    p.nome AS pessoa,
                    t.nome AS tipo,
                    u.nome AS atendente,
                    a.data_atendimento,
                    a.hora_atendimento,
                    a.status,
                    a.descricao
                FROM atendimentos a
                JOIN pessoas p ON a.pessoa_id = p.id
                JOIN tipos_atendimentos t ON a.tipo_atendimento = t.id
                JOIN usuarios u ON a.usuario_id = u.id
                ORDER BY a.data_atendimento DESC, a.hora_atendimento DESC';

        $stmt         = $this->pdo->query($sql);
        $atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($atendimentos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function buscarPorId(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        $sql = 'SELECT * FROM atendimentos WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$atendimento) {
            http_response_code(404);
            echo json_encode(['erro' => 'Atendimento não encontrado.']);
            return;
        }

        echo json_encode($atendimento, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $pessoa_id        = filter_input(INPUT_POST, 'pessoa_id',        FILTER_VALIDATE_INT);
        $tipo_atendimento = filter_input(INPUT_POST, 'tipo_atendimento',  FILTER_VALIDATE_INT);
        $usuario_id       = filter_input(INPUT_POST, 'usuario_id',        FILTER_VALIDATE_INT);
        $data_atendimento =              $_POST['data_atendimento'] ?? null;
        $hora_atendimento =              $_POST['hora_atendimento'] ?? null;
        $descricao        = trim(        $_POST['descricao']        ?? '');
        $observacao       = trim(        $_POST['observacao']       ?? '');

        if (!$pessoa_id || !$tipo_atendimento || !$usuario_id || !$data_atendimento) {
            http_response_code(400);
            echo json_encode(['erro' => 'Pessoa, tipo, atendente e data são obrigatórios.']);
            return;
        }

        try {
            $sql = 'INSERT INTO atendimentos
                        (pessoa_id, tipo_atendimento, usuario_id, data_atendimento,
                         hora_atendimento, descricao, observacao)
                    VALUES
                        (:pessoa_id, :tipo_atendimento, :usuario_id, :data_atendimento,
                         :hora_atendimento, :descricao, :observacao)';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':pessoa_id',        $pessoa_id,        PDO::PARAM_INT);
            $stmt->bindValue(':tipo_atendimento', $tipo_atendimento, PDO::PARAM_INT);
            $stmt->bindValue(':usuario_id',       $usuario_id,       PDO::PARAM_INT);
            $stmt->bindValue(':data_atendimento', $data_atendimento);
            $stmt->bindValue(':hora_atendimento', $hora_atendimento);
            $stmt->bindValue(':descricao',        $descricao ?: null);
            $stmt->bindValue(':observacao',       $observacao ?: null);
            $stmt->execute();

            http_response_code(201);
            echo json_encode([
                'mensagem' => 'Atendimento criado com sucesso.',
                'id'       => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao criar atendimento.']);
        }
    }

    public function atualizarStatus(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id     = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status = trim($_POST['status'] ?? '');

        if (!$id || !in_array($status, ['ativo', 'inativo'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido ou status deve ser ativo ou inativo.']);
            return;
        }

        try {
            $sql = 'UPDATE atendimentos SET status = :status WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id',     $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(
                ['mensagem' => 'Status do atendimento atualizado com sucesso.'],
                JSON_UNESCAPED_UNICODE
            );

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar status.']);
        }
    }
}