CREATE DATABASE sistematica_VCJF


CREATE TABLE admin (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    nome_admin VARCHAR(50) NOT NULL UNIQUE,
    senha_admin VARCHAR(255) NOT NULL,
    email_admin VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE professores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_admin INT NOT NULL,
    nome_professor VARCHAR(50) NOT NULL UNIQUE,
    senha_professor VARCHAR(255) NOT NULL,
    email_professor VARCHAR(100) NOT NULL UNIQUE,
    FOREIGN KEY (id_admin) REFERENCES admin(id_admin) ON DELETE CASCADE
);

CREATE TABLE disciplina (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
);

CREATE TABLE turma (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    nivel_ensino ENUM('Fundamental 2', 'Ensino Médio') NOT NULL -- Adicionada a coluna de nível de ensino
);

CREATE TABLE professordisciplinaturma (
    id_pdt INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_professor INT NOT NULL,
    id_disciplina INT NOT NULL,
    id_turma INT NOT NULL,
    FOREIGN KEY (id_professor) REFERENCES professores (id) ON DELETE CASCADE,
    FOREIGN KEY (id_disciplina) REFERENCES disciplina(id) ON DELETE CASCADE,
    FOREIGN KEY (id_turma) REFERENCES turma(id) ON DELETE CASCADE
);

CREATE TABLE Sistematica (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_admin INT NOT NULL,
    id_pdt INT NOT NULL,
    bimestre TINYINT NOT NULL CHECK (bimestre BETWEEN 1 AND 4),
    ano INT NOT NULL,
    aprovada TINYINT(1) DEFAULT NULL, -- Indicar se a avaliação foi aprovada
    mensagem_reprovacao TEXT, -- Mensagem de reprovação, se aplicável
    status ENUM('Pendente', 'Concluída') DEFAULT 'Pendente',
    data_envio TIMESTAMP DEFAULT CONVERT_TZ(CURRENT_TIMESTAMP, '+00:00', '-03:00'),
    FOREIGN KEY (id_admin) REFERENCES admin(id_admin) ON DELETE CASCADE,
    FOREIGN KEY (id_pdt) REFERENCES professordisciplinaturma (id_pdt) ON DELETE CASCADE
);

CREATE TABLE AV1 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_sistematica INT NOT NULL,
    descricao TEXT NOT NULL,
    data_limite DATE NOT NULL,
    FOREIGN KEY (id_sistematica) REFERENCES Sistematica(id) ON DELETE CASCADE
);

CREATE TABLE AV2 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_sistematica INT NOT NULL,
    descricao TEXT NOT NULL,
    data_limite DATE NOT NULL,
    FOREIGN KEY (id_sistematica) REFERENCES Sistematica(id) ON DELETE CASCADE
);

CREATE TABLE PD (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_sistematica INT NOT NULL,
    descricao TEXT NOT NULL,
    data_limite DATE NOT NULL,
    FOREIGN KEY (id_sistematica) REFERENCES Sistematica(id) ON DELETE CASCADE
);

CREATE TABLE Recuperacao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_sistematica INT NOT NULL,
    descricao TEXT NOT NULL,
    data_limite DATE NOT NULL,
    FOREIGN KEY (id_sistematica) REFERENCES Sistematica(id) ON DELETE CASCADE
);