document.addEventListener("DOMContentLoaded", () => {
    const quizApiUrl = "http://localhost:8000/quizzes";
    const userApiUrl = "http://localhost:8000/users/todos";
    const quizForm = document.getElementById("quiz-form");
    const quizList = document.getElementById("quiz-list");
    const userList = document.getElementById("user-list");

    // Função para carregar quizzes
    async function loadQuizzes() {
        try {
            const response = await fetch(quizApiUrl);
            if (!response.ok) throw new Error("Erro ao carregar quizzes");

            const quizzes = await response.json();
            quizList.innerHTML = quizzes.data
                .map(
                    (quiz) => `
                <tr>
                    <td>${quiz.id}</td>
                    <td>${quiz.title}</td>
                    <td>${quiz.description}</td>
                    <td class="actions">
                        <button onclick="editQuiz(${quiz.id})">Editar</button>
                        <button onclick="deleteQuiz(${quiz.id})">Deletar</button>
                    </td>
                </tr>
            `
                )
                .join("");
        } catch (error) {
            console.error(error);
        }
    }

    // Função para carregar usuários
    async function loadUsers() {
        try {
            const response = await fetch(userApiUrl);
            if (!response.ok) throw new Error("Erro ao carregar usuários");

            const data = await response.json();
            if (data.status === "success" && data.users) {
                userList.innerHTML = data.users
                    .map(
                        (user) => `
                        <tr>
                            <td>${user.id}</td>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>${user.role}</td>
                            <td>${user.created_at}</td>
                        </tr>
                    `
                    )
                    .join("");
            } else {
                userList.innerHTML = "<tr><td colspan='5'>Nenhum usuário encontrado.</td></tr>";
            }
        } catch (error) {
            console.error(error);
            userList.innerHTML = "<tr><td colspan='5'>Erro ao carregar usuários.</td></tr>";
        }
    }

    // Função para criar um novo quiz
    quizForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const title = document.getElementById("quiz-title").value;
        const description = document.getElementById("quiz-description").value;

        try {
            const response = await fetch(quizApiUrl, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ title, description }),
            });

            if (!response.ok) throw new Error("Erro ao cadastrar quiz");

            alert("Quiz cadastrado com sucesso!");
            quizForm.reset();
            loadQuizzes();
        } catch (error) {
            console.error(error);
            alert("Erro ao cadastrar quiz.");
        }
    });

    // Função para deletar um quiz
    async function deleteQuiz(id) {
        try {
            const response = await fetch(`${quizApiUrl}/${id}`, { method: "DELETE" });
            if (!response.ok) throw new Error("Erro ao deletar quiz");

            alert("Quiz deletado com sucesso!");
            loadQuizzes();
        } catch (error) {
            console.error(error);
            alert("Erro ao deletar quiz.");
        }
    }

    // Carrega os quizzes e usuários ao iniciar
    loadQuizzes();
    loadUsers();
});
