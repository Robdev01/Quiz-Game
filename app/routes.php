<?php

require_once 'controllers/UserController.php';
require_once 'controllers/QuizController.php';
// Função de roteamento
function route($pdo)
{
    // Instância do controlador
    $userController = new UserController($pdo);
    $quizController = new QuizController($pdo);
    
    // Pega o URI e o método da requisição
    $uri = $_SERVER['REQUEST_URI'];
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    
    // Rota para criar um usuário (POST)
    if ($uri === '/users' && $requestMethod === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        echo $userController->create(
            $data['name'], 
            $data['email'], 
            $data['password'], 
            isset($data['is_admin']) ? $data['is_admin'] : false
        );
    }
    
    // Rota para obter um usuário (GET)
    elseif (preg_match('/^\/users\/(\d+)$/', $uri, $matches) && $requestMethod === 'GET') {
        $id = $matches[1];
        echo json_encode($userController->get($id));
    }
    
    // Rota para atualizar um usuário (PUT)
    elseif (preg_match('/^\/users\/(\d+)$/', $uri, $matches) && $requestMethod === 'PUT') {
        $id = $matches[1];
        $data = json_decode(file_get_contents("php://input"), true);
        echo $userController->update(
            $id, 
            $data['name'], 
            $data['email'], 
            isset($data['password']) ? $data['password'] : null,
            isset($data['is_admin']) ? $data['is_admin'] : false
        );
    }
    
    // Rota para deletar um usuário (DELETE)
    elseif (preg_match('/^\/users\/(\d+)$/', $uri, $matches) && $requestMethod === 'DELETE') {
        $id = $matches[1];
        echo $userController->delete($id);
    }

    // Criar quiz (POST)
    if ($uri === '/quizzes' && $requestMethod === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        echo $quizController->create(
            $data['title'], 
            $data['created_by']
        );
    }

    // Listar quizzes (GET)
    elseif ($uri === '/quizzes' && $requestMethod === 'GET') {
        echo $quizController->listAll();
    }

    // Obter quiz por ID (GET)
    elseif (preg_match('/^\/quizzes\/(\d+)$/', $uri, $matches) && $requestMethod === 'GET') {
        $id = $matches[1];
        echo $quizController->get($id);
    }

    else {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Rota não encontrada.']);
    }


}

?>
