<?php

namespace App\Controllers;

class QuizController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Criar Quiz
    public function create() {
        $data = json_decode(file_get_contents('php://input'), true);

        $title = $data['title'] ?? null;

        if (!$title) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Title is required']);
            return;
        }

        try {
            $stmt = $this->pdo->prepare('INSERT INTO quizzes (title) VALUES (:title)');
            $stmt->execute(['title' => $title]);

            echo json_encode(['status' => 'success', 'message' => 'Quiz created successfully', 'id' => $this->pdo->lastInsertId()]);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Internal Server Error']);
        }
    }

    // Atualizar Quiz
    public function update($id) {
        $data = json_decode(file_get_contents('php://input'), true);

        $title = $data['title'] ?? null;

        if (!$title) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Title is required']);
            return;
        }

        $stmt = $this->pdo->prepare('UPDATE quizzes SET title = :title WHERE id = :id');
        if ($stmt->execute(['title' => $title, 'id' => $id])) {
            echo json_encode(['status' => 'success', 'message' => 'Quiz updated successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Quiz not found']);
        }
    }

    // Deletar Quiz
    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM quizzes WHERE id = :id');
        if ($stmt->execute(['id' => $id])) {
            echo json_encode(['status' => 'success', 'message' => 'Quiz deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Quiz not found']);
        }
    }

    // Obter um Quiz
    public function get($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM quizzes WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $quiz = $stmt->fetch();

        if ($quiz) {
            echo json_encode(['status' => 'success', 'quiz' => $quiz]);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Quiz not found']);
        }
    }

    // Obter todos os Quizzes
    public function getAll() {
        $stmt = $this->pdo->prepare('SELECT * FROM quizzes');
        $stmt->execute();

        $quizzes = $stmt->fetchAll();

        echo json_encode(['status' => 'success', 'quizzes' => $quizzes]);
    }
}
