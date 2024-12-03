<?php

require_once '../app/routes.php';

// Carrega as configurações de banco de dados
$config = require '../app/config.php';

// Inicia conexão com o banco usando os dados do config.php
try {
    $pdo = new PDO(
        'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['dbname'],
        $config['db']['user'],
        $config['db']['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
}

// Roteia a requisição
route($pdo);
