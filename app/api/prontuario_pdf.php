<?php
// Gera o PDF do prontuário e devolve para download.
// GET /app/api/prontuario_pdf.php?id=7
require_once __DIR__ . "/../../config/sessao.php";
verificarLogin();
require_once __DIR__ . "/../../config/conexao.php";
require_once __DIR__ . "/../includes/consultas.php";
require_once __DIR__ . "/../../vendor/autoload.php";

use Dompdf\Dompdf;
use Dompdf\Options;

$idClinica  = (int) $_SESSION['id_clinica'];
$idPaciente = (int) ($_GET['id'] ?? 0);

$paciente = $idPaciente > 0 ? buscarPacientePorId($conexao, $idPaciente, $idClinica) : null;

// Só o psicólogo responsável (admin ou psicólogo) pode emitir o prontuário.
// A mesma regra das anotações do drawer, conferida aqui no servidor.
$ehResponsavel = $paciente
    && in_array($_SESSION['permissao'], ['ADMIN', 'PSICOLOGO'], true)
    && (int) $paciente['idPsicologo'] === (int) $_SESSION['id'];

if (!$ehResponsavel) {
    http_response_code(403);
    exit('Você não tem permissão para emitir este prontuário.');
}

$clinica      = buscarClinica($conexao, $idClinica);
$profissional = buscarUsuarioPorId($conexao, (int) $_SESSION['id']);

// Só as sessões que têm anotação, da mais antiga para a mais recente
$registros = array_reverse(array_filter(
    buscarSessoesDoPaciente($conexao, $idPaciente),
    fn($sessao) => !empty($sessao['conteudo'])
));

$nomesStatus = [
    'ativo' => 'Ativo', 'em_espera' => 'Em espera', 'pausado' => 'Pausado',
    'encerrado' => 'Encerrado', 'inativo' => 'Inativo',
];

$camposPaciente = [
    'Paciente'           => $paciente['nome'],
    'Data de nascimento' => $paciente['data_nascimento']
                                ? date('d/m/Y', strtotime($paciente['data_nascimento'])) : null,
    'Telefone'           => $paciente['telefone'],
    'Endereço'           => $paciente['endereco'],
    'Situação'           => $nomesStatus[$paciente['status']] ?? $paciente['status'],
];

// Linha de dados da clínica: CNPJ · endereço · telefone (só o que estiver preenchido)
$dadosClinica = array_filter([
    !empty($clinica['CNPJ']) ? 'CNPJ ' . $clinica['CNPJ'] : null,
    $clinica['Endereco'] ?? null,
    $clinica['telefone'] ?? null,
]);

$emitidoEm = date('d/m/Y') . ' às ' . date('H:i');

// Monta o HTML do documento
ob_start();
include __DIR__ . "/../includes/prontuario_documento.php";
$html = ob_get_clean();

// Converte em PDF
$opcoes = new Options();
$opcoes->set('defaultFont', 'Helvetica');
$opcoes->set('isRemoteEnabled', false);

$dompdf = new Dompdf($opcoes);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Numeração "1 / 3" no canto do rodapé, em todas as páginas
$canvas = $dompdf->getCanvas();
$fonte  = $dompdf->getFontMetrics()->getFont('Helvetica');
$canvas->page_text(510, 829, "{PAGE_NUM} / {PAGE_COUNT}", $fonte, 8, [0.42, 0.47, 0.46]);

// Nome do arquivo: prontuario-lucas-rodrigues.pdf
$nomeSimples = trim(preg_replace(
    '/[^a-z0-9]+/', '-',
    strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $paciente['nome']))
), '-');

$dompdf->stream('prontuario-' . $nomeSimples . '.pdf', ['Attachment' => false]);