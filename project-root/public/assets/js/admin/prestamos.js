/**
 * Módulo Préstamos: al elegir un libro en el formulario de nuevo
 * préstamo, avisa si no hay ejemplares disponibles antes de enviar
 * (la validación real y atómica ocurre igual en el servidor).
 */
(function () {
  'use strict';

  const selectLibro = document.querySelector('#select-libro');
  const aviso = document.querySelector('#aviso-disponibilidad');
  if (!selectLibro || !aviso) return;

  selectLibro.addEventListener('change', async () => {
    const libroId = selectLibro.value;
    if (!libroId) { aviso.textContent = ''; return; }

    aviso.textContent = 'Consultando disponibilidad...';
    try {
      const resp = await fetch(`/admin/prestamos/disponibilidad/${libroId}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
      });
      if (!resp.ok) { aviso.textContent = ''; return; }

      const data = await resp.json();
      aviso.textContent = data.disponibles > 0
        ? `${data.disponibles} de ${data.cantidad} ejemplar(es) disponible(s).`
        : 'Sin ejemplares disponibles: todos est\u00e1n prestados.';
    } catch {
      aviso.textContent = '';
    }
  });
})();
