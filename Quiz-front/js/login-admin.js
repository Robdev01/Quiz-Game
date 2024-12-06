// login-admin.js - Arquivo Externo

document.getElementById('login-admin-form').addEventListener('submit', async function(event) {
    event.preventDefault();  // Previne o envio padrão do formulário

    // Coleta os valores dos campos do formulário
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    try {
        // Envia os dados para a API de login usando fetch
        const response = await fetch('http://localhost:8000/admins/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })  // Envia as credenciais de login
        });
        

        const data = await response.json();  // A API retornará uma resposta em formato JSON

        if (response.ok) {
            // Se a resposta for bem-sucedida, armazena o token no localStorage
            localStorage.setItem('auth_token', data.token);  // Armazena o token de autenticação

            // Verifica se o usuário tem o papel de administrador (role)
            const userRole = data.role;
            if (userRole == 'admin') {
                // Redireciona para a página do painel de administração
                window.location.href = 'admin-dashboard.html';
            } else {
                // Caso o usuário não seja admin, exibe mensagem
                document.getElementById('message').textContent = 'Você não tem permissão de administrador.';
            }
        } else {
            // Caso a resposta da API indique falha no login
            document.getElementById('message').textContent = data.message || 'Erro ao fazer login. Tente novamente.';
        }
    } catch (error) {
        // Caso haja erro na requisição
        console.error('Erro ao fazer requisição para a API:', error);
        document.getElementById('message').textContent = 'Erro ao conectar com a API. Tente novamente.';
    }
});
