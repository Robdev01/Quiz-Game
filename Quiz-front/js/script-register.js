// register.js - Arquivo Externo

// Quando o formulário for submetido
document.getElementById('register-form').addEventListener('submit', async function(event) {
    event.preventDefault();  // Previne o envio padrão do formulário

    // Coleta os valores dos campos do formulário
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    try {
        // Envia os dados para a API usando fetch
        const response = await fetch('http://localhost:8000/users/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'  // Define o tipo de conteúdo como JSON
            },
            body: JSON.stringify({ name, email, password })  // Converte o objeto em JSON
        });

        const data = await response.json();  // A API retornará uma resposta em formato JSON

        if (response.ok) {
            // Se a resposta for bem-sucedida, redireciona para a página de login
            window.location.href = 'login-user.html';  // Redireciona para a tela de login
        } else {
            // Caso contrário, exibe a mensagem de erro
            document.getElementById('message').textContent = data.message || 'Erro ao cadastrar. Tente novamente.';
        }
    } catch (error) {
        // Caso haja um erro na requisição
        console.error('Erro ao fazer requisição para a API:', error);
        document.getElementById('message').textContent = 'Erro ao conectar com a API. Tente novamente.';
    }
});
