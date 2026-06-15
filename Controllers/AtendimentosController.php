<?php

class AtendimentosController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../config/database.php';
        $this->pdo = $pdo;
    }

    public function listar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $sql = "
            SELECT
                a.id,
                p.nome AS pessoa,
                u.nome AS usuario,
                t.nome AS tipo_atendimento,
                a.data_atendimento,
                a.hora_atendimento,
                a.status,
                a.criado_em
            FROM atendimentos a
            INNER JOIN pessoas p
                ON p.id = a.pessoa_id
            INNER JOIN usuarios u
                ON u.id = a.usuario_id
            INNER JOIN tipos_atendimentos t
                ON t.id = a.tipo_atendimento
            ORDER BY a.id DESC
        ";

        $stmt = $this->pdo->query($sql);

        echo json_encode(
            $stmt->fetchAll(PDO::FETCH_ASSOC),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
    }

    public function visualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido']);
            return;
        }

        $sql = "
            SELECT
                a.*,
                p.nome AS pessoa,
                u.nome AS usuario,
                t.nome AS tipo_atendimento
            FROM atendimentos a
            INNER JOIN pessoas p
                ON p.id = a.pessoa_id
            INNER JOIN usuarios u
                ON u.id = a.usuario_id
            INNER JOIN tipos_atendimentos t
                ON t.id = a.tipo_atendimento
            WHERE a.id = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$atendimento) {
            http_response_code(404);
            echo json_encode(['erro' => 'Atendimento não encontrado']);
            return;
        }

        echo json_encode(
            $atendimento,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
    }

    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $usuarioId = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);
        $tipoAtendimento = filter_input(INPUT_POST, 'tipo_atendimento', FILTER_VALIDATE_INT);
        $pessoaId = filter_input(INPUT_POST, 'pessoa_id', FILTER_VALIDATE_INT);

        $data = $_POST['data_atendimento'] ?? '';
        $hora = $_POST['hora_atendimento'] ?? '';
        $descricao = trim($_POST['descricao'] ?? '');
        $observacao = trim($_POST['observacao'] ?? '');

        if (
            !$usuarioId ||
            !$tipoAtendimento ||
            !$pessoaId ||
            $data === '' ||
            $hora === '' ||
            $descricao === ''
        ) {
            http_response_code(400);

            echo json_encode([
                'erro' => 'Todos os campos obrigatórios devem ser informados.'
            ]);

            return;
        }

        $sql = "
            INSERT INTO atendimentos
            (
                usuario_id,
                tipo_atendimento,
                pessoa_id,
                data_atendimento,
                hora_atendimento,
                descricao,
                observacao
            )
            VALUES
            (
                :usuario_id,
                :tipo_atendimento,
                :pessoa_id,
                :data_atendimento,
                :hora_atendimento,
                :descricao,
                :observacao
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':tipo_atendimento' => $tipoAtendimento,
            ':pessoa_id' => $pessoaId,
            ':data_atendimento' => $data,
            ':hora_atendimento' => $hora,
            ':descricao' => $descricao,
            ':observacao' => $observacao
        ]);

        http_response_code(201);

        echo json_encode([
            'mensagem' => 'Atendimento criado com sucesso.',
            'id' => $this->pdo->lastInsertId()
        ]);
    }

    public function atualizarStatus(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? '';

        $statusValidos = [
            'pendente',
            'aguardando',
            'em_andamento',
            'finalizado',
            'cancelado'
        ];

        if (!$id || !in_array($status, $statusValidos, true)) {
            http_response_code(400);

            echo json_encode([
                'erro' => 'Dados inválidos.'
            ]);

            return;
        }

        $sql = "
            UPDATE atendimentos
            SET status = :status
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);

        echo json_encode([
            'mensagem' => 'Status atualizado com sucesso.'
        ]);
    }
}
