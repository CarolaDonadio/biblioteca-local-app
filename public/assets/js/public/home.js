document.addEventListener('DOMContentLoaded', () => {

    // 1. Validación rápida del formulario de búsqueda
    const searchForm = document.querySelector('.search-box');
    if (searchForm) {
        searchForm.addEventListener('submit', (e) => {
            const input = searchForm.querySelector('input[name="q"]');
            if (input && input.value.trim() === '') {
                e.preventDefault();
                input.focus();
                input.style.outline = '2px solid #ef4444';
                setTimeout(() => {
                    input.style.outline = 'none';
                }, 1500);
            }
        });
    }

    // 2. Manejo interactivo de inscripción a eventos
    const botonesInscribir = document.querySelectorAll('.btn-inscribir');
    botonesInscribir.forEach(boton => {
        boton.addEventListener('click', (e) => {
            const nombreEvento = e.target.getAttribute('data-evento');
            
            // Simulación de interacción: redirige al portal de socios
            const confirmar = confirm(`¿Deseas registrarte al evento "${nombreEvento}"? Serás redirigido al portal de socios.`);
            if (confirmar) {
                window.location.href = '/socio/login';
            }
        });
    });

});