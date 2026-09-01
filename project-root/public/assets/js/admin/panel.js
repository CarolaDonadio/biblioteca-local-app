/**
 * Comportamientos compartidos del panel administrativo: confirmación
 * antes de acciones destructivas (eliminar libro/socio/ejemplar, etc.)
 * usado en los módulos de Catálogo, Socios, Inventario y Promociones.
 */
(function () {
  'use strict';

  document.querySelectorAll('form[data-confirmar]').forEach((form) => {
    form.addEventListener('submit', (e) => {
      const mensaje = form.getAttribute('data-confirmar') || '¿Confirmás esta acción?';
      if (!window.confirm(mensaje)) {
        e.preventDefault();
      }
    });
  });

  // Cierra las alertas flash del sistema (mensaje/error) al hacer click.
  document.querySelectorAll('.alerta[data-auto-cerrar]').forEach((alerta) => {
    alerta.addEventListener('click', () => alerta.remove());
    setTimeout(() => alerta.remove(), 6000);
  });
})();
