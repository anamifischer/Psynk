USE `mydb`;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `Notificacao`;
TRUNCATE TABLE `Pagamento`;
TRUNCATE TABLE `AnotacaoClinica`;
TRUNCATE TABLE `Consulta`;
TRUNCATE TABLE `Paciente`;
TRUNCATE TABLE `Usuario`;
TRUNCATE TABLE `Clinica`;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------- CLÍNICAS ----------
INSERT INTO `Clinica` (`idtable1`, `nome`, `CNPJ`, `Endereco`, `telefone`, `logo`) VALUES
(1, 'Clínica Mente Viva',  '11.222.333/0001-81', 'Rua das Acácias, 120 - Centro, Rolante/RS', '(51) 3503-1000', NULL),
(2, 'Espaço Equilíbrio',   '44.555.666/0001-92', 'Av. Brasil, 845 - Sala 3, Taquara/RS',      '(51) 3542-2000', NULL);

-- ---------- USUÁRIOS ----------
-- Clínica 1: 1 admin, 2 psicólogos, 1 recepcionista | Clínica 2: 1 admin, 1 psicóloga, 1 recepcionista
INSERT INTO `Usuario` (`idUsuario`, `nome`, `email`, `senha`, `telefone`, `foto`, `permissao`, `id_clinica`) VALUES
(1, 'Ana Souza',          'ana.souza@menteviva.com',        '$2b$10$Q2YYwt4OOmNGYAnOWYWYNuy2Un3Ps/jbLoF0lu0XewTR6MFdxPuHq', '(51) 99111-0001', NULL, 'ADMIN',         1),
(2, 'Carlos Mendes',      'carlos.mendes@menteviva.com',    '$2b$10$Q2YYwt4OOmNGYAnOWYWYNuy2Un3Ps/jbLoF0lu0XewTR6MFdxPuHq', '(51) 99111-0002', NULL, 'PSICOLOGO',     1),
(3, 'Beatriz Lima',       'beatriz.lima@menteviva.com',     '$2b$10$Q2YYwt4OOmNGYAnOWYWYNuy2Un3Ps/jbLoF0lu0XewTR6MFdxPuHq', '(51) 99111-0003', NULL, 'PSICOLOGO',     1),
(4, 'Fernanda Rocha',     'fernanda.rocha@menteviva.com',   '$2b$10$Q2YYwt4OOmNGYAnOWYWYNuy2Un3Ps/jbLoF0lu0XewTR6MFdxPuHq', '(51) 99111-0004', NULL, 'RECEPCIONISTA', 1),
(5, 'Roberto Alves',      'roberto.alves@equilibrio.com',   '$2b$10$Q2YYwt4OOmNGYAnOWYWYNuy2Un3Ps/jbLoF0lu0XewTR6MFdxPuHq', '(51) 99222-0005', NULL, 'ADMIN',         2),
(6, 'Juliana Costa',      'juliana.costa@equilibrio.com',   '$2b$10$Q2YYwt4OOmNGYAnOWYWYNuy2Un3Ps/jbLoF0lu0XewTR6MFdxPuHq', '(51) 99222-0006', NULL, 'PSICOLOGO',     2),
(7, 'Marcos Pereira',     'marcos.pereira@equilibrio.com',  '$2b$10$Q2YYwt4OOmNGYAnOWYWYNuy2Un3Ps/jbLoF0lu0XewTR6MFdxPuHq', '(51) 99222-0007', NULL, 'RECEPCIONISTA', 2);

-- ---------- PACIENTES ----------
-- status: 1 = ativo, 0 = inativo | cada paciente é atendido por um psicólogo da MESMA clínica
INSERT INTO `Paciente` (`idpaciente`, `nome`, `cpf`, `telefone`, `endereco`, `status`, `idClinica`, `idPsicologo`) VALUES
(1, 'Lucas Ferreira',  '111.444.777-35', '(51) 98001-1111', 'Rua XV de Novembro, 45 - Rolante',   1, 1, 2),
(2, 'Mariana Duarte',  '222.555.888-46', '(51) 98002-2222', 'Rua Bento Gonçalves, 310 - Rolante', 1, 1, 2),
(3, 'Pedro Gomes',     '333.666.999-57', '(51) 98003-3333', 'Av. Getúlio Vargas, 77 - Rolante',   1, 1, 3),
(4, 'Camila Torres',   '444.777.000-68', '(51) 98004-4444', 'Rua Tiradentes, 520 - Rolante',      0, 1, 3),
(5, 'Rafael Martins',  '555.888.111-79', '(51) 98005-5555', 'Rua Júlio de Castilhos, 15 - Taquara', 1, 2, 6),
(6, 'Isabela Nunes',   '666.999.222-80', '(51) 98006-6666', 'Rua Marechal Floriano, 98 - Taquara',  1, 2, 6);

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
(8,  '2026-10-08', '10:00:00', '10:50:00', '2026-10-01 15:30:00', 'concluida',    3, 3, 1),
(9,  '2026-10-15', '10:00:00', '10:50:00', '2026-10-08 10:55:00', 'concluida',    3, 3, 1),
(10, '2026-10-22', '16:00:00', '16:50:00', '2026-10-15 09:10:00', 'atrasada',     3, 4, 1),
(11, '2026-10-29', '16:00:00', '16:50:00', '2026-10-22 17:00:00', 'aguarda data', 3, 4, 1),
(12, '2026-10-09', '15:00:00', '15:50:00', '2026-10-02 13:45:00', 'concluida',    6, 5, 2),
(13, '2026-10-16', '15:00:00', '15:50:00', '2026-10-09 15:55:00', 'concluida',    6, 5, 2),
(14, '2026-10-30', '15:00:00', '15:50:00', '2026-10-23 08:20:00', 'aguarda data', 6, 6, 2),
(15, '2026-10-13', '11:00:00', '11:50:00', '2026-10-06 16:00:00', 'atrasada',     6, 6, 2);

-- ---------- ANOTAÇÕES CLÍNICAS ----------
-- Somente para consultas concluídas; o autor é o psicólogo da consulta
INSERT INTO `AnotacaoClinica` (`idAnotacaoClinica`, `data`, `conteudo`, `criadoEm`, `Consulta_idconsulta`, `Usuario_idUsuario`, `Paciente_idpaciente`) VALUES
(1, '2026-10-05', 'Primeira sessão. Paciente relata ansiedade no trabalho e dificuldade para dormir. Definidos objetivos iniciais do acompanhamento.', '2026-10-05', 1, 2, 1),
(2, '2026-10-12', 'Paciente apresentou melhora no sono após técnicas de respiração. Trabalhadas crenças sobre desempenho profissional.', '2026-10-12', 2, 2, 1),
(3, '2026-10-19', 'Discutidas situações de conflito com a chefia. Paciente aplicou o registro de pensamentos e trouxe exemplos.', '2026-10-19', 3, 2, 1),
(4, '2026-10-07', 'Avaliação inicial. Queixa de tristeza persistente após mudança de cidade. Combinada frequência semanal.', '2026-10-07', 5, 2, 2),
(5, '2026-10-08', 'Paciente relata dificuldades de relacionamento familiar. Explorada história de vida e rede de apoio.', '2026-10-08', 8, 3, 3),
(6, '2026-10-15', 'Evolução positiva na comunicação com os familiares. Proposta tarefa de casa sobre assertividade.', '2026-10-15', 9, 3, 3),
(7, '2026-10-09', 'Sessão inicial. Paciente busca apoio para lidar com estresse no ambiente acadêmico. Plano terapêutico traçado.', '2026-10-09', 12, 6, 5),
(8, '2026-10-16', 'Paciente mais engajado. Trabalhada organização de rotina e manejo de prazos. Relata menos episódios de irritação.', '2026-10-16', 13, 6, 5);

-- ---------- PAGAMENTOS ----------
-- Um pagamento por consulta concluída; registrado pela recepcionista da clínica (4 = clínica 1, 7 = clínica 2)
-- Nota: o paciente de cada pagamento é o mesmo da consulta correspondente
INSERT INTO `Pagamento` (`idPagamento`, `status`, `valor`, `data_vencimento`, `data_pagamento`, `Paciente_idpaciente`, `Consulta_idconsulta`, `Usuario_idUsuario`) VALUES
(1, 'pago',     150.00, '2026-10-12', '2026-10-05', 1, 1,  4),
(2, 'pago',     150.00, '2026-10-19', '2026-10-12', 1, 2,  4),
(3, 'pendente', 150.00, '2026-10-26', NULL,         1, 3,  4),
(4, 'atrasado', 150.00, '2026-10-14', NULL,         2, 5,  4),
(5, 'pago',     180.00, '2026-10-15', '2026-10-08', 3, 8,  4),
(6, 'atrasado', 180.00, '2026-10-22', NULL,         3, 9,  4),
(7, 'pago',     200.00, '2026-10-16', '2026-10-09', 5, 12, 7),
(8, 'pendente', 200.00, '2026-10-23', NULL,         5, 13, 7);

-- ---------- NOTIFICAÇÕES ----------
INSERT INTO `Notificacao` (`idNotificacao`, `mensagem`, `criado_em`, `Usuario_idUsuario`) VALUES
(1,  'Consulta de Lucas Ferreira agendada para 26/10 às 09:00.',            '2026-10-19', 2),
(2,  'Consulta de Mariana Duarte agendada para 28/10 às 14:00.',            '2026-10-21', 2),
(3,  'A consulta de Mariana Duarte em 14/10 está atrasada. Atualize o status.', '2026-10-15', 2),
(4,  'A consulta de Camila Torres em 22/10 está atrasada. Atualize o status.', '2026-10-23', 3),
(5,  'Consulta de Camila Torres agendada para 29/10 às 16:00.',             '2026-10-22', 3),
(6,  'Pagamento de Mariana Duarte (R$ 150,00) está atrasado desde 14/10.',  '2026-10-15', 4),
(7,  'Pagamento de Pedro Gomes (R$ 180,00) está atrasado desde 22/10.',     '2026-10-23', 4),
(8,  'Pagamento de Lucas Ferreira (R$ 150,00) vence em 26/10.',             '2026-10-23', 4),
(9,  'Resumo semanal: 3 pagamentos pendentes ou atrasados na clínica.',     '2026-10-23', 1),
(10, 'Consulta de Isabela Nunes agendada para 30/10 às 15:00.',             '2026-10-23', 6),
(11, 'A consulta de Isabela Nunes em 13/10 está atrasada. Atualize o status.', '2026-10-14', 6),
(12, 'Pagamento de Rafael Martins (R$ 200,00) vence hoje.',                 '2026-10-23', 7),
(13, 'Resumo semanal: 1 consulta atrasada e 1 pagamento vencendo hoje.',    '2026-10-23', 5);