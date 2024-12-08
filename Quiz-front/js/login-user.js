document.getElementById('login-form').addEventListener('submit', async function(event) {
    event.preventDefault();  // Previne o envio padrão do formulário

    // Coleta os valores dos campos do formulário
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    try {
        // Envia os dados para a API usando fetch
        const response = await fetch('http://localhost:8000/users/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'  // Define o tipo de conteúdo como JSON
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();
        console.log(data); // Debug do retorno da API

        if (response.ok) {
            // Se a resposta for bem-sucedida, armazena o token no localStorage (por exemplo)
            if (data.role === 'user') {  // Verifica se o token foi retornado
                 // Redireciona para a página inicial ou o painel de quiz
                window.location.href = 'quiz-selection.html';  // Você pode redirecionar para a página do quiz
            } 
        } else {
            // Caso contrário, exibe a mensagem de erro
            document.getElementById('message').textContent = data.message || 'Erro ao fazer login. Tente novamente.';
        }
    } catch (error) {
        // Caso haja um erro na requisição
        console.error('Erro ao fazer requisição para a API:', error);
        document.getElementById('message').textContent = 'Erro ao conectar com a API. Tente novamente.';
    }
});
