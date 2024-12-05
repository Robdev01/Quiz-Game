<?php

use App\Controllers\AdminController;
use App\Controllers\QuizController;
use App\Controllers\UserController;

function route($pdo) {
    // Define o cabeçalho padrão para resposta JSON
    header('Content-Type: application/json');

    // Normaliza a URI removendo barra extra no final
    $uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $method = $_SERVER['REQUEST_METHOD'];

    // Roteamento básico
    switch (true) {
        // Admin
        case $uri === '/admin/login' && $method === 'POST':
            $controller = new AdminController($pdo); // Passa o PDO
            $controller->login();
            break;

        // Quiz
        case $uri === '/quizzes' && $method === 'POST':
            $controller = new QuizController($pdo); // Passa o PDO para o controlador
            $controller->create();
            break;
        case preg_match('/^\/quizzes\/(\d+)$/', $uri, $matches) && $method === 'PUT':
            $quizId = $matches[1];
            $controller = new QuizController($pdo); // Passa o PDO para o controlador
            $controller->update($quizId);
            break;
        case preg_match('/^\/quizzes\/(\d+)$/', $uri, $matches) && $method === 'DELETE':
            $quizId = $matches[1];
            $controller = new QuizController($pdo);
            $controller->delete($quizId);
            break;

        // Usuários
        case $uri === '/users/register' && $method === 'POST':
            $controller = new UserController($pdo);
            $controller->register();
            break;
        case $uri === '/users/ranking' && $method === 'GET':
            $controller = new UserController($pdo);
            $controller->getRanking();
            break;

        // Participação
        case preg_match('/^\/quizzes\/(\d+)\/answer$/', $uri, $matches) && $method === 'POST':
            $quizId = $matches[1];
            $controller = new QuizController($pdo);
            break;

        // Rota não encontrada
        default:
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Route not found']);
            break;
    }
}
