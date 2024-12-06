document.addEventListener("DOMContentLoaded", async function () {
    // URL da sua API
    const apiUrl = 'http://localhost:8000/quizzes/todos';  // Substitua com a URL correta da sua API

    // Função para carregar todos os quizzes
    async function loadQuizzes() {
        try {
            const response = await fetch(apiUrl);
            const data = await response.json();

            if (response.ok) {
                const quizList = document.getElementById('quiz-list');
                quizList.innerHTML = '';  // Limpa a lista antes de adicionar novos quizzes

                // Exibe cada quiz
                data.data.forEach(quiz => {
                    const quizItem = document.createElement('div');
                    quizItem.className = 'quiz-item';
                    quizItem.innerHTML = `
                        <h3>${quiz.title}</h3>
                        <button onclick="showQuestions(${quiz.id})">Ver Perguntas</button>
                    `;
                    quizList.appendChild(quizItem);
                });
            } else {
                alert('Erro ao carregar quizzes');
            }
        } catch (error) {
            console.error('Erro ao carregar quizzes:', error);
        }
    }

    // Função para exibir as perguntas de um quiz
    async function showQuestions(quizId) {
        try {
            const response = await fetch(`${apiUrl}/${quizId}`);
            const data = await response.json();

            if (response.ok) {
                const questionsContainer = document.getElementById('quiz-questions');
                questionsContainer.innerHTML = '';  // Limpa as perguntas antigas

                const quiz = data.data[0];  // Pegando o primeiro (e único) quiz, já que estamos fazendo por ID

                const quizTitle = document.getElementById('quiz-title');
                quizTitle.textContent = `Perguntas do Quiz: ${quiz.title}`;

                // Exibe cada pergunta
                quiz.questions.forEach(question => {
                    const questionItem = document.createElement('div');
                    questionItem.className = 'question-item';
                    questionItem.innerHTML = `
                        <p><strong>Questão:</strong> ${question.text}</p>
                        <p><strong>Descrição:</strong> ${question.description}</p>
                    `;
                    questionsContainer.appendChild(questionItem);
                });

                // Exibe a seção de perguntas
                document.getElementById('quiz-list').style.display = 'none';
                document.getElementById('quiz-container').style.display = 'block';
            } else {
                alert('Erro ao carregar perguntas');
            }
        } catch (error) {
            console.error('Erro ao carregar perguntas:', error);
        }
    }

    // Carregar os quizzes quando a página carregar
    loadQuizzes();
});
