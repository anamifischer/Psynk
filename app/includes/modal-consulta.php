<link rel="stylesheet" type="text/css" href="/app/css/modal-consulta.css">
<!-- Modal Nova Consulta -->
<div class="modal-overlay" id="modal-consulta-overlay" onclick="fecharModalConsulta()"></div>

<div class="modal-consulta" id="modal-consulta">
    <div class="modal-consulta-header">
        <div>
            <h3 class="modal-consulta-titulo">Nova consulta</h3>
            <p class="modal-consulta-subtitulo" id="modal-consulta-subtitulo"></p>
        </div>
        <button class="drawer-fechar" onclick="fecharModalConsulta()">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>

    <div class="modal-consulta-corpo">

        <div class="modal-consulta-campo">
            <label>Paciente</label>
            <select id="modal-consulta-paciente" onchange="preencherPsicologo()">
                <option value="">Selecione o paciente...</option>
            </select>
        </div>

        <div class="modal-consulta-campo">
            <label>Psicólogo(a)</label>
            <select id="modal-consulta-psicologo">
                <option value="">Selecione o psicólogo...</option>
                <option value="Dr. Carlos Souza">Dr. Carlos Souza</option>
                <option value="Dra. Fernanda Rocha">Dra. Fernanda Rocha</option>
                <option value="Dr. Pedro Alves">Dr. Pedro Alves</option>
            </select>
        </div>

        <div class="modal-consulta-linha">
            <div class="modal-consulta-campo">
                <label>Data</label>
                <input type="date" id="modal-consulta-data">
            </div>
            <div class="modal-consulta-campo">
                <label>Horário de início</label>
                <input type="time" id="modal-consulta-hora">
            </div>
            <div class="modal-consulta-campo">
                <label>Duração</label>
                <select id="modal-consulta-duracao">
                    <option value="30">30 min</option>
                    <option value="45" selected>45 min</option>
                    <option value="50">50 min</option>
                    <option value="60">60 min</option>
                </select>
            </div>
        </div>

        <div class="modal-consulta-horario-fim" id="modal-consulta-horario-fim"></div>

        <div class="modal-consulta-campo">
            <label>Status</label>
            <select id="modal-consulta-status">
                <option value="pendente">Pendente</option>
                <option value="confirmado">Confirmado</option>
            </select>
        </div>

        <div class="modal-consulta-campo">
            <label>Anotação prévia <span class="modal-consulta-opcional">(opcional)</span></label>
            <textarea id="modal-consulta-anotacao" class="modal-textarea" placeholder="Observações iniciais sobre a consulta..."></textarea>
        </div>

    </div>

    <div class="modal-rodape">
        <button class="btn btn-secondary" onclick="fecharModalConsulta()">Cancelar</button>
        <button class="btn btn-primary" onclick="salvarConsulta()">Agendar consulta</button>
    </div>
</div>