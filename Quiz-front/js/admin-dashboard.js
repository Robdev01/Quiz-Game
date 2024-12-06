document.addEventListener("DOMContentLoaded", () => {
    const apiUrl = "http://localhost:8000/quizzes";
    const quizForm = document.getElementById("quiz-form");
    const quizList = document.getElementById("quiz-list");

    // Função para carregar quizzes
    async function loadQuizzes() {
        try {
            const response = await fetch(apiUrl);
            if (!response.ok) throw new Error("Erro ao carregar quizzes");

            const quizzes = await response.json();
            quizList.innerHTML = quizzes
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

    // Função para criar um novo quiz
    quizForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const title = document.getElementById("quiz-title").value;
        const description = document.getElementById("quiz-description").value;

        try {
            const response = await fetch(apiUrl, {
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
            const response = await fetch(`${apiUrl}/${id}`, { method: "DELETE" });
            if (!response.ok) throw new Error("Erro ao deletar quiz");

            alert("Quiz deletado com sucesso!");
            loadQuizzes();
        } catch (error) {
            console.error(error);
            alert("Erro ao deletar quiz.");
        }
    }

    // Carrega os quizzes ao iniciar
    loadQuizzes();
});
