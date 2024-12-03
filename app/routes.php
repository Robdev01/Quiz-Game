<?php

require_once 'controllers/UserController.php';
// Função de roteamento
function route($pdo)
{
    // Instância do controlador
    $userController = new UserController($pdo);
    
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

    // Rota para listar todos os usuários (GET)
    elseif ($uri === '/users' && $requestMethod === 'GET') {
        echo json_encode($userController->listAll());
    }
    
    else {
        http_response_code(404);
        echo "Rota não encontrada.";
    }
}

?>
