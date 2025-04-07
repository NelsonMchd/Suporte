-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 13-Fev-2025 às 13:16
-- Versão do servidor: 10.4.28-MariaDB
-- versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: suporte
--

-- --------------------------------------------------------

--
-- Estrutura da tabela blocos
--

CREATE TABLE blocos (
  cod_bloco int(11) NOT NULL,
  descricao_bloco varchar(100) NOT NULL,
  estado tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela blocos
--

INSERT INTO blocos (cod_bloco, descricao_bloco, estado) VALUES
(1, 'Bloco A', 1),
(2, 'Bloco B', 1),
(3, 'Bloco C', 1),
(4, 'Bloco D', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela equipamentos
--

CREATE TABLE equipamentos (
  Cod_Equipamento int(11) NOT NULL,
  Descricao_Equipamento varchar(150) NOT NULL,
  Obs_Equipamento varchar(150) NOT NULL,
  Estado_Equipamento varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela equipamentos
--

INSERT INTO equipamentos (Cod_Equipamento, Descricao_Equipamento, Obs_Equipamento, Estado_Equipamento) VALUES
(1, 'Computador DELL', 'Com alguns danos externos', 'Ativo'),
(2, 'Projetor Epson XPTO1', '', 'Ativo');

-- --------------------------------------------------------

--
-- Estrutura da tabela equipamentos_localizacao
--

CREATE TABLE equipamentos_localizacao (
  Cod_Equipamento int(11) NOT NULL,
  Cod_Sala int(11) NOT NULL,
  Data_Inicio date NOT NULL,
  Data_Fim date DEFAULT NULL,
  Estado_localizacao varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela equipamentos_localizacao
--

INSERT INTO equipamentos_localizacao (Cod_Equipamento, Cod_Sala, Data_Inicio, Data_Fim, Estado_localizacao) VALUES
(1, 1, '2025-02-01', NULL, 'Ativo'),
(2, 2, '2024-10-01', NULL, 'Ativo');

-- --------------------------------------------------------

--
-- Estrutura da tabela ocorrencias
--

CREATE TABLE ocorrencias (
  id_ocorrencia int(11) NOT NULL,
  idutil int(11) NOT NULL,
  contato varchar(15) NOT NULL,
  prob_utilizador text NOT NULL,
  prob_encontrado text DEFAULT NULL,
  solucao text DEFAULT NULL,
  estado enum('ABERTO','EM CURSO','RESOLVIDO') NOT NULL,
  data_abertura datetime DEFAULT current_timestamp(),
  data_decorrer datetime DEFAULT NULL,
  data_finalizada datetime DEFAULT NULL,
  tecnico varchar(100) DEFAULT NULL,
  equipamento varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela ocorrencias
--

INSERT INTO ocorrencias (id_ocorrencia, idutil, contato, prob_utilizador, prob_encontrado, solucao, estado, data_abertura, data_decorrer, data_finalizada, tecnico, equipamento) VALUES
(2, 2, '23454345567', 'Problema de Internet', NULL, NULL, 'ABERTO', '2024-11-26 11:06:30', NULL, NULL, NULL, NULL),
(3, 5, '9656544543', 'PC não Liga', NULL, NULL, 'ABERTO', '2024-11-28 12:30:37', NULL, NULL, NULL, 'PC34');

-- --------------------------------------------------------

--
-- Estrutura da tabela pisos
--

CREATE TABLE pisos (
  Cod_piso int(11) NOT NULL,
  Descricao_piso varchar(100) NOT NULL,
  Estado tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela pisos
--

INSERT INTO pisos (Cod_piso, Descricao_piso, Estado) VALUES
(1, 'Piso 1', 1),
(2, 'Piso 2', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela salas
--

CREATE TABLE salas (
  cod_sala int(11) NOT NULL,
  Nome_sala varchar(50) NOT NULL,
  Bloco_sala int(11) NOT NULL,
  Piso_sala int(11) NOT NULL,
  Observações varchar(150) NOT NULL,
  Estado tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela salas
--

INSERT INTO salas (cod_sala, Nome_sala, Bloco_sala, Piso_sala, Observações, Estado) VALUES
(1, 'Sala 1', 1, 1, '', 1),
(2, 'Sala 13', 3, 2, '', 0),
(3, 'Sala 2', 1, 1, '', 1),
(4, 'Sala 14', 3, 2, '', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela utilizadores
--

CREATE TABLE utilizadores (
  id int(11) NOT NULL,
  nome varchar(100) NOT NULL,
  login varchar(50) NOT NULL,
  pass varchar(255) NOT NULL,
  status enum('ativo','inativo') NOT NULL,
  nivel enum('administrador','utilizador','tecnico') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela utilizadores
--

INSERT INTO utilizadores (id, nome, login, pass, status, nivel) VALUES
(2, 'Ricardo Castro', 'rapc', 'e10adc3949ba59abbe56e057f20f883e', 'ativo', 'administrador'),
(3, 'Pedro Faria', 'pfaria', 'caf1a3dfb505ffed0d024130f58c5cfa', 'ativo', 'utilizador'),
(4, 'Carlos Faria', 'cfaria', 'caf1a3dfb505ffed0d024130f58c5cfa', 'ativo', 'tecnico'),
(5, 'Administrador', 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'ativo', 'administrador');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela blocos
--
ALTER TABLE blocos
  ADD PRIMARY KEY (cod_bloco);

--
-- Índices para tabela equipamentos
--
ALTER TABLE equipamentos
  ADD PRIMARY KEY (Cod_Equipamento);

--
-- Índices para tabela equipamentos_localizacao
--
ALTER TABLE equipamentos_localizacao
  ADD PRIMARY KEY (Cod_Equipamento,Cod_Sala,Data_Inicio),
  ADD KEY Cod_Sala (Cod_Sala);

--
-- Índices para tabela ocorrencias
--
ALTER TABLE ocorrencias
  ADD PRIMARY KEY (id_ocorrencia),
  ADD KEY idutil (idutil);

--
-- Índices para tabela pisos
--
ALTER TABLE pisos
  ADD PRIMARY KEY (Cod_piso);

--
-- Índices para tabela salas
--
ALTER TABLE salas
  ADD PRIMARY KEY (cod_sala),
  ADD KEY Bloco_Sala (Bloco_sala,Piso_sala),
  ADD KEY Piso_sala (Piso_sala);

--
-- Índices para tabela utilizadores
--
ALTER TABLE utilizadores
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY login (login);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela blocos
--
ALTER TABLE blocos
  MODIFY cod_bloco int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela equipamentos
--
ALTER TABLE equipamentos
  MODIFY Cod_Equipamento int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela ocorrencias
--
ALTER TABLE ocorrencias
  MODIFY id_ocorrencia int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela pisos
--
ALTER TABLE pisos
  MODIFY Cod_piso int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela salas
--
ALTER TABLE salas
  MODIFY cod_sala int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela utilizadores
--
ALTER TABLE utilizadores
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela equipamentos_localizacao
--
ALTER TABLE equipamentos_localizacao
  ADD CONSTRAINT equipamentos_localizacao_ibfk_1 FOREIGN KEY (Cod_Sala) REFERENCES salas (cod_sala) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT equipamentos_localizacao_ibfk_2 FOREIGN KEY (Cod_Equipamento) REFERENCES equipamentos (Cod_Equipamento) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela ocorrencias
--
ALTER TABLE ocorrencias
  ADD CONSTRAINT ocorrencias_ibfk_1 FOREIGN KEY (idutil) REFERENCES utilizadores (id) ON DELETE CASCADE;

--
-- Limitadores para a tabela salas
--
ALTER TABLE salas
  ADD CONSTRAINT salas_ibfk_1 FOREIGN KEY (Bloco_sala) REFERENCES blocos (cod_bloco) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT salas_ibfk_2 FOREIGN KEY (Piso_sala) REFERENCES pisos (Cod_piso) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

ALTER TABLE ocorrencias MODIFY estado ENUM('ABERTO', 'EM CURSO', 'RESOLVIDO', 'ACEITO', 'RECUSADO');
