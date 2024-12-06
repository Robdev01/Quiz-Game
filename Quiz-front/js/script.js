document.addEventListener("DOMContentLoaded", function() {
    // Formulários de cadastro
    const userRegisterForm = document.getElementById("user-register-form");
    const adminRegisterForm = document.getElementById("admin-register-form");

    // Elemento para mostrar mensagens
    const messageDiv = document.getElementById("message");

    // Função para mostrar a mensagem de sucesso ou erro
    function showMessage(message, type) {
        messageDiv.textContent = message;
        messageDiv.classList.add(type === 'success' ? 'success' : 'error');
    }

    // Função de cadastro de usuário
    async function registerUser(name, email, password) {
        const response = await fetch('/users/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ name, email, password })
        });

        const data = await response.json();
        if (response.ok) {
            showMessage('Usuário cadastrado com sucesso!', 'success');
        } else {
            showMessage(data.message, 'error');
        }
    }

    // Função de cadastro de administrador
    async function registerAdmin(name, email, password) {
        const response = await fetch('/admins/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ name, email, password })
        });

        const data = await response.json();
        if (response.ok) {
            showMessage('Administrador cadastrado com sucesso!', 'success');
        } else {
            showMessage(data.message, 'error');
        }
    }

    // Enviar o formulário de cadastro de usuário
    userRegisterForm.addEventListener("submit", function(event) {
        event.preventDefault();

        const name = document.getElementById("user-name").value;
        const email = document.getElementById("user-email").value;
        const password = document.getElementById("user-password").value;

        // Chama a função de cadastro de usuário
        registerUser(name, email, password);
    });

    // Enviar o formulário de cadastro de administrador
    adminRegisterForm.addEventListener("submit", function(event) {
        event.preventDefault();

        const name = document.getElementById("admin-name").value;
        const email = document.getElementById("admin-email").value;
        const password = document.getElementById("admin-password").value;

        // Chama a função de cadastro de administrador
        registerAdmin(name, email, password);
    });
});
