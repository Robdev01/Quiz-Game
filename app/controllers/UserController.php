<?php

namespace App\Controllers;

class UserController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Registro de usuários
    public function register() {
        $data = json_decode(file_get_contents('php://input'), true);

        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $role = 'user'; // Define o papel como 'user'

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

            echo json_encode(['status' => 'success', 'message' => 'User registered successfully']);
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] == 1062) { // Duplicate entry
                http_response_code(409);
                echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Internal Server Error']);
            }
        }
    }

    // Login de usuários
    public function login() {
        $data = json_decode(file_get_contents('php://input'), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Email and password are required']);
            return;
        }

        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email AND role = :role');
        $stmt->execute([
            'email' => $email,
            'role' => 'user', // Garante que apenas usuários normais façam login aqui
        ]);

        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
            return;
        }
        // Sucesso no login
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => 'Login realizado com sucesso',
            'role' => $user['role'] // Inclui o papel no retorno
        ]);

    }
    // Método GET - Para obter dados do usuário
    public function get($id) {
        // Verifique se o ID é válido
        if (!$id) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
            return;
        }

        $stmt = $this->pdo->prepare('SELECT id, name, email, role, created_at FROM users WHERE id = :id AND role = :role');
        $stmt->execute([
            'id' => $id,
            'role' => 'user', // Apenas usuários
        ]);

        $user = $stmt->fetch();

        if (!$user) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            return;
        }

        echo json_encode(['status' => 'success', 'user' => $user]);
    }
    // Método PUT - Para atualizar dados do usuário
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
            'role' => 'user',
        ]);

        echo json_encode(['status' => 'success', 'message' => 'User updated successfully']);
    }
    // Método GET - Para obter todos os usuários
    public function getAll() {
        $stmt = $this->pdo->prepare('SELECT id, name, email, role, created_at FROM users WHERE role = :role');
        $stmt->execute([
            'role' => 'user' // Apenas usuários
        ]);

        $users = $stmt->fetchAll();

        if (empty($users)) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'No users found']);
            return;
        }

        echo json_encode(['status' => 'success', 'users' => $users]);
    }
}
