# Documentação do Projeto de Quiz

## 1. Introdução
Este projeto consiste em um sistema de quiz com interface para usuários e administradores. Ele permite:

- Usuários se registrarem, fazerem login e responderem quizzes.
- Administradores gerenciarem os quizzes através de um dashboard.

---

## 2. Estrutura do Projeto
O projeto está dividido em camadas para separar responsabilidades, facilitando a manutenção e expansão. Abaixo está a descrição da estrutura:

### Diretórios Principais
#### **`app/controllers`** 
Contém os controladores que gerenciam a lógica de negócio.
- **`adminController.php`**: Controlador responsável pelas funcionalidades dos administradores, como gerenciamento de quizzes.
- **`QuestionController.php`**: Lida com as operações relacionadas às perguntas do quiz.
- **`QuizController.php`**: Gerencia as operações do quiz, como criação e exibição.
- **`UserController.php`**: Controlador para funcionalidades de usuários, como login e registro.

#### **`app/utils`**
Diretório para utilitários e configurações gerais.
- **`Response.php`**: Define um padrão para as respostas enviadas ao cliente.
- **`config.php`**: Contém configurações globais, como conexão ao banco de dados.
- **`routes.php`**: Define as rotas do sistema, mapeando URLs para controladores e métodos.

#### **`public`**
Diretório público acessível pela web.
- **`.htaccess`**: Configurações para redirecionamento e segurança.
- **`index.php`**: Ponto de entrada principal do sistema.

#### **`Quiz-front`**
Contém os arquivos estáticos da interface.
- **`css/`**: Arquivos de estilo.
- **`imagens/`**: Recursos visuais, como ícones ou imagens.
- **`js/`**: Scripts JavaScript para interatividade.
- Arquivos HTML, como `index.html`, `quiz.html`, e `register-user.html`, que representam páginas da aplicação.

#### **`sql`**
Scripts de banco de dados.
- **`schema.sql`**: Arquivo para criação das tabelas e estrutura do banco de dados.

---

## 3. Funcionalidades

### Usuários
- **Cadastro**: Usuários podem se registrar usando `register-user.html`.
- **Login**: Sistema de autenticação implementado em `login-user.html`.
- **Participação em quizzes**: Após logados, os usuários podem responder quizzes.

### Administradores
- **Cadastro e Login**: Administradores têm páginas dedicadas (`register-admin.html` e `login-admin.html`).
- **Gerenciamento de Quizzes**: Administradores podem criar, editar e excluir perguntas dos quizzes no dashboard (`qui-details.html`).

---

## 4. Banco de Dados
O arquivo **`schema.sql`** define a estrutura do banco de dados:

### Tabelas Principais
- **`users`**: Armazena informações dos usuários.
- **`admins`**: Armazena informações dos administradores.
- **`quizzes`**: Contém os quizzes disponíveis no sistema.
- **`questions`**: Armazena as perguntas relacionadas aos quizzes.
- **`answers`**: Guarda as respostas dos usuários.

---

## 5. Rotas do Sistema
As rotas são definidas no arquivo **`routes.php`** e seguem o padrão RESTful:

### Usuários
- **`POST /user/register`**: Registro de usuários.
- **`POST /user/login`**: Login de usuários.
- **`GET /user/quiz`**: Listagem de quizzes disponíveis.

### Administradores
- **`POST /admin/register`**: Registro de administradores.
- **`POST /admin/login`**: Login de administradores.
- **`POST /admin/quiz`**: Criação de quizzes.
- **`DELETE /admin/quiz/{id}`**: Exclusão de um quiz.

---

## 6. Configuração e Execução

### Requisitos
- **PHP 7.4+**
- **Servidor Apache** com suporte a `.htaccess`.
- **Banco de dados MySQL**.

### Instalação
1. Configure o arquivo **`config.php`** com as credenciais do banco de dados.
2. Importe o arquivo **`schema.sql`** no banco de dados.
3. Coloque os arquivos em um servidor local (como XAMPP ou WAMP).

### Acesso
- **Frontend**: Navegue para o diretório `Quiz-front` e abra `index.html`.
- **Backend**: Use `php -S localhost:8000` para testar as rotas.

---

## 7. Considerações Finais
Este projeto pode ser expandido para incluir:
- Autenticação com JWT para maior segurança.
- Funcionalidades de ranking de usuários.
- Paginação na listagem de quizzes.
