<?php

class QuizController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Cria um novo quiz
    public function create($title, $createdBy)
    {
        if (empty($title)) {
            return json_encode(['status' => 'error', 'message' => 'O título é obrigatório.']);
        }

        if (empty($createdBy)) {
            return json_encode(['status' => 'error', 'message' => 'O criador do quiz é obrigatório.']);
        }

        $sql = "INSERT INTO quizzes (title, created_by) VALUES (:title, :created_by)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':created_by', $createdBy);

        try {
            $stmt->execute();
            return json_encode(['status' => 'success', 'message' => 'Quiz criado com sucesso!']);
        } catch (PDOException $e) {
            return json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // Lista todos os quizzes com informações do criador
    public function listAll()
    {
        $sql = "
            SELECT 
                q.id, 
                q.title, 
                q.created_by, 
                u.name AS creator_name
            FROM 
                quizzes q
            JOIN 
                users u ON q.created_by = u.id
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    // Obter quiz por ID
    public function get($id)
    {
        if (empty($id)) {
            return json_encode(['status' => 'error', 'message' => 'O ID do quiz é obrigatório.']);
        }

        $sql = "
            SELECT 
                q.id, 
                q.title, 
                q.created_by, 
                u.name AS creator_name
            FROM 
                quizzes q
            JOIN 
                users u ON q.created_by = u.id
            WHERE 
                q.id = :id
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);

        try {
            $stmt->execute();
            $quiz = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($quiz) {
                return json_encode($quiz);
            } else {
                return json_encode(['status' => 'error', 'message' => 'Quiz não encontrado.']);
            }
        } catch (PDOException $e) {
            return json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
?>
