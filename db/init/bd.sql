SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8mb4;
USE `mydb`;

-- -----------------------------------------------------
-- Table `mydb`.`Clinica`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Clinica` (
  `idClinica`  INT            NOT NULL AUTO_INCREMENT,
  `nome`       VARCHAR(100)   NOT NULL,
  `CNPJ`       VARCHAR(18)    NULL,
  `endereco`   VARCHAR(100)   NULL,
  `telefone`   VARCHAR(20)    NULL,
  `logo`       LONGBLOB       NULL,
  `criado_em`  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idClinica`),
  UNIQUE INDEX `uq_Clinica_CNPJ` (`CNPJ` ASC)
) ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Usuario` (
  `idUsuario`     INT          NOT NULL AUTO_INCREMENT,
  `nome`          VARCHAR(100) NOT NULL,
  `email`         VARCHAR(100) NOT NULL,
  `senha`         VARCHAR(255) NOT NULL,
  `telefone`      VARCHAR(20)  NULL,
  `foto`          LONGBLOB     NULL,
  `permissao`     ENUM('ADMIN', 'PSICOLOGO', 'RECEPCIONISTA') NOT NULL,
  `id_clinica`    INT          NOT NULL,
  `criado_em`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idUsuario`),
  UNIQUE INDEX `uq_Usuario_email` (`email` ASC),
  INDEX `fk_Usuario_Clinica1_idx` (`id_clinica` ASC),
  CONSTRAINT `fk_Usuario_Clinica1`
    FOREIGN KEY (`id_clinica`)
    REFERENCES `mydb`.`Clinica` (`idClinica`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Paciente`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Paciente` (
  `idPaciente`    INT          NOT NULL AUTO_INCREMENT,
  `nome`          VARCHAR(100) NOT NULL,
  `cpf`           VARCHAR(14)  NOT NULL,
  `telefone`      VARCHAR(20)  NOT NULL,
  `endereco`      VARCHAR(100) NULL,
  `status`        ENUM('ativo', 'inativo', 'arquivado') NOT NULL DEFAULT 'ativo',
  `idClinica`     INT          NOT NULL,
  `idPsicologo`   INT          NOT NULL,
  `criado_em`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idPaciente`),
  UNIQUE INDEX `uq_Paciente_cpf` (`cpf` ASC),
  INDEX `fk_Paciente_Clinica1_idx` (`idClinica` ASC),
  INDEX `fk_Paciente_Usuario1_idx` (`idPsicologo` ASC),
  CONSTRAINT `fk_Paciente_Clinica1`
    FOREIGN KEY (`idClinica`)
    REFERENCES `mydb`.`Clinica` (`idClinica`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Paciente_Usuario1`
    FOREIGN KEY (`idPsicologo`)
    REFERENCES `mydb`.`Usuario` (`idUsuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Consulta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Consulta` (
  `idConsulta`      INT          NOT NULL AUTO_INCREMENT,
  `data`            DATE         NOT NULL,
  `hora_inicio`     TIME         NOT NULL,
  `hora_fim`        TIME         NOT NULL,
  `status`          ENUM('aguarda data', 'atrasada', 'concluida') NOT NULL DEFAULT 'aguarda data',
  `id_psicologo`    INT          NOT NULL,
  `id_paciente`     INT          NOT NULL,
  `id_clinica`      INT          NOT NULL,
  `criado_em`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idConsulta`),
  INDEX `fk_Consulta_Usuario1_idx`  (`id_psicologo` ASC),
  INDEX `fk_Consulta_Paciente1_idx` (`id_paciente`  ASC),
  INDEX `fk_Consulta_Clinica1_idx`  (`id_clinica`   ASC),
  CONSTRAINT `fk_Consulta_Usuario1`
    FOREIGN KEY (`id_psicologo`)
    REFERENCES `mydb`.`Usuario` (`idUsuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Consulta_Paciente1`
    FOREIGN KEY (`id_paciente`)
    REFERENCES `mydb`.`Paciente` (`idPaciente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Consulta_Clinica1`
    FOREIGN KEY (`id_clinica`)
    REFERENCES `mydb`.`Clinica` (`idClinica`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`AnotacaoClinica`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`AnotacaoClinica` (
  `idAnotacaoClinica` INT      NOT NULL AUTO_INCREMENT,
  `data`              DATE     NOT NULL,
  `conteudo`          TEXT     NOT NULL,
  `id_consulta`       INT      NOT NULL,
  `id_usuario`        INT      NOT NULL,
  `id_paciente`       INT      NOT NULL,
  `criado_em`         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idAnotacaoClinica`),
  INDEX `fk_AnotacaoClinica_Consulta1_idx` (`id_consulta` ASC),
  INDEX `fk_AnotacaoClinica_Usuario1_idx`  (`id_usuario`  ASC),
  INDEX `fk_AnotacaoClinica_Paciente1_idx` (`id_paciente` ASC),
  CONSTRAINT `fk_AnotacaoClinica_Consulta1`
    FOREIGN KEY (`id_consulta`)
    REFERENCES `mydb`.`Consulta` (`idConsulta`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_AnotacaoClinica_Usuario1`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `mydb`.`Usuario` (`idUsuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_AnotacaoClinica_Paciente1`
    FOREIGN KEY (`id_paciente`)
    REFERENCES `mydb`.`Paciente` (`idPaciente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Pagamento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Pagamento` (
  `idPagamento`     INT              NOT NULL AUTO_INCREMENT,
  `status`          ENUM('pago', 'pendente', 'atrasado') NOT NULL DEFAULT 'pendente',
  `valor`           DECIMAL(10, 2)   NOT NULL,
  `data_vencimento` DATE             NOT NULL,
  `data_pagamento`  DATE             NULL,
  `id_paciente`     INT              NOT NULL,
  `id_consulta`     INT              NOT NULL,
  `id_usuario`      INT              NOT NULL,
  `criado_em`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em`   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idPagamento`),
  INDEX `fk_Pagamento_Paciente1_idx` (`id_paciente` ASC),
  INDEX `fk_Pagamento_Consulta1_idx` (`id_consulta` ASC),
  INDEX `fk_Pagamento_Usuario1_idx`  (`id_usuario`  ASC),
  CONSTRAINT `fk_Pagamento_Paciente1`
    FOREIGN KEY (`id_paciente`)
    REFERENCES `mydb`.`Paciente` (`idPaciente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Pagamento_Consulta1`
    FOREIGN KEY (`id_consulta`)
    REFERENCES `mydb`.`Consulta` (`idConsulta`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Pagamento_Usuario1`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `mydb`.`Usuario` (`idUsuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Notificacao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Notificacao` (
  `idNotificacao` INT          NOT NULL AUTO_INCREMENT,
  `mensagem`      VARCHAR(255) NOT NULL,
  `lida`          TINYINT(1)   NOT NULL DEFAULT 0,
  `id_usuario`    INT          NOT NULL,
  `criado_em`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idNotificacao`),
  INDEX `fk_Notificacao_Usuario1_idx` (`id_usuario` ASC),
  CONSTRAINT `fk_Notificacao_Usuario1`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `mydb`.`Usuario` (`idUsuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
