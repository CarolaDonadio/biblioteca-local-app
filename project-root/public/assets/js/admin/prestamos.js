/**
 * Módulo Préstamos: combobox de búsqueda + selección en un solo campo
 * para libro y socio (máx. 5 resultados, navegables con flechas y Enter),
 * y aviso de disponibilidad antes de enviar (el servidor valida igual).
 */
(function () {
  'use strict';

  const MAX_RESULTADOS = 5;

  const normalizar = (texto) =>
    (texto || '')
      .toString()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .toLowerCase()
      .trim();

  function iniciarCombobox(contenedor) {
    const input = contenedor.querySelector('.combobox__input');
    const select = contenedor.querySelector('select');
    const lista = contenedor.querySelector('.combobox__lista');
    if (!input || !select || !lista) return;

    const opciones = Array.from(select.options).filter((o) => o.value !== '');
    opciones.forEach((o) => {
      o.dataset.buscarNorm = normalizar(o.dataset.buscar || o.textContent);
    });

    let visibles = [];
    let activa = -1;

    const seleccionar = (opcion) => {
      select.value = opcion.value;
      input.value = opcion.dataset.label || opcion.textContent.trim();
      select.dispatchEvent(new Event('change'));
      cerrar();
    };

    const pintar = () => {
      lista.replaceChildren();
      if (!visibles.length) {
        const vacio = document.createElement('li');
        vacio.className = 'combobox__vacio';
        vacio.textContent = 'Sin resultados.';
        lista.append(vacio);
        return;
      }
      visibles.forEach((opcion, indice) => {
        const item = document.createElement('li');
        item.className = 'combobox__opcion' + (opcion.disabled ? ' is-deshabilitada' : '');
        if (indice === activa) item.classList.add('is-activa');
        item.setAttribute('role', 'option');
        item.textContent = opcion.dataset.label || opcion.textContent.trim();
        item.addEventListener('mousedown', (ev) => {
          ev.preventDefault();
          if (!opcion.disabled) seleccionar(opcion);
        });
        lista.append(item);
      });
    };

    const abrir = () => {
      lista.hidden = false;
      input.setAttribute('aria-expanded', 'true');
    };

    const cerrar = () => {
      lista.hidden = true;
      input.setAttribute('aria-expanded', 'false');
      activa = -1;
    };

    const filtrar = () => {
      const termino = normalizar(input.value);

      if (select.value && input.value !== (select.options[select.selectedIndex]?.dataset.label)) {
        select.value = '';
        select.dispatchEvent(new Event('change'));
      }

      const coincidencias = termino
        ? opciones.filter((o) => o.dataset.buscarNorm.includes(termino))
        : opciones;
      visibles = coincidencias.slice(0, MAX_RESULTADOS);
      activa = visibles.findIndex((o) => !o.disabled);
      pintar();
      abrir();
    };

    const moverActiva = (delta) => {
      if (!visibles.length) return;
      let siguiente = activa;
      for (let i = 0; i < visibles.length; i++) {
        siguiente = (siguiente + delta + visibles.length) % visibles.length;
        if (!visibles[siguiente].disabled) break;
      }
      activa = siguiente;
      pintar();
    };

    input.addEventListener('focus', filtrar);
    input.addEventListener('input', filtrar);

    input.addEventListener('keydown', (ev) => {
      if (ev.key === 'ArrowDown') {
        ev.preventDefault();
        if (lista.hidden) filtrar(); else moverActiva(1);
      } else if (ev.key === 'ArrowUp') {
        ev.preventDefault();
        moverActiva(-1);
      } else if (ev.key === 'Enter') {
        ev.preventDefault();
        if (!lista.hidden && activa >= 0 && !visibles[activa].disabled) {
          seleccionar(visibles[activa]);
        }
      } else if (ev.key === 'Escape') {
        cerrar();
      }
    });

    input.addEventListener('blur', () => {
      // Pequeño retraso para permitir que el click en un ítem se procese antes de cerrar.
      setTimeout(() => {
        cerrar();
        const opcionActual = select.options[select.selectedIndex];
        input.value = select.value && opcionActual ? (opcionActual.dataset.label || opcionActual.textContent.trim()) : '';
      }, 120);
    });
  }

  document.querySelectorAll('[data-combobox]').forEach(iniciarCombobox);

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

  const form = selectLibro.closest('form');
  const errorFormulario = document.getElementById('error-formulario');
  if (form && errorFormulario) {
    form.addEventListener('submit', (ev) => {
      const socioId = document.getElementById('socio_id');
      if (!selectLibro.value || !socioId?.value) {
        ev.preventDefault();
        errorFormulario.textContent = 'Seleccioná un libro y un socio de las listas antes de continuar.';
      } else {
        errorFormulario.textContent = '';
      }
    });
  }
})();
