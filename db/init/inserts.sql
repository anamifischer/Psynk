-------------------------------------
-- DADOS MOCKADOS NÍVEL BANCO
-------------------------------------

USE `mydb`;

-- =====================================================
-- CLINICA
-- =====================================================
INSERT INTO `Clinica` (`nome`, `CNPJ`, `endereco`, `telefone`) VALUES
('Clínica Mente Sã', '12.345.678/0001-90', 'Rua das Flores, 123 - Centro, São Paulo/SP', '(11) 3456-7890');


-- =====================================================
-- USUARIOS (senha = bcrypt de "senha123" — placeholder)
-- =====================================================
INSERT INTO `Usuario` (`nome`, `email`, `senha`, `telefone`, `permissao`, `id_clinica`) VALUES
('Dr. Carlos Mendes',     'carlos.mendes@mentesa.com.br',  '$2b$12$placeholderHashCarlos',   '(11) 91234-5678', 'PSICOLOGO',     1),
('Dra. Ana Beatriz Lima', 'ana.lima@mentesa.com.br',       '$2b$12$placeholderHashAna',      '(11) 92345-6789', 'PSICOLOGO',     1),
('Fernanda Souza',        'fernanda.souza@mentesa.com.br', '$2b$12$placeholderHashFernanda', '(11) 93456-7890', 'RECEPCIONISTA', 1),
('Roberto Admin',         'roberto.admin@mentesa.com.br',  '$2b$12$placeholderHashRoberto',  '(11) 94567-8901', 'ADMIN',         1);


-- =====================================================
-- PACIENTES
-- =====================================================
INSERT INTO `Paciente` (`nome`, `cpf`, `telefone`, `endereco`, `status`, `idClinica`, `idPsicologo`) VALUES
-- Pacientes do Dr. Carlos (idUsuario=1)
('Lucas Ferreira',        '123.456.789-00', '(11) 91111-1111', 'Rua A, 10 - São Paulo/SP', 'ativo',     1, 1),
('Mariana Gomes',         '234.567.890-11', '(11) 92222-2222', 'Rua B, 20 - São Paulo/SP', 'ativo',     1, 1),
('Pedro Henrique Santos', '345.678.901-22', '(11) 93333-3333', 'Rua C, 30 - São Paulo/SP', 'inativo',   1, 1),

-- Pacientes da Dra. Ana (idUsuario=2)
('Isabela Martins',       '456.789.012-33', '(11) 94444-4444', 'Rua D, 40 - São Paulo/SP', 'ativo',     1, 2),
('Rafael Torres',         '567.890.123-44', '(11) 95555-5555', 'Rua E, 50 - São Paulo/SP', 'ativo',     1, 2),
('Beatriz Carvalho',      '678.901.234-55', '(11) 96666-6666', 'Rua F, 60 - São Paulo/SP', 'ativo',     1, 2),
('Diego Ribeiro',         '789.012.345-66', '(11) 97777-7777', 'Rua G, 70 - São Paulo/SP', 'arquivado', 1, 2);


-- =====================================================
-- CONSULTAS
-- =====================================================
INSERT INTO `Consulta` (`data`, `hora_inicio`, `hora_fim`, `status`, `id_psicologo`, `id_paciente`, `id_clinica`) VALUES
-- Concluídas
('2025-07-01', '09:00:00', '10:00:00', 'concluida', 1, 1, 1),
('2025-07-08', '09:00:00', '10:00:00', 'concluida', 1, 1, 1),
('2025-07-15', '10:00:00', '11:00:00', 'concluida', 1, 2, 1),
('2025-07-10', '14:00:00', '15:00:00', 'concluida', 2, 4, 1),
('2025-07-17', '14:00:00', '15:00:00', 'concluida', 2, 5, 1),
('2025-07-22', '11:00:00', '12:00:00', 'concluida', 2, 6, 1),

-- Atrasadas
('2025-08-01', '09:00:00', '10:00:00', 'atrasada',  1, 3, 1),
('2025-08-06', '16:00:00', '17:00:00', 'atrasada',  2, 7, 1),

-- Futuras
('2025-09-10', '09:00:00', '10:00:00', 'aguarda data', 1, 1, 1),
('2025-09-10', '10:00:00', '11:00:00', 'aguarda data', 1, 2, 1),
('2025-09-11', '09:00:00', '10:00:00', 'aguarda data', 1, 3, 1),
('2025-09-11', '14:00:00', '15:00:00', 'aguarda data', 2, 4, 1),
('2025-09-11', '15:00:00', '16:00:00', 'aguarda data', 2, 5, 1),
('2025-09-12', '11:00:00', '12:00:00', 'aguarda data', 2, 6, 1);


-- =====================================================
-- ANOTACOES CLINICAS
-- =====================================================
INSERT INTO `AnotacaoClinica` (`data`, `conteudo`, `id_consulta`, `id_usuario`, `id_paciente`) VALUES
('2025-07-01',
 'Primeira sessão. Paciente relata ansiedade generalizada e dificuldades no trabalho. Boa disposição para o processo terapêutico. Proposta de abordagem TCC.',
 1, 1, 1),

('2025-07-08',
 'Retorno. Paciente realizou as tarefas propostas. Relata leve melhora na qualidade do sono. Mantemos foco em reestruturação cognitiva.',
 2, 1, 1),

('2025-07-15',
 'Sessão inicial com Mariana. Queixa principal: síndrome do pânico com episódios em locais públicos. Anamnese completa realizada. Encaminhamento para avaliação psiquiátrica sugerido.',
 3, 1, 2),

('2025-07-10',
 'Paciente em processo de luto após perda de familiar. Sessão focada em acolhimento e validação emocional. Abordagem humanista.',
 4, 2, 4),

('2025-07-17',
 'Paciente relata conflitos no relacionamento conjugal. Iniciado trabalho de comunicação não-violenta. Boa adesão à terapia.',
 5, 2, 5),

('2025-07-22',
 'Segunda sessão com Beatriz. Apresentou melhora no padrão de sono após técnicas de higiene trabalhadas anteriormente. Continuamos EMDR para trauma.',
 6, 2, 6);


-- =====================================================
-- PAGAMENTOS
-- =====================================================
INSERT INTO `Pagamento` (`status`, `valor`, `data_vencimento`, `data_pagamento`, `id_paciente`, `id_consulta`, `id_usuario`) VALUES
-- Pagos
('pago',     150.00, '2025-07-01', '2025-07-01', 1, 1,  3),
('pago',     150.00, '2025-07-08', '2025-07-10', 1, 2,  3),
('pago',     150.00, '2025-07-15', '2025-07-15', 2, 3,  3),
('pago',     180.00, '2025-07-10', '2025-07-10', 4, 4,  3),
('pago',     180.00, '2025-07-17', '2025-07-20', 5, 5,  3),
('pago',     180.00, '2025-07-22', '2025-07-24', 6, 6,  3),

-- Atrasados
('atrasado', 150.00, '2025-08-01', NULL,          3, 7,  3),
('atrasado', 180.00, '2025-08-06', NULL,          7, 8,  3),

-- Pendentes
('pendente', 150.00, '2025-09-10', NULL, 1, 9,  3),
('pendente', 150.00, '2025-09-10', NULL, 2, 10, 3),
('pendente', 150.00, '2025-09-11', NULL, 3, 11, 3),
('pendente', 180.00, '2025-09-11', NULL, 4, 12, 3),
('pendente', 180.00, '2025-09-11', NULL, 5, 13, 3),
('pendente', 180.00, '2025-09-12', NULL, 6, 14, 3);


-- =====================================================
-- NOTIFICACOES
-- =====================================================
INSERT INTO `Notificacao` (`mensagem`, `lida`, `id_usuario`) VALUES
-- Recepcionista
('Nova consulta agendada para Lucas Ferreira em 10/09.',        0, 3),
('Pagamento de Pedro Henrique está atrasado há 30 dias.',       0, 3),
('Pagamento de Diego Ribeiro em atraso.',                       0, 3),
('Consulta de Mariana Gomes confirmada para 10/09.',            1, 3),

-- Dr. Carlos
('Você tem 3 consultas agendadas para setembro.',               0, 1),
('Lembrete: sessão com Lucas Ferreira às 09h amanhã.',          0, 1),
('Anotação clínica de Mariana Gomes salva com sucesso.',        1, 1),

-- Dra. Ana
('Você tem 3 consultas agendadas para setembro.',               0, 2),
('Lembrete: sessão com Isabela Martins às 14h amanhã.',         0, 2),
('Novo paciente atribuído: Beatriz Carvalho.',                  1, 2),

-- Admin
('2 pagamentos em atraso. Verifique o financeiro.',             0, 4),
('Relatório mensal de julho disponível para revisão.',          0, 4),
('7 pacientes cadastrados na clínica.',                         1, 4),
('Novo usuário cadastrado: Fernanda Souza (Recepcionista).',    1, 4);