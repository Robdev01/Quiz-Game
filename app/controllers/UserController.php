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
