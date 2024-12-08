document.addEventListener("DOMContentLoaded", () => {
    const questionContainer = document.getElementById("question-container");
    const trueButton = document.getElementById("true-button");
    const falseButton = document.getElementById("false-button");
    const scoreDisplay = document.getElementById("score-display");

    const urlParams = new URLSearchParams(window.location.search);
    const quizId = urlParams.get("quiz_id");

    let currentQuestionIndex = 0;
    let score = 0;
    let questions = [];

    async function loadQuestions() {
        try {
            const response = await fetch(`http://localhost:8000/quizzes/${quizId}/questions`);
            const data = await response.json();

            if (data.status === "success" && data.questions) {
                questions = data.questions;
                loadQuestion();
            } else {
                questionContainer.innerHTML = "<p>Nenhuma pergunta encontrada.</p>";
            }
        } catch (error) {
            console.error("Erro ao carregar perguntas:", error);
        }
    }

    function loadQuestion() {
        if (currentQuestionIndex < questions.length) {
            const question = questions[currentQuestionIndex];
            questionContainer.innerHTML = `<p>${question.question_text}</p>`;
        } else {
            endQuiz();
        }
    }

    function checkAnswer(userAnswer) {
        const question = questions[currentQuestionIndex];
        if (userAnswer === question.answer) {
            score++;
        } else {
            score--;
        }
        scoreDisplay.textContent = `Pontuação: ${score}`;
        currentQuestionIndex++;
        loadQuestion();
    }

    function endQuiz() {
        questionContainer.innerHTML = `<p>Quiz finalizado! Sua pontuação: ${score}</p>`;
        trueButton.style.display = "none";
        falseButton.style.display = "none";
    }

    trueButton.addEventListener("click", () => checkAnswer("verdadeiro"));
    falseButton.addEventListener("click", () => checkAnswer("falso"));

    loadQuestions();
});