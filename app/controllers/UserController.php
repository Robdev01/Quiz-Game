<?php

namespace App\Controllers;

class UserController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function register() {
        $data = json_decode(file_get_contents('php://input'), true);

        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';

        if (!$name || !$email) {
            echo json_encode(['status' => 'error', 'message' => 'Name and email are required']);
            http_response_code(400);
            return;
        }

        $stmt = $this->pdo->prepare('INSERT INTO users (name, email) VALUES (:name, :email)');
        $stmt->execute(['name' => $name, 'email' => $email]);

        echo json_encode(['status' => 'success', 'message' => 'User registered successfully']);
    }

    public function getRanking() {
        $stmt = $this->pdo->query('SELECT name, score FROM users ORDER BY score DESC');
        $ranking = $stmt->fetchAll();

        echo json_encode(['status' => 'success', 'ranking' => $ranking]);
    }
}
