/**
 * Módulo de optimización visual con Lazy Load (requisito MVP).
 * Carga diferida de portadas e imágenes promocionales: las imágenes
 * se marcan con <img class="lazy" data-src="..."> y solo se cargan
 * cuando entran en el viewport, evitando lentitud en listados largos.
 */
(function () {
  'use strict';

  function cargarImagen(img) {
    const src = img.getAttribute('data-src');
    if (!src) return;

    const fallBack = img.getAttribute('data-fallback');
    img.src = src;
    img.removeAttribute('data-src');
    img.classList.add('cargada');

    if (fallBack) {
      img.dataset.fallback = fallBack;
    }
  }

  function iniciar() {
    const imagenes = document.querySelectorAll('img.lazy[data-src]');

    if (!('IntersectionObserver' in window)) {
      // Fallback para navegadores/redes antiguas: carga todo de una vez.
      imagenes.forEach(cargarImagen);
      return;
    }

    const observer = new IntersectionObserver((entradas, obs) => {
      entradas.forEach((entrada) => {
        if (entrada.isIntersecting) {
          cargarImagen(entrada.target);
          obs.unobserve(entrada.target);
        }
      });
    }, { rootMargin: '150px 0px' });

    imagenes.forEach((img) => observer.observe(img));
  }

  document.addEventListener('DOMContentLoaded', iniciar);

  // Re-escanea después de resultados cargados por AJAX (buscador del catálogo).
  window.BibliotecaLazyLoad = { rescan: iniciar, cargarImagen };
})();
