// Navegação entre seções
document.querySelectorAll('.config-nav-item').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.config-nav-item').forEach(b => b.classList.remove('ativo'));
        document.querySelectorAll('.config-secao').forEach(s => s.classList.remove('ativa'));
        btn.classList.add('ativo');
        document.getElementById(btn.dataset.secao).classList.add('ativa');
    });
});

// Toggle dias da semana
function toggleDia(checkbox) {
    const linha = checkbox.closest('.horario-linha');
    const campos = linha.querySelector('.horario-campos');
    const inputs = campos.querySelectorAll('input');
    if (checkbox.checked) {
        linha.classList.add('ativo');
        campos.classList.remove('desabilitado');
        inputs.forEach(i => { i.disabled = false; i.value = i === inputs[0] ? '08:00' : '18:00'; });
    } else {
        linha.classList.remove('ativo');
        campos.classList.add('desabilitado');
        inputs.forEach(i => { i.disabled = true; i.value = ''; });
    }
}

// Formulário novo usuário
function abrirFormUsuario() {
    document.getElementById('form-novo-usuario').style.display = 'block';
}
function fecharFormUsuario() {
    document.getElementById('form-novo-usuario').style.display = 'none';
}