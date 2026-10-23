SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

CREATE DATABASE IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mydb`;

CREATE TABLE IF NOT EXISTS `Clinica` (
  `idtable1` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NOT NULL,
  `CNPJ` VARCHAR(18) NULL,
  `Endereco` VARCHAR(60) NULL,
  `telefone` VARCHAR(45) NULL,
  `logo` BLOB NULL,
  PRIMARY KEY (`idtable1`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

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
    FOREIGN KEY (`id_clinica`)
    REFERENCES `Clinica` (`idtable1`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `Paciente` (
  `idpaciente` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NOT NULL,
  `cpf` VARCHAR(14) NOT NULL,
  `telefone` VARCHAR(20) NOT NULL,
  `endereco` VARCHAR(60) NULL,
  `status` INT NOT NULL,
  `idClinica` INT NOT NULL,
  `idPsicologo` INT NOT NULL,
  PRIMARY KEY (`idpaciente`),
  INDEX `fk_paciente_Clinica1_idx` (`idClinica`),
  INDEX `fk_paciente_Usuario1_idx` (`idPsicologo`),
  CONSTRAINT `fk_paciente_Clinica1`
    FOREIGN KEY (`idClinica`)
    REFERENCES `Clinica` (`idtable1`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_paciente_Usuario1`
    FOREIGN KEY (`idPsicologo`)
    REFERENCES `Usuario` (`idUsuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

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
    FOREIGN KEY (`id_psicologo`)
    REFERENCES `Usuario` (`idUsuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_consulta_paciente1`
    FOREIGN KEY (`id_paciente`)
    REFERENCES `Paciente` (`idpaciente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_consulta_Clinica1`
    FOREIGN KEY (`Clinica_idtable1`)
    REFERENCES `Clinica` (`idtable1`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

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
    FOREIGN KEY (`Consulta_idconsulta`)
    REFERENCES `Consulta` (`idconsulta`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_AnotacaoClinica_Usuario1`
    FOREIGN KEY (`Usuario_idUsuario`)
    REFERENCES `Usuario` (`idUsuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_AnotacaoClinica_Paciente1`
    FOREIGN KEY (`Paciente_idpaciente`)
    REFERENCES `Paciente` (`idpaciente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

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
    FOREIGN KEY (`Paciente_idpaciente`)
    REFERENCES `Paciente` (`idpaciente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Pagamento_Consulta1`
    FOREIGN KEY (`Consulta_idconsulta`)
    REFERENCES `Consulta` (`idconsulta`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Pagamento_Usuario1`
    FOREIGN KEY (`Usuario_idUsuario`)
    REFERENCES `Usuario` (`idUsuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `Notificacao` (
  `idNotificacao` INT NOT NULL AUTO_INCREMENT,
  `mensagem` VARCHAR(100) NOT NULL,
  `criado_em` DATE NOT NULL,
  `Usuario_idUsuario` INT NOT NULL,
  PRIMARY KEY (`idNotificacao`),
  INDEX `fk_Notificacao_Usuario1_idx` (`Usuario_idUsuario`),
  CONSTRAINT `fk_Notificacao_Usuario1`
    FOREIGN KEY (`Usuario_idUsuario`)
    REFERENCES `Usuario` (`idUsuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;