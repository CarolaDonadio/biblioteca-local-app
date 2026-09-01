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
      const resp = await fetch(`/catalogo/libro/${libroId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      // Nota: en un entorno de producción conviene un endpoint JSON dedicado
      // (ej. /admin/libros/:id/disponibilidad) en vez de parsear la vista pública.
      aviso.textContent = resp.ok ? 'Verificá el detalle del libro para confirmar ejemplares libres.' : '';
    } catch {
      aviso.textContent = '';
    }
  });
})();
