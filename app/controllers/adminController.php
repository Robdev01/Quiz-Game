<?php

namespace App\Controllers;
use PDOException;


class AdminController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Registro de administradores
    public function register() {
        $data = json_decode(file_get_contents('php://input'), true);

        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $role = 'admin'; // Define o papel como 'admin'

        if (!$name || !$email || !$password) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            return;
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        try {
            $stmt = $this->pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :password_hash, :role)');
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password_hash' => $passwordHash,
                'role' => $role,
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Admin registered successfully']);
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) { // Duplicate entry
                http_response_code(409);
                echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Internal Server Error']);
            }
        }
    }

    // Login de administradores
   // Login de administradores
public function login() {
    $data = json_decode(file_get_contents('php://input'), true);

    $email = $data['email'] ?? null;
    $password = $data['password'] ?? null;

    if (!$email || !$password) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Email e senha são obrigatórios'
        ]);
        return;
    }

    try {
        // Busca o administrador no banco de dados
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email AND role = :role');
        $stmt->execute([
            'email' => $email,
            'role' => 'admin', // Filtra apenas administradores
        ]);

        $admin = $stmt->fetch();

        // Verifica se o usuário foi encontrado e se a senha está correta
        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Credenciais inválidas'
            ]);
            return;
        }

        // Sucesso no login
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => 'Login realizado com sucesso',
            'role' => $admin['role'] // Inclui o papel no retorno
        ]);
    } catch (PDOException $e) {
        // Erro ao conectar ao banco de dados
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro no servidor: ' . $e->getMessage()
        ]);
    }
}

    // Método GET - Para obter dados do administrador
    public function get($id) {
        // Verifique se o ID é válido
        if (!$id) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Admin ID is required']);
            return;
        }

        $stmt = $this->pdo->prepare('SELECT id, name, email, role, created_at FROM users WHERE id = :id AND role = :role');
        $stmt->execute([
            'id' => $id,
            'role' => 'admin', // Apenas administradores
        ]);

        $admin = $stmt->fetch();

        if (!$admin) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Admin not found']);
            return;
        }

        echo json_encode(['status' => 'success', 'admin' => $admin]);
    }
    // Método PUT - Para atualizar dados do administrador
    public function update($id) {
        $data = json_decode(file_get_contents('php://input'), true);

        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$name || !$email) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Name and email are required']);
            return;
        }

        // Atualiza senha, se fornecida
        $passwordHash = $password ? password_hash($password, PASSWORD_BCRYPT) : null;

        $stmt = $this->pdo->prepare('UPDATE users SET name = :name, email = :email, password_hash = :password_hash WHERE id = :id AND role = :role');
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password_hash' => $passwordHash,
            'id' => $id,
            'role' => 'admin',
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Admin updated successfully']);
    }
    // Método GET - Para obter todos os administradores
    public function getAll() {
        $stmt = $this->pdo->prepare('SELECT id, name, email, role, created_at FROM users WHERE role = :role');
        $stmt->execute([
            'role' => 'admin' // Apenas administradores
        ]);

        $admins = $stmt->fetchAll();

        if (empty($admins)) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'No admins found']);
            return;
        }

        echo json_encode(['status' => 'success', 'admins' => $admins]);
    }

}
