(function () {
  'use strict';

  const tablero = document.getElementById('gestion-tablero');
  const estado = document.querySelector('[data-gestion-estado]');
  const actualizar = document.querySelector('[data-gestion-actualizar]');
  if (!tablero) return;

  let solicitando = false;

  const cargar = async () => {
    if (solicitando) return;
    solicitando = true;
    try {
      const respuesta = await fetch(tablero.dataset.gestionUrl, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        cache: 'no-store',
      });
      if (!respuesta.ok) throw new Error('No se pudo actualizar');
      tablero.innerHTML = await respuesta.text();
      if (estado) estado.textContent = `Actualizado a las ${new Date().toLocaleTimeString()}`;
    } catch {
      if (estado) estado.textContent = 'Sin conexión: se reintentará automáticamente';
    } finally {
      solicitando = false;
    }
  };

  actualizar?.addEventListener('click', cargar);
  window.setInterval(cargar, 10000);
})();