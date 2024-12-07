<?php
// Permitir qualquer origem
header('Access-Control-Allow-Origin: *');

// Permitir métodos
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');

// Permitir cabeçalhos
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Verificar se o método é OPTIONS e retornar imediatamente
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Inclui o arquivo de rotas e os controladores
require_once __DIR__ . '/../app/routes.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/controllers/QuizController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';
require_once __DIR__ . '/../app/controllers/QuestionController.php';

// Carrega as configurações de banco de dados
$config = require __DIR__ . '/../app/config.php';

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

// Roteia a requisição passando a conexão com o banco para o arquivo de rotas
route($pdo);
