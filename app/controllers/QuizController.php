<?php

namespace App\Controllers;

use PDOException;

class QuizController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Criar um novo quiz e associar perguntas existentes
    public function create() {
        $data = json_decode(file_get_contents('php://input'), true);

        $title = $data['title'] ?? null;

        if (!$title || empty($questionIds)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Título e IDs de perguntas são obrigatórios']);
            return;
        }

        try {
            $this->pdo->beginTransaction();

            // Inserir o quiz
            $stmt = $this->pdo->prepare('INSERT INTO quizzes (title, created_at) VALUES (:title, NOW())');
            $stmt->execute(['title' => $title]);
            $quizId = $this->pdo->lastInsertId();

            // Associar perguntas ao quiz
            $stmt = $this->pdo->prepare('INSERT INTO quiz_questions (quiz_id, id) VALUES (:quiz_id, :id)');
            foreach ($questionIds as $questionId) {
                $stmt->execute([
                    'quiz_id' => $quizId,
                    'id' => $questionId
                ]);
            }

            $this->pdo->commit();

            http_response_code(201);
            echo json_encode(['status' => 'success', 'message' => 'Quiz criado com sucesso']);
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erro ao criar o quiz', 'error' => $e->getMessage()]);
        }
    }

    // Obter todos os quizzes com perguntas associadas
    public function getAll() {
        try {
            // Consultar quizzes e suas perguntas associadas
            $stmt = $this->pdo->prepare('
                SELECT 
                    q.id AS quiz_id,
                    q.title AS quiz_title,
                    qt.id AS question_id,
                    qt.question_text,
                    qt.description
                FROM quizzes q
                LEFT JOIN quiz_questions qq ON q.id = qq.quiz_id
                LEFT JOIN questions qt ON qq.question_id = qt.id
            ');
            $stmt->execute();

            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Agrupar quizzes com suas perguntas
            $quizzes = [];
            foreach ($results as $row) {
                $quizId = $row['quiz_id'];

                if (!isset($quizzes[$quizId])) {
                    $quizzes[$quizId] = [
                        'id' => $quizId,
                        'title' => $row['quiz_title'],
                        'questions' => []
                    ];
                }

                if ($row['question_id']) {
                    $quizzes[$quizId]['questions'][] = [
                        'id' => $row['question_id'],
                        'text' => $row['question_text'],
                        'description' => $row['description']
                    ];
                }
            }

            http_response_code(200);
            echo json_encode(['status' => 'success', 'data' => array_values($quizzes)]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erro ao buscar quizzes', 'error' => $e->getMessage()]);
        }
    }
    // Deletar um quiz e suas associações
    public function delete($quizId) {
        try {
            $this->pdo->beginTransaction();
        
            // Excluir associações entre o quiz e as perguntas
            $stmt = $this->pdo->prepare('DELETE FROM quiz_questions WHERE quiz_id = :quiz_id');
            $stmt->execute(['quiz_id' => $quizId]);
        
            // Excluir o quiz
            $stmt = $this->pdo->prepare('DELETE FROM quizzes WHERE id = :id');
            $stmt->execute(['id' => $quizId]);
        
            $this->pdo->commit();
        
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Quiz deletado com sucesso']);
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erro ao deletar o quiz', 'error' => $e->getMessage()]);
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
        
            // Remover perguntas antigas do quiz
            $stmt = $this->pdo->prepare('DELETE FROM quiz_questions WHERE quiz_id = :quiz_id');
            $stmt->execute(['quiz_id' => $quizId]);
        
            // Inserir novas perguntas
            $stmt = $this->pdo->prepare('INSERT INTO quiz_questions (quiz_id, question_id) VALUES (:quiz_id, :question_id)');
            foreach ($questions as $questionId) {
                $stmt->execute([
                    'quiz_id' => $quizId,
                    'question_id' => $questionId // Aqui assumimos que 'question_id' é passado no corpo da requisição
                ]);
            }
        
            $this->pdo->commit();
        
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Quiz updated successfully']);
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to update quiz', 'error' => $e->getMessage()]);
        }
    }


}
