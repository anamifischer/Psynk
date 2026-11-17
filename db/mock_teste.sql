-- ============================================================
-- Dados de teste do Psynk (consultas, pacientes, pagamentos)
-- Pacientes 101-108 e consultas 1001-1028.
-- Pode rodar de novo: apaga os dados de teste antes de inserir.
-- Todas as datas são relativas a @hoje. Ajuste se rodar em outro dia.
-- ============================================================
SET NAMES utf8mb4;
SET @hoje = '2026-11-16';

-- limpa dados de teste anteriores
DELETE FROM Pagamento       WHERE Consulta_idconsulta >= 1000;
DELETE FROM AnotacaoClinica WHERE Consulta_idconsulta >= 1000;
DELETE FROM Notificacao     WHERE id_consulta >= 1000 OR id_paciente >= 100;
DELETE FROM Consulta        WHERE idconsulta >= 1000;
DELETE FROM HistoricoResponsavel WHERE Paciente_idpaciente >= 100;
DELETE FROM Paciente        WHERE idpaciente >= 100;

-- pacientes
INSERT INTO Paciente (idpaciente, nome, cpf, telefone, endereco, status, idClinica, idPsicologo, data_nascimento) VALUES
  (101, 'Marina Alves', '529.982.247-25', '(51) 98101-0101', 'Rua das Acácias, 120', 'ativo', 1, 2, DATE_SUB(@hoje, INTERVAL 25 YEAR)),
  (102, 'Rafael Costa', '390.533.447-05', '(51) 98102-0102', 'Av. Brasil, 455', 'ativo', 1, 2, DATE_SUB(@hoje, INTERVAL 28 YEAR)),
  (103, 'Beatriz Nunes', '123.456.789-09', '(51) 98103-0103', 'Rua do Sol, 78', 'ativo', 1, 4, DATE_SUB(@hoje, INTERVAL 31 YEAR)),
  (104, 'Thiago Martins', '168.995.350-09', '(51) 98104-0104', 'Rua Verde, 301', 'ativo', 1, 4, DATE_SUB(@hoje, INTERVAL 34 YEAR)),
  (105, 'Camila Duarte', '746.971.314-01', '(51) 98105-0105', 'Rua Alegre, 15', 'ativo', 1, 1, DATE_SUB(@hoje, INTERVAL 37 YEAR)),
  (106, 'Henrique Prado', '987.654.321-00', '(51) 98106-0106', NULL, 'em_espera', 1, 2, DATE_SUB(@hoje, INTERVAL 40 YEAR)),
  (107, 'Juliana Ribeiro', '295.379.955-93', '(51) 98107-0107', 'Rua Nova, 9', 'pausado', 1, 4, DATE_SUB(@hoje, INTERVAL 43 YEAR)),
  (108, 'Otávio Lemos', '214.365.870-26', '(51) 98108-0108', 'Travessa Lima, 44', 'ativo', 1, 2, DATE_SUB(@hoje, INTERVAL 46 YEAR));

-- consultas (o trigger já cria o pagamento de cada uma, menos das canceladas)
INSERT INTO Consulta (idconsulta, data, hora_inicio, hora_fim, status, id_psicologo, id_paciente, Clinica_idtable1, confirmada_em, concluida_em) VALUES
  (1001, DATE_ADD(@hoje, INTERVAL -62 DAY), '10:00:00', ADDTIME('10:00:00', '00:50:00'), 'concluida', 2, 108, 1, TIMESTAMP(DATE_ADD(@hoje, INTERVAL -63 DAY), '10:00:00'), TIMESTAMP(DATE_ADD(@hoje, INTERVAL -62 DAY), ADDTIME('10:00:00', '00:50:00'))),
  (1002, DATE_ADD(@hoje, INTERVAL -48 DAY), '10:00:00', ADDTIME('10:00:00', '00:50:00'), 'concluida', 2, 108, 1, TIMESTAMP(DATE_ADD(@hoje, INTERVAL -49 DAY), '10:00:00'), TIMESTAMP(DATE_ADD(@hoje, INTERVAL -48 DAY), ADDTIME('10:00:00', '00:50:00'))),
  (1003, DATE_ADD(@hoje, INTERVAL -41 DAY), '14:00:00', ADDTIME('14:00:00', '00:50:00'), 'concluida', 2, 101, 1, TIMESTAMP(DATE_ADD(@hoje, INTERVAL -42 DAY), '10:00:00'), TIMESTAMP(DATE_ADD(@hoje, INTERVAL -41 DAY), ADDTIME('14:00:00', '00:50:00'))),
  (1004, DATE_ADD(@hoje, INTERVAL -40 DAY), '09:00:00', ADDTIME('09:00:00', '00:50:00'), 'concluida', 4, 103, 1, TIMESTAMP(DATE_ADD(@hoje, INTERVAL -41 DAY), '10:00:00'), TIMESTAMP(DATE_ADD(@hoje, INTERVAL -40 DAY), ADDTIME('09:00:00', '00:50:00'))),
  (1005, DATE_ADD(@hoje, INTERVAL -34 DAY), '14:00:00', ADDTIME('14:00:00', '00:50:00'), 'concluida', 2, 101, 1, TIMESTAMP(DATE_ADD(@hoje, INTERVAL -35 DAY), '10:00:00'), TIMESTAMP(DATE_ADD(@hoje, INTERVAL -34 DAY), ADDTIME('14:00:00', '00:50:00'))),
  (1006, DATE_ADD(@hoje, INTERVAL -27 DAY), '09:00:00', ADDTIME('09:00:00', '00:50:00'), 'concluida', 4, 103, 1, TIMESTAMP(DATE_ADD(@hoje, INTERVAL -28 DAY), '10:00:00'), TIMESTAMP(DATE_ADD(@hoje, INTERVAL -27 DAY), ADDTIME('09:00:00', '00:50:00'))),
  (1008, DATE_ADD(@hoje, INTERVAL -13 DAY), '14:00:00', ADDTIME('14:00:00', '00:50:00'), 'concluida', 2, 101, 1, TIMESTAMP(DATE_ADD(@hoje, INTERVAL -14 DAY), '10:00:00'), TIMESTAMP(DATE_ADD(@hoje, INTERVAL -13 DAY), ADDTIME('14:00:00', '00:50:00'))),
  (1009, DATE_ADD(@hoje, INTERVAL -12 DAY), '10:00:00', ADDTIME('10:00:00', '00:50:00'), 'concluida', 4, 104, 1, TIMESTAMP(DATE_ADD(@hoje, INTERVAL -13 DAY), '10:00:00'), TIMESTAMP(DATE_ADD(@hoje, INTERVAL -12 DAY), ADDTIME('10:00:00', '00:50:00'))),
  (1010, DATE_ADD(@hoje, INTERVAL -11 DAY), '16:00:00', ADDTIME('16:00:00', '00:50:00'), 'concluida', 2, 102, 1, TIMESTAMP(DATE_ADD(@hoje, INTERVAL -12 DAY), '10:00:00'), TIMESTAMP(DATE_ADD(@hoje, INTERVAL -11 DAY), ADDTIME('16:00:00', '00:50:00'))),
  (1011, DATE_ADD(@hoje, INTERVAL -10 DAY), '09:00:00', ADDTIME('09:00:00', '00:50:00'), 'concluida', 4, 103, 1, TIMESTAMP(DATE_ADD(@hoje, INTERVAL -11 DAY), '10:00:00'), TIMESTAMP(DATE_ADD(@hoje, INTERVAL -10 DAY), ADDTIME('09:00:00', '00:50:00'))),
  (1012, DATE_ADD(@hoje, INTERVAL -9 DAY), '11:00:00', ADDTIME('11:00:00', '00:50:00'), 'concluida', 1, 105, 1, TIMESTAMP(DATE_ADD(@hoje, INTERVAL -10 DAY), '10:00:00'), TIMESTAMP(DATE_ADD(@hoje, INTERVAL -9 DAY), ADDTIME('11:00:00', '00:50:00'))),
  (1013, DATE_ADD(@hoje, INTERVAL -6 DAY), '15:00:00', ADDTIME('15:00:00', '00:50:00'), 'concluida', 2, 108, 1, TIMESTAMP(DATE_ADD(@hoje, INTERVAL -7 DAY), '10:00:00'), TIMESTAMP(DATE_ADD(@hoje, INTERVAL -6 DAY), ADDTIME('15:00:00', '00:50:00'))),
  (1014, DATE_ADD(@hoje, INTERVAL -4 DAY), '10:00:00', ADDTIME('10:00:00', '00:50:00'), 'atrasada', 4, 104, 1, TIMESTAMP(DATE_ADD(@hoje, INTERVAL -5 DAY), '10:00:00'), NULL),
  (1015, DATE_ADD(@hoje, INTERVAL -3 DAY), '14:00:00', ADDTIME('14:00:00', '00:50:00'), 'atrasada', 2, 102, 1, TIMESTAMP(DATE_ADD(@hoje, INTERVAL -4 DAY), '10:00:00'), NULL),
  (1016, DATE_ADD(@hoje, INTERVAL -2 DAY), '09:00:00', ADDTIME('09:00:00', '00:50:00'), 'aguarda data', 1, 105, 1, TIMESTAMP(DATE_ADD(@hoje, INTERVAL -3 DAY), '10:00:00'), NULL),
  (1017, DATE_ADD(@hoje, INTERVAL -1 DAY), '10:00:00', ADDTIME('10:00:00', '00:50:00'), 'cancelada', 2, 101, 1, NULL, NULL),
  (1018, DATE_ADD(@hoje, INTERVAL 0 DAY), '08:00:00', ADDTIME('08:00:00', '00:50:00'), 'concluida', 2, 102, 1, TIMESTAMP(DATE_ADD(@hoje, INTERVAL -1 DAY), '10:00:00'), TIMESTAMP(DATE_ADD(@hoje, INTERVAL 0 DAY), ADDTIME('08:00:00', '00:50:00'))),
  (1019, DATE_ADD(@hoje, INTERVAL 0 DAY), '14:00:00', ADDTIME('14:00:00', '00:50:00'), 'aguarda data', 4, 103, 1, TIMESTAMP(DATE_ADD(@hoje, INTERVAL -1 DAY), '10:00:00'), NULL),
  (1020, DATE_ADD(@hoje, INTERVAL 0 DAY), '22:30:00', ADDTIME('22:30:00', '00:50:00'), 'aguarda data', 2, 101, 1, NULL, NULL),
  (1021, DATE_ADD(@hoje, INTERVAL 0 DAY), '23:00:00', ADDTIME('23:00:00', '00:50:00'), 'aguarda data', 4, 104, 1, TIMESTAMP(DATE_ADD(@hoje, INTERVAL -1 DAY), '10:00:00'), NULL),
  (1022, DATE_ADD(@hoje, INTERVAL 1 DAY), '09:00:00', ADDTIME('09:00:00', '00:50:00'), 'aguarda data', 2, 102, 1, TIMESTAMP(DATE_ADD(@hoje, INTERVAL 0 DAY), '10:00:00'), NULL),
  (1023, DATE_ADD(@hoje, INTERVAL 1 DAY), '15:00:00', ADDTIME('15:00:00', '00:50:00'), 'aguarda data', 4, 103, 1, NULL, NULL),
  (1024, DATE_ADD(@hoje, INTERVAL 2 DAY), '10:00:00', ADDTIME('10:00:00', '00:50:00'), 'aguarda data', 2, 108, 1, NULL, NULL),
  (1025, DATE_ADD(@hoje, INTERVAL 3 DAY), '14:00:00', ADDTIME('14:00:00', '00:50:00'), 'aguarda data', 1, 105, 1, TIMESTAMP(DATE_ADD(@hoje, INTERVAL 2 DAY), '10:00:00'), NULL),
  (1026, DATE_ADD(@hoje, INTERVAL 4 DAY), '09:00:00', ADDTIME('09:00:00', '00:50:00'), 'aguarda data', 2, 101, 1, NULL, NULL),
  (1027, DATE_ADD(@hoje, INTERVAL 7 DAY), '10:00:00', ADDTIME('10:00:00', '00:50:00'), 'aguarda data', 4, 104, 1, NULL, NULL),
  (1028, DATE_ADD(@hoje, INTERVAL 2 DAY), '16:00:00', ADDTIME('16:00:00', '00:50:00'), 'cancelada', 4, 104, 1, NULL, NULL);

-- anotações das consultas concluídas
INSERT INTO AnotacaoClinica (data, conteudo, criadoEm, Consulta_idconsulta, Usuario_idUsuario, Paciente_idpaciente) VALUES
  (DATE_ADD(@hoje, INTERVAL -62 DAY), 'Primeira sessão. Paciente apresenta queixa de estresse no trabalho.', DATE_ADD(@hoje, INTERVAL -62 DAY), 1001, 2, 108),
  (DATE_ADD(@hoje, INTERVAL -48 DAY), 'Dificuldade para dormir. Orientada higiene do sono.', DATE_ADD(@hoje, INTERVAL -48 DAY), 1002, 2, 108),
  (DATE_ADD(@hoje, INTERVAL -41 DAY), 'Relato de ansiedade em situações sociais. Definidas metas iniciais.', DATE_ADD(@hoje, INTERVAL -41 DAY), 1003, 2, 101),
  (DATE_ADD(@hoje, INTERVAL -40 DAY), 'Paciente fala sobre conflitos familiares. Boa abertura na sessão.', DATE_ADD(@hoje, INTERVAL -40 DAY), 1004, 4, 103),
  (DATE_ADD(@hoje, INTERVAL -34 DAY), 'Trabalhadas técnicas de respiração e grounding.', DATE_ADD(@hoje, INTERVAL -34 DAY), 1005, 2, 101),
  (DATE_ADD(@hoje, INTERVAL -27 DAY), 'Evolução positiva. Relata menos episódios de irritabilidade.', DATE_ADD(@hoje, INTERVAL -27 DAY), 1006, 4, 103),
  (DATE_ADD(@hoje, INTERVAL -13 DAY), 'Paciente relatou semana tranquila. Mantido plano terapêutico.', DATE_ADD(@hoje, INTERVAL -13 DAY), 1008, 2, 101),
  (DATE_ADD(@hoje, INTERVAL -12 DAY), 'Discutida rotina de trabalho e limites pessoais.', DATE_ADD(@hoje, INTERVAL -12 DAY), 1009, 4, 104),
  (DATE_ADD(@hoje, INTERVAL -11 DAY), 'Primeira sessão de acompanhamento. Levantamento de histórico.', DATE_ADD(@hoje, INTERVAL -11 DAY), 1010, 2, 102),
  (DATE_ADD(@hoje, INTERVAL -10 DAY), 'Retomada de tarefas combinadas. Paciente engajada.', DATE_ADD(@hoje, INTERVAL -10 DAY), 1011, 4, 103),
  (DATE_ADD(@hoje, INTERVAL -9 DAY), 'Sessão focada em autoestima e relações no trabalho.', DATE_ADD(@hoje, INTERVAL -9 DAY), 1012, 1, 105),
  (DATE_ADD(@hoje, INTERVAL -6 DAY), 'Paciente relata melhora no sono após orientações.', DATE_ADD(@hoje, INTERVAL -6 DAY), 1013, 2, 108),
  (DATE_ADD(@hoje, INTERVAL 0 DAY), 'Paciente trouxe demanda sobre prazos e cobrança no trabalho.', DATE_ADD(@hoje, INTERVAL 0 DAY), 1018, 2, 102);

-- pagamentos: cria o que o trigger não tenha criado (caso ele não exista no seu banco)
INSERT INTO Pagamento (status, valor, data_vencimento, data_pagamento, Paciente_idpaciente, Consulta_idconsulta, Usuario_idUsuario)
SELECT 'pendente', u.valor_consulta, c.data, NULL, c.id_paciente, c.idconsulta, c.id_psicologo
FROM Consulta c
JOIN Usuario u ON u.idUsuario = c.id_psicologo
WHERE c.idconsulta >= 1000 AND c.status <> 'cancelada'
  AND NOT EXISTS (SELECT 1 FROM Pagamento p WHERE p.Consulta_idconsulta = c.idconsulta);

-- os já recebidos
UPDATE Pagamento
SET status = 'pago', data_pagamento = data_vencimento
WHERE Consulta_idconsulta IN (1001,1002,1003,1004,1005,1006,1008,1009,1012,1013,1018);

-- os demais: pendente até o vencimento, atrasado depois
UPDATE Pagamento
SET status = IF(data_vencimento < @hoje, 'atrasado', 'pendente'), data_pagamento = NULL
WHERE Consulta_idconsulta >= 1000 AND Consulta_idconsulta NOT IN (1001,1002,1003,1004,1005,1006,1008,1009,1012,1013,1018);
