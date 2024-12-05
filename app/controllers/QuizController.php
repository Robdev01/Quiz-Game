<?php

namespace App\Controllers;

class QuizController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Criar um novo quiz
    public function create() {
        $data = json_decode(file_get_contents('php://input'), true);

        $title = $data['title'] ?? null;
        $questions = $data['questions'] ?? [];

        if (!$title || empty($questions)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Title and questions are required']);
            return;
        }

        try {
            $this->pdo->beginTransaction();

            // Inserir o quiz
            $stmt = $this->pdo->prepare('INSERT INTO quizzes (title) VALUES (:title)');
            $stmt->execute(['title' => $title]);
            $quizId = $this->pdo->lastInsertId();

            // Inserir perguntas
            $stmt = $this->pdo->prepare('INSERT INTO questions (quiz_id, question_text) VALUES (:quiz_id, :question_text)');
            foreach ($questions as $question) {
                $stmt->execute([
                    'quiz_id' => $quizId,
                    'question_text' => $question['question_text']
                ]);
            }

            $this->pdo->commit();

            http_response_code(201);
            echo json_encode(['status' => 'success', 'message' => 'Quiz created successfully']);
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to create quiz', 'error' => $e->getMessage()]);
        }
    }

    // Atualizar um quiz existente
    public function update($quizId) {
        $data = json_decode(file_get_contents('php://input'), true);

        $title = $data['title'] ?? null;
        $questions = $data['questions'] ?? [];

        if (!$title || empty($questions)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Title and questions are required']);
            return;
        }

        try {
            $this->pdo->beginTransaction();

            // Atualizar o título do quiz
            $stmt = $this->pdo->prepare('UPDATE quizzes SET title = :title WHERE id = :id');
            $stmt->execute(['title' => $title, 'id' => $quizId]);

            // Remover perguntas antigas
            $stmt = $this->pdo->prepare('DELETE FROM questions WHERE quiz_id = :quiz_id');
            $stmt->execute(['quiz_id' => $quizId]);

            // Inserir novas perguntas
            $stmt = $this->pdo->prepare('INSERT INTO questions (quiz_id, question_text) VALUES (:quiz_id, :question_text)');
            foreach ($questions as $question) {
                $stmt->execute([
                    'quiz_id' => $quizId,
                    'question_text' => $question['question_text']
                ]);
            }

            $this->pdo->commit();

            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Quiz updated successfully']);
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to update quiz', 'error' => $e->getMessage()]);
        }
    }

    // Deletar um quiz
    public function delete($quizId) {
        try {
            // Deletar o quiz e suas perguntas
            $stmt = $this->pdo->prepare('DELETE FROM quizzes WHERE id = :id');
            $stmt->execute(['id' => $quizId]);

            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Quiz deleted successfully']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete quiz', 'error' => $e->getMessage()]);
        }
    }
    
}
