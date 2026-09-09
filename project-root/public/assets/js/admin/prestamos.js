/**
 * Módulo Préstamos: filtra los selects de libro y socio desde una caja de
 * búsqueda y avisa si no hay ejemplares disponibles antes de enviar
 * (la validación real y atómica ocurre igual en el servidor).
 */
(function () {
  'use strict';

  const normalizar = (texto) =>
    (texto || '')
      .toString()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .toLowerCase()
      .trim();

  function filtrarSelect(inputId, selectId, contadorId, sustantivo) {
    const input = document.getElementById(inputId);
    const select = document.getElementById(selectId);
    const contador = document.getElementById(contadorId);
    if (!input || !select) return;

    const placeholder = select.options[0];
    const opciones = Array.from(select.options).slice(1);
    opciones.forEach((o) => {
      o.dataset.buscarNorm = normalizar(o.dataset.buscar || o.textContent);
    });

    const aplicar = () => {
      const termino = normalizar(input.value);
      const visibles = termino
        ? opciones.filter((o) => o.dataset.buscarNorm.includes(termino))
        : opciones;

      const seleccionado = select.value;
      select.replaceChildren(placeholder, ...visibles);

      if (visibles.some((o) => o.value === seleccionado)) {
        select.value = seleccionado;
      } else if (select.value !== '') {
        select.value = '';
        select.dispatchEvent(new Event('change'));
      }

      if (contador) {
        contador.textContent = termino
          ? `${visibles.length} ${sustantivo}(s) coinciden con la búsqueda.`
          : '';
      }
    };

    input.addEventListener('input', aplicar);
    input.addEventListener('search', aplicar);
    input.addEventListener('keydown', (ev) => {
      if (ev.key === 'Enter') ev.preventDefault();
    });
  }

  filtrarSelect('buscar-libro', 'select-libro', 'contador-libros', 'libro');
  filtrarSelect('buscar-socio', 'socio_id', 'contador-socios', 'socio');

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
