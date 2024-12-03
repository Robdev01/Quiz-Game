<?php

class UserController
{
    private $pdo;

    // Construtor que recebe a conexão PDO
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Cria um novo usuário
    public function create($name, $email, $password, $is_admin = false)
    {
        if (empty($name) || empty($email) || empty($password)) {
            return "Nome, email e senha são obrigatórios.";
        }
        
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (name, email, password, is_admin) VALUES (:name, :email, :password, :is_admin)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':is_admin', $is_admin, PDO::PARAM_BOOL);

        if ($stmt->execute()) {
            return "Usuário criado com sucesso!";
        } else {
            return "Erro ao criar o usuário.";
        }
    }

    // Obtém um usuário pelo ID
    public function get($id)
    {
        if (empty($id)) {
            return "ID é necessário.";
        }

        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            return $user;
        } else {
            return "Usuário não encontrado.";
        }
    }

    // Atualiza informações do usuário
    public function update($id, $name, $email, $password = null, $is_admin = false)
    {
        if (empty($id) || empty($name) || empty($email)) {
            return "ID, nome e email são obrigatórios.";
        }

        $sql = "UPDATE users SET name = :name, email = :email, is_admin = :is_admin";
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $sql .= ", password = :password";
        }
        $sql .= " WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':is_admin', $is_admin, PDO::PARAM_BOOL);
        $stmt->bindParam(':id', $id);

        if (!empty($password)) {
            $stmt->bindParam(':password', $hashedPassword);
        }

        if ($stmt->execute()) {
            return "Usuário atualizado com sucesso!";
        } else {
            return "Erro ao atualizar o usuário.";
        }
    }

    // Deleta um usuário pelo ID
    public function delete($id)
    {
        if (empty($id)) {
            return "ID é necessário.";
        }

        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return "Usuário deletado com sucesso!";
        } else {
            return "Erro ao deletar o usuário.";
        }
    }

    // Lista todos os usuários
    public function listAll()
    {
        $sql = "SELECT * FROM users";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    }
}
?>
