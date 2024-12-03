CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255),
    is_admin BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    created_by INT NOT NULL,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    text TEXT NOT NULL,
    image_path VARCHAR(255),
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id)
);

CREATE TABLE participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    participant_id INT NOT NULL,
    question_id INT NOT NULL,
    selected_answer BOOLEAN NOT NULL,
    is_correct BOOLEAN NOT NULL,
    FOREIGN KEY (participant_id) REFERENCES participants(id),
    FOREIGN KEY (question_id) REFERENCES questions(id)
);

CREATE TABLE ranking (
    participant_id INT NOT NULL,
    points INT NOT NULL,
    PRIMARY KEY (participant_id),
    FOREIGN KEY (participant_id) REFERENCES participants(id)
);
