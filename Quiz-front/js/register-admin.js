document.getElementById('login-admin-form').addEventListener('submit', async function (event) {
    event.preventDefault(); // Previne o comportamento padrão do formulário

    // Captura os valores dos campos de email e senha
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    // URL da API, usando variável de ambiente para flexibilidade entre produção/desenvolvimento
    const apiUrl = 'http://localhost:8000'; // Pode ser ajustado para usar variáveis de ambiente

    // Campo de mensagem para exibir erros ou feedback ao usuário
    const messageField = document.getElementById('message');

    try {
        // Realiza a requisição para a API
        const response = await fetch(`${apiUrl}/admins/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password }) // Envia os dados do login
        });

        // Processa a resposta da API
        const data = await response.json();
        console.log('Resposta da API:', data); // Para debug

        if (response.ok) {
            // Se o login foi bem-sucedido e o usuário é administrador
            if (data.role === 'admin') {
                // Redireciona para o painel administrativo
                window.location.href = 'admin-dashboard.html';
            } else {
                // Exibe mensagem se o usuário não tiver permissões de administrador
                messageField.textContent = 'Você não tem permissão de administrador.';
                messageField.style.color = 'red';
            }
        } else {
            // Trata erros específicos retornados pela API
            messageField.textContent = data.message || 'Erro ao fazer login. Tente novamente.';
            messageField.style.color = 'red';
        }
    } catch (error) {
        // Lida com erros inesperados, como falha na conexão com a API
        console.error('Erro ao fazer requisição para a API:', error);
        messageField.textContent = 'Erro ao conectar com a API. Verifique sua conexão.';
        messageField.style.color = 'red';
    }
});
