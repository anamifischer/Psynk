USE `psynk`;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `Notificacao`;
TRUNCATE TABLE `Pagamento`;
TRUNCATE TABLE `AnotacaoClinica`;
TRUNCATE TABLE `Consulta`;
TRUNCATE TABLE `Paciente`;
TRUNCATE TABLE `Usuario`;
TRUNCATE TABLE `Clinica`;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------- CLÍNICA ----------
INSERT INTO `Clinica` (`idtable1`, `nome`, `CNPJ`, `Endereco`, `telefone`, `logo`) VALUES
(1, 'Clínica Mente Viva', '11.222.333/0001-81', 'Rua das Acácias, 120 - Centro, Rolante/RS', '(51) 3503-1000', NULL);

-- ---------- USUÁRIOS ----------
-- Clínica 1: 1 admin, 1 psicóloga, 1 recepcionista
INSERT INTO `Usuario` (`idUsuario`, `nome`, `email`, `senha`, `telefone`, `foto`, `permissao`, `id_clinica`) VALUES
(1, 'Ana Admin',          'ana.admin@psynk.com',          '$2b$10$Q2YYwt4OOmNGYAnOWYWYNuy2Un3Ps/jbLoF0lu0XewTR6MFdxPuHq', '(51) 99111-0001', NULL, 'ADMIN',         1),
(2, 'Ana Psicologa',      'ana.psicologa@psynk.com',      '$2b$10$Q2YYwt4OOmNGYAnOWYWYNuy2Un3Ps/jbLoF0lu0XewTR6MFdxPuHq', '(51) 99111-0002', NULL, 'PSICOLOGO',     1),
(3, 'Ana Recepcionista',  'ana.recepcionista@psynk.com',  '$2b$10$Q2YYwt4OOmNGYAnOWYWYNuy2Un3Ps/jbLoF0lu0XewTR6MFdxPuHq', '(51) 99111-0004', NULL, 'RECEPCIONISTA', 1);

-- ---------- PACIENTES ----------
-- status: 1 = ativo, 0 = inativo | todos atendidos pela psicóloga (id 2)
INSERT INTO `Paciente` (`idpaciente`, `nome`, `cpf`, `telefone`, `endereco`, `status`, `idClinica`, `idPsicologo`) VALUES
(1, 'Lucas Ferreira',  '111.444.777-35', '(51) 98001-1111', 'Rua XV de Novembro, 45 - Rolante',   1, 1, 2),
(2, 'Mariana Duarte',  '222.555.888-46', '(51) 98002-2222', 'Rua Bento Gonçalves, 310 - Rolante', 1, 1, 2),
(3, 'Pedro Gomes',     '333.666.999-57', '(51) 98003-3333', 'Av. Getúlio Vargas, 77 - Rolante',   1, 1, 2),
(4, 'Camila Torres',   '444.777.000-68', '(51) 98004-4444', 'Rua Tiradentes, 520 - Rolante',      0, 1, 2);

-- ---------- CONSULTAS ----------
-- Hoje (referência): 2026-10-23
-- concluida = já realizada | atrasada = passou da data e não foi concluída | aguarda data = agendada
INSERT INTO `Consulta` (`idconsulta`, `data`, `hora_inicio`, `hora_fim`, `criado_em`, `status`, `id_psicologo`, `id_paciente`, `Clinica_idtable1`) VALUES
(1,  '2026-10-05', '09:00:00', '09:50:00', '2026-09-28 10:15:00', 'concluida',    2, 1, 1),
(2,  '2026-10-12', '09:00:00', '09:50:00', '2026-10-05 09:55:00', 'concluida',    2, 1, 1),
(3,  '2026-10-19', '09:00:00', '09:50:00', '2026-10-12 09:58:00', 'concluida',    2, 1, 1),
(4,  '2026-10-26', '09:00:00', '09:50:00', '2026-10-19 09:55:00', 'aguarda data', 2, 1, 1),
(5,  '2026-10-07', '14:00:00', '14:50:00', '2026-09-30 11:20:00', 'concluida',    2, 2, 1),
(6,  '2026-10-14', '14:00:00', '14:50:00', '2026-10-07 14:55:00', 'atrasada',     2, 2, 1),
(7,  '2026-10-28', '14:00:00', '14:50:00', '2026-10-21 08:40:00', 'aguarda data', 2, 2, 1),
(8,  '2026-10-08', '10:00:00', '10:50:00', '2026-10-01 15:30:00', 'concluida',    2, 3, 1),
(9,  '2026-10-15', '10:00:00', '10:50:00', '2026-10-08 10:55:00', 'concluida',    2, 3, 1),
(10, '2026-10-22', '16:00:00', '16:50:00', '2026-10-15 09:10:00', 'atrasada',     2, 4, 1),
(11, '2026-10-29', '16:00:00', '16:50:00', '2026-10-22 17:00:00', 'aguarda data', 2, 4, 1);

-- ---------- ANOTAÇÕES CLÍNICAS ----------
-- Somente para consultas concluídas; o autor é a psicóloga (id 2)
INSERT INTO `AnotacaoClinica` (`idAnotacaoClinica`, `data`, `conteudo`, `criadoEm`, `Consulta_idconsulta`, `Usuario_idUsuario`, `Paciente_idpaciente`) VALUES
(1, '2026-10-05', 'Primeira sessão. Paciente relata ansiedade no trabalho e dificuldade para dormir. Definidos objetivos iniciais do acompanhamento.', '2026-10-05', 1, 2, 1),
(2, '2026-10-12', 'Paciente apresentou melhora no sono após técnicas de respiração. Trabalhadas crenças sobre desempenho profissional.', '2026-10-12', 2, 2, 1),
(3, '2026-10-19', 'Discutidas situações de conflito com a chefia. Paciente aplicou o registro de pensamentos e trouxe exemplos.', '2026-10-19', 3, 2, 1),
(4, '2026-10-07', 'Avaliação inicial. Queixa de tristeza persistente após mudança de cidade. Combinada frequência semanal.', '2026-10-07', 5, 2, 2),
(5, '2026-10-08', 'Paciente relata dificuldades de relacionamento familiar. Explorada história de vida e rede de apoio.', '2026-10-08', 8, 2, 3),
(6, '2026-10-15', 'Evolução positiva na comunicação com os familiares. Proposta tarefa de casa sobre assertividade.', '2026-10-15', 9, 2, 3);

-- ---------- PAGAMENTOS ----------
-- Um pagamento por consulta concluída; registrado pela recepcionista (id 3)
INSERT INTO `Pagamento` (`idPagamento`, `status`, `valor`, `data_vencimento`, `data_pagamento`, `Paciente_idpaciente`, `Consulta_idconsulta`, `Usuario_idUsuario`) VALUES
(1, 'pago',     150.00, '2026-10-12', '2026-10-05', 1, 1, 3),
(2, 'pago',     150.00, '2026-10-19', '2026-10-12', 1, 2, 3),
(3, 'pendente', 150.00, '2026-10-26', NULL,         1, 3, 3),
(4, 'atrasado', 150.00, '2026-10-14', NULL,         2, 5, 3),
(5, 'pago',     180.00, '2026-10-15', '2026-10-08', 3, 8, 3),
(6, 'atrasado', 180.00, '2026-10-22', NULL,         3, 9, 3);

-- ---------- NOTIFICAÇÕES ----------
-- 2 = psicóloga | 3 = recepcionista | 1 = admin
INSERT INTO `Notificacao` (`idNotificacao`, `mensagem`, `criado_em`, `Usuario_idUsuario`) VALUES
(1, 'Consulta de Lucas Ferreira agendada para 26/10 às 09:00.',                 '2026-10-19', 2),
(2, 'Consulta de Mariana Duarte agendada para 28/10 às 14:00.',                 '2026-10-21', 2),
(3, 'A consulta de Mariana Duarte em 14/10 está atrasada. Atualize o status.',  '2026-10-15', 2),
(4, 'A consulta de Camila Torres em 22/10 está atrasada. Atualize o status.',   '2026-10-23', 2),
(5, 'Consulta de Camila Torres agendada para 29/10 às 16:00.',                  '2026-10-22', 2),
(6, 'Pagamento de Mariana Duarte (R$ 150,00) está atrasado desde 14/10.',       '2026-10-15', 3),
(7, 'Pagamento de Pedro Gomes (R$ 180,00) está atrasado desde 22/10.',          '2026-10-23', 3),
(8, 'Pagamento de Lucas Ferreira (R$ 150,00) vence em 26/10.',                  '2026-10-23', 3),
(9, 'Resumo semanal: 3 pagamentos pendentes ou atrasados na clínica.',          '2026-10-23', 1);