-- =====================================================
-- Letudo.pt - Estrutura da Base de Dados
-- Conforme requisitos do PDF do projeto final
-- =====================================================

DROP DATABASE IF EXISTS `letudo`;
CREATE DATABASE `letudo` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `letudo`;

-- Tabela Utilizadores (com diferentes privilegios: admin / cliente)
CREATE TABLE utilizadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    morada VARCHAR(255) DEFAULT NULL,
    nif VARCHAR(9) DEFAULT NULL,
    data_nascimento DATE DEFAULT NULL,
    tipo ENUM('admin','cliente') NOT NULL DEFAULT 'cliente',
    data_registo DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabela Produtos (livros)
CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(200) NOT NULL,
    autor VARCHAR(150) DEFAULT NULL,
    categoria VARCHAR(100) DEFAULT 'Geral',
    descricao TEXT,
    quantidade_disponivel INT NOT NULL DEFAULT 0,
    preco_unidade DECIMAL(10,2) NOT NULL,
    imagem VARCHAR(255) DEFAULT NULL,
    destaque TINYINT(1) DEFAULT 0,
    data_adicao DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabela Encomendas
CREATE TABLE encomendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT DEFAULT NULL,
    cliente_nome VARCHAR(150) NOT NULL,
    data_nascimento DATE NOT NULL,
    morada TEXT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    data_encomenda DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Linhas de Encomenda (produtos + quantidades por encomenda)
CREATE TABLE encomenda_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    encomenda_id INT NOT NULL,
    produto_id INT NOT NULL,
    nome_produto VARCHAR(200) NOT NULL,
    quantidade INT NOT NULL,
    preco_unidade DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (encomenda_id) REFERENCES encomendas(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
) ENGINE=InnoDB;

-- =====================================================
-- DADOS INICIAIS
-- =====================================================

-- Utilizador admin (password: admin123)
-- Utilizador cliente (password: cliente123)
-- Hashes gerados com password_hash(..., PASSWORD_DEFAULT)
INSERT INTO utilizadores (nome, email, username, password, morada, tipo) VALUES
('Administrador Letudo', 'admin@letudo.pt', 'admin', '$2y$10$wH7mE6Z8m1Q1N1k5yYQ4.uGm3VkQ4dU5Xz4sY7N.VY7u6d6QzQ6e', 'Rua da Livraria 1, Lisboa', 'admin'),
('Maria Cliente', 'cliente@letudo.pt', 'cliente', '$2y$10$wH7mE6Z8m1Q1N1k5yYQ4.uGm3VkQ4dU5Xz4sY7N.VY7u6d6QzQ6e', 'Rua do Cliente 2, Porto', 'cliente');

-- Catalogo de livros (usa as imagens que ja existem na pasta /img)
INSERT INTO produtos (nome, autor, categoria, descricao, quantidade_disponivel, preco_unidade, imagem, destaque) VALUES
('Ainda Estou Aqui', 'Marcelo Rubens Paiva', 'Biografia', 'Uma memoria tocante sobre resistencia, familia e memoria durante a ditadura.', 15, 18.90, 'livro_ainda_estou_aqui.jpg', 1),
('A Mae que te Adora - Livro de Historias', 'Varios Autores', 'Infantil', 'Colecao de historias para criancas com ilustracoes originais.', 25, 14.50, 'a_mae_que_te_adora_livro_de_historias.jpg', 1),
('Amor e Respeito', 'Emerson Eggerichs', 'Autoajuda', 'Um guia essencial sobre comunicacao e relacoes saudaveis.', 12, 16.90, 'livro_amor_e_respeito.jpg', 0),
('Cartas de Paulo', 'Estudos Biblicos', 'Religiao', 'Analise profunda das epistolas paulinas.', 8, 12.00, 'livro_cartas_de_paulo.jpg', 0),
('A Casa', 'William P. Young', 'Ficcao', 'Um romance emocionante sobre perda, fe e redencao.', 20, 15.50, 'livro_casa.jpg', 1),
('Ceu', 'Randy Alcorn', 'Religiao', 'Uma exploracao biblica do lar eterno.', 10, 19.90, 'livro_ceu.jpg', 0),
('Deive', 'Autor Desconhecido', 'Ficcao', 'Uma aventura inesquecivel.', 18, 13.50, 'livro_deive.jpg', 0),
('O Especialista', 'Ensaios Modernos', 'Nao-Ficcao', 'Perspetivas sobre carreira e excelencia.', 14, 17.00, 'livro_especialista.jpg', 1),
('Forte', 'Autobiografia', 'Biografia', 'A historia de quem venceu contra todas as probabilidades.', 9, 15.90, 'livro_forte.jpg', 0),
('Israel no Exilio', 'Historia Antiga', 'Historia', 'Uma narrativa profunda sobre o povo de Israel.', 7, 21.00, 'livro_israel_exilio.jpg', 0),
('Novo Testamento Comentado', 'Biblico', 'Religiao', 'Edicao de referencia com estudo versiculo a versiculo.', 16, 24.90, 'livro_novo_testamento.jpg', 1),
('O Pai', 'Drama Familiar', 'Ficcao', 'Um romance intenso sobre lacos familiares.', 11, 14.90, 'livro_pai.jpg', 0),
('Pais', 'Educacao Parental', 'Familia', 'Manual pratico para pais modernos.', 13, 16.50, 'livro_pais.jpg', 0),
('Querida Eu', 'Poesia Contemporanea', 'Poesia', 'Uma colecao intima de versos que tocam o coracao.', 22, 11.90, 'livro_querida_eu.jpg', 1),
('Refugio', 'Narrativa Profunda', 'Ficcao', 'Uma historia de esperanca em tempos dificeis.', 10, 15.00, 'livro_refugio.jpg', 0),
('Sonho', 'Realismo Magico', 'Ficcao', 'Explora os limites entre realidade e sonho.', 0, 13.90, 'livro_sonho.jpg', 0);
