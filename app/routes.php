<?php

use App\Controllers\AdminController;
use App\Controllers\QuizController;
use App\Controllers\UserController;
use App\Controllers\QuestionController;

function route($pdo) {
    // Define o cabeçalho padrão para resposta JSON
    header('Content-Type: application/json');

    // Normaliza a URI removendo barra extra no final
    $uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $method = $_SERVER['REQUEST_METHOD'];

     // Ignorar a requisição para favicon.ico
     $uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
     if ($uri === '/favicon.ico') {
         http_response_code(204); // Sem conteúdo
         exit();
     }

    // Roteamento básico
    switch (true) {
        // Admin    
        case $uri === '/admins/register' && $method === 'POST':
            $controller = new AdminController($pdo);
            $controller->register();
            break;
        
        case $uri === '/admins/login' && $method === 'POST':
            $controller = new AdminController($pdo);
            $controller->login();
            break;  
            
        case preg_match('/^\/admins\/get\/(\d+)$/', $uri, $matches) && $method === 'GET':
            $id = $matches[1];  // O ID está na primeira posição do array $matches
            $controller = new AdminController($pdo);
            $controller->get($id);
            break;
        
        case preg_match('/^\/admins\/update\/(\d+)$/', $uri, $matches) && $method === 'PUT':
            $id = $matches[1];  // O ID está na primeira posição do array $matches
            $controller = new AdminController($pdo);
            $controller->update($id);
            break;;
        case $uri === '/admins/todos' && $method === 'GET':
            $controller = new AdminController($pdo);
            $controller->getAll();
            break;
        
       // **Quizzes**
    case $uri === '/quizzes/criar' && $method === 'POST':
        $controller = new QuizController($pdo);
        $controller->create();
        break;

    case preg_match('/^\/quizzes\/(\d+)$/', $uri, $matches) && $method === 'PUT':
        $quizId = $matches[1];
        $controller = new QuizController($pdo);
        $controller->update($quizId);
        break;

    case preg_match('/^\/quizzes\/(\d+)$/', $uri, $matches) && $method === 'DELETE':
        $quizId = $matches[1];
        $controller = new QuizController($pdo);
        $controller->delete($quizId);
        break;

    case $uri === '/quizzes/todos' && $method === 'GET':
        $controller = new QuizController($pdo);
        $controller->getAll();
        break;

    case preg_match('/^\/quizzes\/(\d+)$/', $uri, $matches) && $method === 'GET':
        $quizId = $matches[1];
        $controller = new QuizController($pdo);
        $controller->get($quizId);
        break;

    // **Questions**
    case $uri === '/questions/criar' && $method === 'POST':
        $controller = new QuestionController($pdo);
        $controller->create();
        break;

    case preg_match('/^\/questions\/(\d+)$/', $uri, $matches) && $method === 'PUT':
        $questionId = $matches[1];
        $controller = new QuestionController($pdo);
        $controller->update($questionId);
        break;

    case preg_match('/^\/questions\/(\d+)$/', $uri, $matches) && $method === 'DELETE':
        $questionId = $matches[1];
        $controller = new QuestionController($pdo);
        $controller->delete($questionId);
        break;

    case $uri === '/questions/todas' && $method === 'GET':
        $controller = new QuestionController($pdo);
        $controller->getAll();
        break;

    case preg_match('/^\/questions\/(\d+)$/', $uri, $matches) && $method === 'GET':
        $questionId = $matches[1];
        $controller = new QuestionController($pdo);
        $controller->get($questionId);
        break;

        // Usuários
        case $uri === '/users/register' && $method === 'POST':
            $controller = new UserController($pdo);
            $controller->register();
            break;
        
        case $uri === '/users/login' && $method === 'POST':
            $controller = new UserController($pdo);
            $controller->login();
            break;

        case preg_match('/^\/users\/get\/(\d+)$/', $uri, $matches) && $method === 'GET':
            $id = $matches[1];  // O ID está na primeira posição do array $matches
            $controller = new UserController($pdo);
            $controller->get($id);
            break;
        
        case preg_match('/^\/users\/update\/(\d+)$/', $uri, $matches) && $method === 'PUT':
            $id = $matches[1];  // O ID está na primeira posição do array $matches
            $controller = new UserController($pdo);
            $controller->update($id);
            break;   
        case $uri === '/users/todos' && $method === 'GET':
            $controller = new UserController($pdo);
            $controller->getAll();
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
