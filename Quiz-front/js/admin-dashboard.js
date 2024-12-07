document.addEventListener("DOMContentLoaded", () => {
    const quizApiUrl = "http://localhost:8000/quizzes/todos";
    const userApiUrl = "http://localhost:8000/users/todos";
    const quizForm = document.getElementById("quizForm");  // Corrigido id do formulário
    const quizList = document.getElementById("quiz-list");
    const userList = document.getElementById("user-list");
    const questionForm = document.getElementById('questionForm');
    const quizIdSelect = document.getElementById('quizId');
    
    // Função para cadastrar um Quiz
    quizForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const quizTitle = document.getElementById('quizTitle').value;

        fetch('http://localhost:8000/quizzes/criar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ title: quizTitle })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Quiz cadastrado com sucesso!');
                loadQuizzes(); // Recarrega a lista de quizzes para o select
            } else {
                alert('Erro ao cadastrar quiz!');
            }
        })
        .catch(error => {
            alert('Erro de rede: ' + error);
        });
    });
    
    // Função para cadastrar uma Pergunta
    questionForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const quizId = quizIdSelect.value;
        const questionText = document.getElementById('questionText').value;
        const answer = document.getElementById('answer').value;

        fetch('http://localhost:8000/questions/criar', {  // Corrigido para usar URL completa
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                quiz_id: quizId,
                question_text: questionText,
                answer: answer,
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Pergunta cadastrada com sucesso!');
            } else {
                alert('Erro ao cadastrar pergunta!');
            }
        })
        .catch(error => {
            alert('Erro de rede: ' + error);
        });
    });

    // Função para carregar os Quizzes cadastrados
    async function loadQuizzes() {
        try {
            const response = await fetch('http://localhost:8000/quizzes/todos');
            if (!response.ok) throw new Error("Erro ao carregar quizzes");
    
            const data = await response.json();
            console.log(data); // Verifique no console os dados retornados pela API
    
            // Verifique se a resposta tem a estrutura correta (status 'success' e dados de quizzes)
            if (data.status === 'success' && data.quizzes) {
                // Preencher o select com quizzes
                quizIdSelect.innerHTML = '<option value="">Selecione um Quiz</option>'; // Opção inicial
    
                data.quizzes.forEach(quiz => {
                    const option = document.createElement('option');
                    option.value = quiz.id; // Atribui o id do quiz
                    option.textContent = quiz.title; // Atribui o título do quiz
                    quizIdSelect.appendChild(option);
                });
    
                // Opcionalmente, preenche a tabela com os quizzes também
                quizList.innerHTML = data.quizzes
                    .map(
                        (quiz) => `
                            <tr>
                                <td>${quiz.id}</td>
                                <td>${quiz.title}</td>
                                <td>${quiz.created_at}</td>
                               <td class="Ações">
                                    <button onclick="editQuiz(${quiz.id})">Editar</button>
                                    <button onclick="deleteQuiz(${quiz.id})">Deletar</button>
                                </td>
                            </tr>
                        `
                    )
                    .join("");
            } else {
                quizIdSelect.innerHTML = "<option value=''>Nenhum quiz encontrado</option>";
                quizList.innerHTML = "<tr><td colspan='4'>Nenhum quiz encontrado.</td></tr>";
            }
        } catch (error) {
            console.error("Erro ao carregar quizzes:", error);
            quizIdSelect.innerHTML = "<option value=''>Erro ao carregar quizzes</option>";
            quizList.innerHTML = "<tr><td colspan='4'>Erro ao carregar quizzes.</td></tr>";
        }
    }
    
    // Função para editar o Quiz
function editQuiz(quizId) {
    // Aqui você pode fazer uma requisição para buscar os dados do quiz e preencher um formulário de edição
    fetch(`http://localhost:8000/quizzes/${quizId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.quiz) {
                const quiz = data.quiz;
                document.getElementById('quizTitle').value = quiz.title;  // Preenche o campo de título
                document.getElementById('quizDescription').value = quiz.description;  // Preenche o campo de descrição
                // Aqui, você poderia fazer o formulário de edição visível, caso esteja escondido inicialmente
                alert('Edite as informações do quiz.');
            } else {
                alert('Erro ao carregar dados para editar.');
            }
        })
        .catch(error => {
            console.error('Erro ao carregar quiz:', error);
            alert('Erro ao editar quiz.');
        });
}
// Função para deletar o Quiz e suas perguntas
async function deleteQuiz(quizId) {
    if (confirm('Tem certeza que deseja excluir este quiz e suas perguntas?')) {
        try {
            // Deleta o quiz primeiro
            const response = await fetch(`http://localhost:8000/quizzes/${quizId}`, { method: "DELETE" });
            if (!response.ok) throw new Error("Erro ao deletar quiz");

            // Depois, deleta as perguntas associadas
            const deleteQuestionsResponse = await fetch(`http://localhost:8000/questions/${quizId}`, { method: "DELETE" });
            if (!deleteQuestionsResponse.ok) throw new Error("Erro ao deletar perguntas do quiz");

            alert('Quiz e perguntas deletados com sucesso!');
            loadQuizzes(); // Recarrega a lista de quizzes
        } catch (error) {
            console.error('Erro ao deletar quiz:', error);
            alert('Erro ao deletar quiz.');
        }
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

        const title = document.getElementById("quizTitle").value;  // Corrigido ID para title
        const description = document.getElementById("quizDescription").value;  // Certifique-se de ter o campo para descrição no HTML

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


    // Carrega os quizzes e usuários ao iniciar
    loadQuizzes();
    loadUsers();
});
