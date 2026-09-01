/**
 * Confirma acciones del motor de reservas desde el catálogo público
 * y evita doble envío mientras se procesa la petición al servidor.
 */
(function () {
  'use strict';

  document.querySelectorAll('form[data-confirmar-reserva]').forEach((form) => {
    form.addEventListener('submit', (e) => {
      const mensaje = form.getAttribute('data-confirmar-reserva') || '¿Confirmás la reserva de este libro?';
      if (!window.confirm(mensaje)) {
        e.preventDefault();
        return;
      }
      const boton = form.querySelector('button[type="submit"]');
      if (boton) {
        boton.disabled = true;
        boton.textContent = 'Procesando...';
      }
    });
  });
})();
