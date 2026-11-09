
SET NAMES utf8mb4;
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

CREATE DATABASE IF NOT EXISTS `psynk` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `psynk`;

-- ---------------------------------------------------------------------
-- Clinica
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Clinica` (
  `idtable1` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NOT NULL,
  `CNPJ` VARCHAR(18) NULL,
  `Endereco` VARCHAR(60) NULL,
  `telefone` VARCHAR(45) NULL,
  `logo` BLOB NULL,
  PRIMARY KEY (`idtable1`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Usuario
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Usuario` (
  `idUsuario` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `senha` VARCHAR(255) NOT NULL,
  `telefone` VARCHAR(45) NULL,
  `foto` BLOB NULL,
  `permissao` ENUM('ADMIN', 'PSICOLOGO', 'RECEPCIONISTA') NOT NULL,
  `id_clinica` INT NOT NULL,
  PRIMARY KEY (`idUsuario`),
  INDEX `fk_Usuario_Clinica1_idx` (`id_clinica`),
  CONSTRAINT `fk_Usuario_Clinica1`
    FOREIGN KEY (`id_clinica`) REFERENCES `Clinica` (`idtable1`)
    ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Paciente  (já com o status final em ENUM, incorporando o ALTER)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Paciente` (
  `idpaciente` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NOT NULL,
  `cpf` VARCHAR(14) NOT NULL,
  `telefone` VARCHAR(20) NOT NULL,
  `endereco` VARCHAR(60) NULL,
  `status` ENUM('ativo','em_espera','pausado','encerrado','inativo') NOT NULL DEFAULT 'ativo',
  `idClinica` INT NOT NULL,
  `idPsicologo` INT NOT NULL,
  PRIMARY KEY (`idpaciente`),
  INDEX `fk_paciente_Clinica1_idx` (`idClinica`),
  INDEX `fk_paciente_Usuario1_idx` (`idPsicologo`),
  CONSTRAINT `fk_paciente_Clinica1`
    FOREIGN KEY (`idClinica`) REFERENCES `Clinica` (`idtable1`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_paciente_Usuario1`
    FOREIGN KEY (`idPsicologo`) REFERENCES `Usuario` (`idUsuario`)
    ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Consulta
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Consulta` (
  `idconsulta` INT NOT NULL AUTO_INCREMENT,
  `data` DATE NOT NULL,
  `hora_inicio` TIME NOT NULL,
  `hora_fim` TIME NOT NULL,
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('aguarda data', 'atrasada', 'concluida') NOT NULL,
  `id_psicologo` INT NOT NULL,
  `id_paciente` INT NOT NULL,
  `Clinica_idtable1` INT NOT NULL,
  PRIMARY KEY (`idconsulta`),
  INDEX `fk_consulta_Usuario1_idx` (`id_psicologo`),
  INDEX `fk_consulta_paciente1_idx` (`id_paciente`),
  INDEX `fk_consulta_Clinica1_idx` (`Clinica_idtable1`),
  CONSTRAINT `fk_consulta_Usuario1`
    FOREIGN KEY (`id_psicologo`) REFERENCES `Usuario` (`idUsuario`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_consulta_paciente1`
    FOREIGN KEY (`id_paciente`) REFERENCES `Paciente` (`idpaciente`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_consulta_Clinica1`
    FOREIGN KEY (`Clinica_idtable1`) REFERENCES `Clinica` (`idtable1`)
    ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- AnotacaoClinica
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `AnotacaoClinica` (
  `idAnotacaoClinica` INT NOT NULL AUTO_INCREMENT,
  `data` DATE NOT NULL,
  `conteudo` VARCHAR(500) NOT NULL,
  `criadoEm` DATE NOT NULL,
  `Consulta_idconsulta` INT NOT NULL,
  `Usuario_idUsuario` INT NOT NULL,
  `Paciente_idpaciente` INT NOT NULL,
  PRIMARY KEY (`idAnotacaoClinica`),
  INDEX `fk_AnotacaoClinica_Consulta1_idx` (`Consulta_idconsulta`),
  INDEX `fk_AnotacaoClinica_Usuario1_idx` (`Usuario_idUsuario`),
  INDEX `fk_AnotacaoClinica_Paciente1_idx` (`Paciente_idpaciente`),
  CONSTRAINT `fk_AnotacaoClinica_Consulta1`
    FOREIGN KEY (`Consulta_idconsulta`) REFERENCES `Consulta` (`idconsulta`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_AnotacaoClinica_Usuario1`
    FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `Usuario` (`idUsuario`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_AnotacaoClinica_Paciente1`
    FOREIGN KEY (`Paciente_idpaciente`) REFERENCES `Paciente` (`idpaciente`)
    ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Pagamento
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Pagamento` (
  `idPagamento` INT NOT NULL AUTO_INCREMENT,
  `status` ENUM('pago', 'pendente', 'atrasado') NOT NULL,
  `valor` DECIMAL(10,2) NOT NULL,
  `data_vencimento` DATE NOT NULL,
  `data_pagamento` DATE NULL,
  `Paciente_idpaciente` INT NOT NULL,
  `Consulta_idconsulta` INT NOT NULL,
  `Usuario_idUsuario` INT NOT NULL,
  PRIMARY KEY (`idPagamento`),
  INDEX `fk_Pagamento_Paciente1_idx` (`Paciente_idpaciente`),
  INDEX `fk_Pagamento_Consulta1_idx` (`Consulta_idconsulta`),
  INDEX `fk_Pagamento_Usuario1_idx` (`Usuario_idUsuario`),
  CONSTRAINT `fk_Pagamento_Paciente1`
    FOREIGN KEY (`Paciente_idpaciente`) REFERENCES `Paciente` (`idpaciente`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Pagamento_Consulta1`
    FOREIGN KEY (`Consulta_idconsulta`) REFERENCES `Consulta` (`idconsulta`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Pagamento_Usuario1`
    FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `Usuario` (`idUsuario`)
    ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Notificacao
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Notificacao` (
  `idNotificacao` INT NOT NULL AUTO_INCREMENT,
  `mensagem` VARCHAR(100) NOT NULL,
  `criado_em` DATE NOT NULL,
  `Usuario_idUsuario` INT NOT NULL,
  PRIMARY KEY (`idNotificacao`),
  INDEX `fk_Notificacao_Usuario1_idx` (`Usuario_idUsuario`),
  CONSTRAINT `fk_Notificacao_Usuario1`
    FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `Usuario` (`idUsuario`)
    ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- =====================================================================
-- DADOS INICIAIS (todos fictícios)
-- =====================================================================

-- Clinica ------------------------------------------------------------
INSERT INTO `Clinica` (`idtable1`, `nome`, `CNPJ`, `Endereco`, `telefone`) VALUES
  (1, 'Clínica Psynk', '00.000.000/0001-00', 'Rua das Flores, 100 - Viamão', '(51) 99999-0000');

-- Usuario ------------------------------------------------------------
-- Senhas de teste (bcrypt, compatível com password_verify):
--   admin@psynk.com          -> admin123
--   psicologa@psynk.com      -> psico123
--   carlos@psynk.com         -> psico123
--   recepcionista@psynk.com  -> recep123
-- TROQUE antes de usar em produção.
INSERT INTO `Usuario` (`idUsuario`, `nome`, `email`, `senha`, `telefone`, `permissao`, `id_clinica`) VALUES
  (1, 'Administrador Psynk', 'admin@psynk.com',
      '$2y$10$Ggh7/dkGAGSj8c1DuCHhkedPb4UEJWegIEgawAFScV5vn1XrqKK9G', '(51) 98888-0001', 'ADMIN', 1),
  (2, 'Maria Psicóloga', 'psicologa@psynk.com',
      '$2y$10$t9GwKJK6YcPQcYgA5hpO3.YOWdHTLZmX6JtZPVzRnr7DI/AIJXEfW', '(51) 98888-0002', 'PSICOLOGO', 1),
  (3, 'João Recepcionista', 'recepcionista@psynk.com',
      '$2y$10$UxR/302RtMDX4.TA6eTNHupIy6CPU5HVnC2GTO3VA4L8vB3CItLa2', '(51) 98888-0003', 'RECEPCIONISTA', 1),
  (4, 'Carlos Psicólogo', 'carlos@psynk.com',
      '$2y$10$t9GwKJK6YcPQcYgA5hpO3.YOWdHTLZmX6JtZPVzRnr7DI/AIJXEfW', '(51) 98888-0004', 'PSICOLOGO', 1);

-- Paciente -----------------------------------------------------------
INSERT INTO `Paciente` (`idpaciente`, `nome`, `cpf`, `telefone`, `endereco`, `status`, `idClinica`, `idPsicologo`) VALUES
  (1, 'Ana Souza',      '111.444.777-35', '(51) 97777-0001', 'Rua A, 10',  'ativo',     1, 2),
  (2, 'Bruno Lima',     '222.333.444-05', '(51) 97777-0002', 'Rua B, 20',  'ativo',     1, 2),
  (3, 'Carla Mendes',   '333.222.111-00', '(51) 97777-0003', 'Rua C, 30',  'em_espera', 1, 4),
  (4, 'Diego Rocha',    '444.555.666-77', '(51) 97777-0004', 'Rua D, 40',  'pausado',   1, 4),
  (5, 'Elisa Ferreira', '555.666.777-88', '(51) 97777-0005', 'Rua E, 50',  'encerrado', 1, 2),
  (6, 'Felipe Santos',  '666.777.888-99', '(51) 97777-0006', NULL,         'inativo',   1, 4),
  (7, 'Lucas Rodrigues', '111.444.666-35', '(51) 97777-0001', 'Rua A, 10',  'ativo',     1, 1);

-- Consulta -----------------------------------------------------------
INSERT INTO `Consulta` (`idconsulta`, `data`, `hora_inicio`, `hora_fim`, `status`, `id_psicologo`, `id_paciente`, `Clinica_idtable1`) VALUES
  (1, '2026-10-20', '09:00:00', '10:00:00', 'concluida',    2, 5, 1),
  (2, '2026-11-03', '14:00:00', '15:00:00', 'concluida',    2, 1, 1),
  (3, '2026-11-04', '10:00:00', '11:00:00', 'concluida',    2, 2, 1),
  (4, '2026-11-05', '09:00:00', '10:00:00', 'concluida',    4, 4, 1),
  (5, '2026-11-06', '16:00:00', '17:00:00', 'atrasada',     2, 1, 1),
  (6, '2026-11-10', '14:00:00', '15:00:00', 'aguarda data', 2, 1, 1),
  (7, '2026-11-11', '10:00:00', '11:00:00', 'aguarda data', 2, 2, 1),
  (8, '2026-11-12', '15:00:00', '16:00:00', 'aguarda data', 4, 3, 1);

-- AnotacaoClinica (apenas das consultas concluídas) ------------------
INSERT INTO `AnotacaoClinica` (`idAnotacaoClinica`, `data`, `conteudo`, `criadoEm`, `Consulta_idconsulta`, `Usuario_idUsuario`, `Paciente_idpaciente`) VALUES
  (1, '2026-10-20', 'Sessão de encerramento. Paciente relata melhora significativa e boa adesão ao plano.', '2026-10-20', 1, 2, 5),
  (2, '2026-11-03', 'Paciente relatou ansiedade no trabalho. Trabalhadas técnicas de respiração.',          '2026-11-03', 2, 2, 1),
  (3, '2026-11-04', 'Primeira sessão de acompanhamento. Definidas metas para as próximas semanas.',         '2026-11-04', 3, 2, 2),
  (4, '2026-11-05', 'Paciente solicitou pausa no tratamento por motivos pessoais.',                         '2026-11-05', 4, 4, 4);

-- Pagamento ----------------------------------------------------------
-- Registrados pela recepcionista (idUsuario = 3)
INSERT INTO `Pagamento` (`idPagamento`, `status`, `valor`, `data_vencimento`, `data_pagamento`, `Paciente_idpaciente`, `Consulta_idconsulta`, `Usuario_idUsuario`) VALUES
  (1, 'pago',     150.00, '2026-10-20', '2026-10-20', 5, 1, 3),
  (2, 'pago',     150.00, '2026-11-03', '2026-11-03', 1, 2, 3),
  (3, 'pago',     150.00, '2026-11-04', '2026-11-04', 2, 3, 3),
  (4, 'pendente', 150.00, '2026-11-20', NULL,         4, 4, 3),
  (5, 'atrasado', 150.00, '2026-11-06', NULL,         1, 5, 3),
  (6, 'pendente', 150.00, '2026-11-10', NULL,         1, 6, 3),
  (7, 'pendente', 150.00, '2026-11-11', NULL,         2, 7, 3);

-- Notificacao --------------------------------------------------------
INSERT INTO `Notificacao` (`idNotificacao`, `mensagem`, `criado_em`, `Usuario_idUsuario`) VALUES
  (1, 'Bem-vindo ao Psynk!',                              '2026-11-09', 1),
  (2, 'Você tem consulta amanhã às 14:00 com Ana Souza.', '2026-11-09', 2),
  (3, 'Pagamento de Ana Souza está atrasado.',            '2026-11-09', 3),
  (4, 'Consulta de Carla Mendes marcada para 12/11.',     '2026-11-09', 4);

  ALTER TABLE Paciente
  ADD COLUMN data_nascimento DATE NULL,
  ADD COLUMN observacoes TEXT NULL;