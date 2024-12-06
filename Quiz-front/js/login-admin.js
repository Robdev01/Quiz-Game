document.getElementById('login-admin-form').addEventListener('submit', async function(event) {
    event.preventDefault(); 

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    try {
        const response = await fetch('http://localhost:8000/admins/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();
        console.log(data); // Debug do retorno da API

        if (response.ok) {
            if (data.role === 'admin') {
                window.location.href = 'admin-dashboard.html';
            } else {
                document.getElementById('message').textContent = 'Você não tem permissão de administrador.';
            }
        } else {
            document.getElementById('message').textContent = data.message || 'Erro ao fazer login.';
        }
    } catch (error) {
        console.error('Erro ao fazer requisição para a API:', error);
        document.getElementById('message').textContent = 'Erro ao conectar com a API.';
    }
});
