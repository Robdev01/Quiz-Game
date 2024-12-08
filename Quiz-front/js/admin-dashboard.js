document.addEventListener("DOMContentLoaded", () => {
    const quizForm = document.getElementById("quizForm");
    const questionForm = document.getElementById("questionForm");
    const quizIdSelect = document.getElementById("quizId");
    const quizList = document.getElementById("quiz-list");

    // Cadastrar Quiz
    quizForm.addEventListener("submit", function (event) {
        event.preventDefault();
        const quizTitle = document.getElementById("quizTitle").value;

        fetch("http://localhost:8000/quizzes/criar", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ title: quizTitle }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.status === "success") {
                    alert("Quiz cadastrado com sucesso!");
                    loadQuizzes(); // Recarrega os quizzes
                } else {
                    alert("Erro ao cadastrar quiz!");
                }
            })
            .catch((error) => {
                alert("Erro de rede: " + error);
            });
    });

    // Cadastrar Pergunta
    questionForm.addEventListener("submit", function (event) {
        event.preventDefault();
        const quizId = quizIdSelect.value;
        const questionText = document.getElementById("questionText").value;
        const answer = document.getElementById("answer").value;

        fetch("http://localhost:8000/questions/criar", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                quiz_id: quizId,
                question_text: questionText,
                answer: answer,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.status === "success") {
                    alert("Pergunta cadastrada com sucesso!");
                    window.location.href = `/Quiz-front/quiz-details.html?quiz_id=${quizId}`; // Redireciona para a página do quiz
                } else {
                    alert("Erro ao cadastrar pergunta!");
                }
            })
            .catch((error) => {
                alert("Erro de rede: " + error);
            });
    });

    // Carregar Quizzes
    async function loadQuizzes() {
        try {
            const response = await fetch("http://localhost:8000/quizzes/todos");
            const data = await response.json();

            if (data.status === "success" && data.quizzes) {
                quizIdSelect.innerHTML = '<option value="">Selecione um Quiz</option>';
                quizList.innerHTML = "";

                data.quizzes.forEach((quiz) => {
                    // Popula o select
                    const option = document.createElement("option");
                    option.value = quiz.id;
                    option.textContent = quiz.title;
                    quizIdSelect.appendChild(option);

                    // Popula a tabela de quizzes
                    quizList.innerHTML += `
                        <tr>
                            <td>${quiz.id}</td>
                            <td>${quiz.title}</td>
                            <td>${quiz.created_at}</td>
                            <td>
                                <a href="/Quiz-front/quiz-details.html?quiz_id=${quiz.id}">Visualizar</a>
                            </td>
                        </tr>
                    `;
                });
            }
        } catch (error) {
            console.error("Erro ao carregar quizzes:", error);
        }
    }

    // Inicializar
    loadQuizzes();
});
