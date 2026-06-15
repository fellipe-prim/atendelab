<?php

class TiposAtendimentosController
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

        $stmt = $this->pdo->query(
            "SELECT * FROM tipos_atendimentos ORDER BY id DESC"
        );

        echo json_encode(
            $stmt->fetchAll(PDO::FETCH_ASSOC),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
    }

    public function buscarPorId(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido']);
            return;
        }

        $stmt = $this->pdo->prepare(
            "SELECT * FROM tipos_atendimentos WHERE id = :id"
        );

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(
            $stmt->fetch(PDO::FETCH_ASSOC),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
    }

    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $status = $_POST['status'] ?? 'presencial';

        if ($nome === '' || $descricao === '') {
            http_response_code(400);

            echo json_encode([
                'erro' => 'Nome e descrição são obrigatórios.'
            ]);

            return;
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO tipos_atendimentos
            (nome, descricao, status)
            VALUES
            (:nome, :descricao, :status)"
        );

        $stmt->execute([
            ':nome' => $nome,
            ':descricao' => $descricao,
            ':status' => $status
        ]);

        echo json_encode([
            'mensagem' => 'Tipo de atendimento criado.',
            'id' => $this->pdo->lastInsertId()
        ]);
    }

    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $status = $_POST['status'] ?? 'presencial';

        $stmt = $this->pdo->prepare(
            "UPDATE tipos_atendimentos
            SET nome = :nome,
                descricao = :descricao,
                status = :status
            WHERE id = :id"
        );

        $stmt->execute([
            ':id' => $id,
            ':nome' => $nome,
            ':descricao' => $descricao,
            ':status' => $status
        ]);

        echo json_encode([
            'mensagem' => 'Tipo de atendimento atualizado.'
        ]);
    }

    public function excluir(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        $stmt = $this->pdo->prepare(
            "DELETE FROM tipos_atendimentos WHERE id = :id"
        );

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode([
            'mensagem' => 'Tipo de atendimento removido.'
        ]);
    }
}
