<?php

namespace App\Controllers;

class QuestionController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Criar Pergunta
    public function create() {
        $data = json_decode(file_get_contents('php://input'), true);

        $quiz_id = $data['quiz_id'] ?? null;
        $question_text = $data['question_text'] ?? null;
        $answer = $data['answer'] ?? null;

        if (!$quiz_id || !$question_text || !$answer) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            return;
        }

        try {
            $stmt = $this->pdo->prepare('INSERT INTO questions (quiz_id, question_text, answer) VALUES (:quiz_id, :question_text, :answer)');
            $stmt->execute([
                'quiz_id' => $quiz_id,
                'question_text' => $question_text,
                'answer' => $answer,
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Question created successfully', 'id' => $this->pdo->lastInsertId()]);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Internal Server Error']);
        }
    }

    // Atualizar Pergunta
    public function update($id) {
        $data = json_decode(file_get_contents('php://input'), true);

        $question_text = $data['question_text'] ?? null;
        $answer = $data['answer'] ?? null;

        if (!$question_text || !$answer) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            return;
        }

        $stmt = $this->pdo->prepare('UPDATE questions SET question_text = :question_text, answer = :answer WHERE id = :id');
        if ($stmt->execute(['question_text' => $question_text, 'answer' => $answer, 'id' => $id])) {
            echo json_encode(['status' => 'success', 'message' => 'Question updated successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Question not found']);
        }
    }

    // Deletar Pergunta
    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM questions WHERE id = :id');
        if ($stmt->execute(['id' => $id])) {
            echo json_encode(['status' => 'success', 'message' => 'Question deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Question not found']);
        }
    }

    // Obter uma Pergunta
    public function get($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM questions WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $question = $stmt->fetch();

        if ($question) {
            echo json_encode(['status' => 'success', 'question' => $question]);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Question not found']);
        }
    }

    // Obter todas as Perguntas
    public function getAll() {
        $stmt = $this->pdo->prepare('SELECT * FROM questions');
        $stmt->execute();

        $questions = $stmt->fetchAll();

        echo json_encode(['status' => 'success', 'questions' => $questions]);
    }
}
