<?php

//Atenção à porta do MySQL
//Se o MySQL estiver configurado em outra porta, por exemplo 3307, será necessário
//informar a porta na string de conexão.

$host = 'localhost';
$port = 3306; // Porta padrão do MySQL
$dbname = 'atendelab';
$user = 'root';
$password = '';
try {
    $pdo = new PDO(
    "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
    $user,
    $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['erro' => 'Erro ao conectar: ' . $e->getMessage()]));
}