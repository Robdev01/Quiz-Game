// Obtém o ID do quiz a partir da URL
function getQuizIdFromUrl() {
    const params = new URLSearchParams(window.location.search);
    return params.get("quiz_id");
}

const quizId = getQuizIdFromUrl(); // Obtém o quizId da URL
const apiUrl = `http://localhost:8000/quizzes/${quizId}/questions`; // Endpoint para obter os dados do quiz

document.addEventListener("DOMContentLoaded", () => {
    if (!quizId) {
        alert("Quiz ID não encontrado na URL.");
        return;
    }
    fetchQuizDetails(); // Carrega os detalhes do quiz
});

document.getElementById("reload-quiz").addEventListener("click", fetchQuizDetails);

function fetchQuizDetails() {
    fetch(apiUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erro ao buscar quiz: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.status === "success") {
                displayQuizDetails(data);
            } else {
                console.error("Erro ao obter dados do quiz.");
            }
        })
        .catch(error => console.error(error));
}

function displayQuizDetails(data) {
    const titleElement = document.getElementById("quiz-title");
    const descriptionElement = document.getElementById("quiz-description");
    const questionsElement = document.getElementById("quiz-questions");

    // Define título e descrição
    titleElement.textContent = `Quiz ID: ${quizId}`;
    descriptionElement.textContent = "Descrição do quiz não fornecida.";

    // Renderiza perguntas
    questionsElement.innerHTML = "";
    if (data.questions && data.questions.length > 0) {
        data.questions.forEach(question => {
            const li = document.createElement("li");
            li.innerHTML = `
                <strong>Pergunta:</strong> ${question.question_text}<br>
                <strong>Resposta:</strong> ${question.answer === "verdadeiro" ? "Verdadeiro" : "Falso"}<br>
                <strong>Criada em:</strong> ${question.created_at}<br>
                <button class="edit-btn" data-id="${question.id}">Editar</button>
                <button class="delete-btn" data-id="${question.id}">Excluir</button>
            `;
            questionsElement.appendChild(li);
        });

        // Adiciona eventos aos botões
        document.querySelectorAll(".edit-btn").forEach(button =>
            button.addEventListener("click", handleEditQuestion)
        );

        document.querySelectorAll(".delete-btn").forEach(button =>
            button.addEventListener("click", handleDeleteQuestion)
        );
    } else {
        questionsElement.innerHTML = "<li>Sem perguntas disponíveis.</li>";
    }
}

// Função para editar uma pergunta
function handleEditQuestion(event) {
    const questionId = event.target.dataset.id;
    const newQuestionText = prompt("Digite o novo texto da pergunta:");

    if (newQuestionText) {
        fetch(`http://localhost:8000/questions/${questionId}`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ question_text: newQuestionText }),
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error("Erro ao atualizar a pergunta.");
                }
                return response.json();
            })
            .then(data => {
                if (data.status === "success") {
                    alert("Pergunta atualizada com sucesso!");
                    fetchQuizDetails(); // Recarrega os detalhes do quiz
                } else {
                    alert("Erro ao atualizar a pergunta.");
                }
            })
            .catch(error => console.error(error));
    }
}

// Função para excluir uma pergunta
function handleDeleteQuestion(event) {
    const questionId = event.target.dataset.id;

    if (confirm("Tem certeza que deseja excluir esta pergunta?")) {
        fetch(`http://localhost:8000/questions/${questionId}`, {
            method: "DELETE",
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error("Erro ao excluir a pergunta.");
                }
                return response.json();
            })
            .then(data => {
                if (data.status === "success") {
                    alert("Pergunta excluída com sucesso!");
                    fetchQuizDetails(); // Recarrega os detalhes do quiz
                } else {
                    alert("Erro ao excluir a pergunta.");
                }
            })
            .catch(error => console.error(error));
    }
}
