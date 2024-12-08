document.addEventListener("DOMContentLoaded", () => {
    const quizList = document.getElementById("quiz-list");

    async function loadQuizzes() {
        try {
            const response = await fetch("http://localhost:8000/quizzes/todos");
            const data = await response.json();

            if (data.status === "success" && data.quizzes) {
                data.quizzes.forEach((quiz) => {
                    const li = document.createElement("li");
                    li.innerHTML = `
                        <button onclick="startQuiz(${quiz.id})">
                            ${quiz.title}
                        </button>
                    `;
                    quizList.appendChild(li);
                });
            } else {
                quizList.innerHTML = "<p>Nenhum quiz encontrado.</p>";
            }
        } catch (error) {
            console.error("Erro ao carregar quizzes:", error);
        }
    }

    window.startQuiz = (quizId) => {
        window.location.href = `/Quiz-front/quiz.html?quiz_id=${quizId}`;
    };

    loadQuizzes();
});