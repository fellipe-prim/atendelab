 CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    perfil ENUM('admin', 'atendente') DEFAULT 'atendente',
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pessoas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    documento VARCHAR(20) UNIQUE,
    telefone VARCHAR(20),
    curso VARCHAR(100),
    periodo VARCHAR(100),
    status VARCHAR(100)
);

CREATE TABLE tipos_atendimentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    descricao TEXT,  
    status ENUM(
        'agendado',
        'em_andamento',
        'cancelado',
        'concluido'
    ) NOT NULL DEFAULT 'agendado'
);

CREATE TABLE atendimentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pessoa_id INT NOT NULL,
    tipo_atendimento INT NOT NULL,
    usuario_id INT NOT NULL,
    data_atendimento DATE,
    hora_atendimento TIME,
    descricao TEXT,
    observacao TEXT,
    status ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    criado_em TIMESTAMP,
    CONSTRAINT fk_pessoas_id FOREIGN KEY(pessoa_id) REFERENCES pessoas(id),
    CONSTRAINT fk_tipo_atendimento FOREIGN KEY(tipo_atendimento) REFERENCES tipos_atendimentos(id),
    CONSTRAINT fk_usuario_id FOREIGN KEY(usuario_id) REFERENCES usuarios(id)
);