/**
 * Buscador público de libros (consulta pública): pide resultados por
 * AJAX a /catalogo/buscar y reemplaza el fragmento de la grilla,
 * evitando recargar toda la página y respetando el Lazy Load.
 */
(function () {
  'use strict';

  const form = document.querySelector('#form-buscador');
  const grilla = document.querySelector('#catalogo-resultados');
  if (!form || !grilla) return;

  let timeoutId = null;

  async function buscar() {
    const datos = new FormData(form);
    const params = new URLSearchParams(datos).toString();

    try {
      const respuesta = await fetch(`${form.action}?${params}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
      });
      if (!respuesta.ok) throw new Error('Error de red al buscar');
      grilla.innerHTML = await respuesta.text();
      if (window.BibliotecaLazyLoad) window.BibliotecaLazyLoad.rescan();
    } catch (err) {
      grilla.innerHTML = '<p class="alerta alerta--error">No se pudo cargar el catálogo. Probá de nuevo.</p>';
    }
  }

  form.addEventListener('submit', (e) => { e.preventDefault(); buscar(); });

  const campoTexto = form.querySelector('input[name="q"]');
  if (campoTexto) {
    campoTexto.addEventListener('input', () => {
      clearTimeout(timeoutId);
      timeoutId = setTimeout(buscar, 350); // debounce para no saturar el servidor
    });
  }

  const selectCategoria = form.querySelector('select[name="categoria"]');
  if (selectCategoria) selectCategoria.addEventListener('change', buscar);
})();
