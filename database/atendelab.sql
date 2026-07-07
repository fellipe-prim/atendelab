-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Tempo de geração: 02/07/2026 às 23:05
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `atendelab`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `atendimentos`
--

CREATE TABLE `atendimentos` (
  `id` int(11) NOT NULL,
  `pessoa_id` int(11) NOT NULL,
  `tipo_atendimento_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `data_atendimento` date NOT NULL,
  `hora_atendimento` time NOT NULL,
  `descricao` text DEFAULT NULL,
  `observacao` text DEFAULT NULL,
  `status` enum('ABERTO','EM_ANDAMENTO','CONCLUIDO','CANCELADO') DEFAULT 'ABERTO',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacao_final` text DEFAULT NULL,
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `atendimentos`
--

INSERT INTO `atendimentos` (`id`, `pessoa_id`, `tipo_atendimento_id`, `usuario_id`, `data_atendimento`, `hora_atendimento`, `descricao`, `observacao`, `status`, `criado_em`, `observacao_final`, `atualizado_em`) VALUES
(1, 1, 1, 1, '2026-06-11', '22:26:57', 'O aluno Pedro veio tirar uma dúvida sobre a instalação do XAMPP.', 'Atendimento realizado via balcão.', 'CONCLUIDO', '2026-06-12 01:26:57', 'Atendimento finalizado com sucesso. O aluno entendeu o roteamento.', '2026-07-01 23:47:52'),
(2, 1, 1, 1, '2026-06-15', '14:30:00', 'Aluno com dúvida na Aula 04 de Fábrica de Software.', NULL, 'ABERTO', '2026-07-01 22:54:12', NULL, '2026-07-01 22:54:12'),
(3, 4, 3, 1, '2026-07-06', '20:00:00', 'Quero ver todos os jogos da copa', NULL, 'EM_ANDAMENTO', '2026-07-02 20:33:07', NULL, '2026-07-02 20:35:16');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pessoas`
--

CREATE TABLE `pessoas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `documento` varchar(20) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `curso` varchar(100) NOT NULL,
  `periodo` varchar(100) NOT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacoes` text DEFAULT NULL,
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pessoas`
--

INSERT INTO `pessoas` (`id`, `nome`, `documento`, `telefone`, `email`, `curso`, `periodo`, `status`, `criado_em`, `observacoes`, `atualizado_em`) VALUES
(1, 'Mario de souza', '7777777777777', '(47) 888888888', 'mario@gmail.com', 'Engenharia de Software', '6º', 'ativo', '2026-06-28 17:30:07', 'atualizado', '2026-07-02 20:55:00'),
(2, 'Carlos Souza', '98765432100', '(47) 98888-7777', 'carlos@gmail.com', 'Engenharia de Software', '5º Período', 'ativo', '2026-06-28 17:30:07', '', '2026-07-02 20:54:24'),
(3, 'kaka', '111.111.111-11', '(47) 77777-7777', 'kaka@gmail.com', 'Técnico de futebol', '5º', 'ativo', '2026-07-01 22:07:50', 'jogador caro', '2026-07-02 20:54:50'),
(4, 'Pedro', '888888888888', '(55) 5555-55555', 'pedro@gmail.com', 'Engenharia de Software', '5', 'ativo', '2026-07-02 20:06:22', '', '2026-07-02 20:06:22');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos_atendimentos`
--

CREATE TABLE `tipos_atendimentos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('ativo','inativo','','') NOT NULL,
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tipos_atendimentos`
--

INSERT INTO `tipos_atendimentos` (`id`, `nome`, `descricao`, `criado_em`, `status`, `atualizado_em`) VALUES
(1, 'Dúvida Acadêmica', 'Atendimento para tirar dúvidas sobre matérias e notas.', '2026-06-28 17:30:25', 'inativo', '2026-07-01 22:29:26'),
(2, 'Solicitação de Documento', 'Pedido de histórico escolar ou certificados.', '2026-06-28 17:30:25', 'ativo', '2026-06-28 17:24:11'),
(3, 'Copa', 'Copa do mundo', '2026-07-01 22:28:13', 'ativo', '2026-07-02 21:01:01'),
(4, 'teste', 'teste1', '2026-07-02 20:56:31', 'inativo', '2026-07-02 20:56:37');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('admin','atendente') DEFAULT 'atendente',
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `perfil`, `status`, `criado_em`, `atualizado_em`) VALUES
(1, 'Administrador', 'admin@atendelab.com', '$2y$10$GZkTPC/UMxPLey9DWXNNzuW4aTvaS5nHeE2OZYRx0FIjIXtOaFl1i', 'admin', 'ativo', '2026-06-02 00:22:41', '2026-06-28 17:24:11'),
(3, 'Bruxo', 'Bruxo@gmail.com', '$2y$10$ZWP7upA5QGhu4DvWO198hOL3hkzbEzIllSgN40e3CEj.2dPpqWbS.', '', 'ativo', '2026-06-12 01:10:13', '2026-06-28 17:24:11');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `atendimentos`
--
ALTER TABLE `atendimentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_atendimento_pessoa` (`pessoa_id`),
  ADD KEY `fk_atendimento_tipo` (`tipo_atendimento_id`),
  ADD KEY `fk_atendimento_usuario` (`usuario_id`);

--
-- Índices de tabela `pessoas`
--
ALTER TABLE `pessoas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `doc` (`documento`);

--
-- Índices de tabela `tipos_atendimentos`
--
ALTER TABLE `tipos_atendimentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `atendimentos`
--
ALTER TABLE `atendimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `pessoas`
--
ALTER TABLE `pessoas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `tipos_atendimentos`
--
ALTER TABLE `tipos_atendimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `atendimentos`
--
ALTER TABLE `atendimentos`
  ADD CONSTRAINT `fk_atendimento_pessoa` FOREIGN KEY (`pessoa_id`) REFERENCES `pessoas` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_atendimento_tipo` FOREIGN KEY (`tipo_atendimento_id`) REFERENCES `tipos_atendimentos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_atendimento_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;