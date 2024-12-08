const quizId = 9; // Substituir pelo ID do quiz desejado
const apiUrl = `http://localhost:8000/quizzes/${quizId}/questions`; // Endpoint para obter os dados do quiz

document.addEventListener("DOMContentLoaded", fetchQuizDetails);

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

    // Como a descrição não está presente na resposta da API, deixaremos um texto padrão
    titleElement.textContent = `Quiz ID: ${quizId}`;
    descriptionElement.textContent = "Descrição do quiz não fornecida.";

    // Exibe as perguntas do quiz
    questionsElement.innerHTML = "";
    if (data.questions && data.questions.length > 0) {
        data.questions.forEach(question => {
            const li = document.createElement("li");
            li.innerHTML = `
                <strong>Pergunta:</strong> ${question.question_text}<br>
                <strong>Resposta:</strong> ${question.answer === "verdadeiro" ? "Verdadeiro" : "Falso"}<br>
                <strong>Criada em:</strong> ${question.created_at}
            `;
            questionsElement.appendChild(li);
        });
    } else {
        questionsElement.innerHTML = "<li>Sem perguntas disponíveis.</li>";
    }
}
