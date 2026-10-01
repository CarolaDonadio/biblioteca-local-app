(function () {
  'use strict';

  const menu = document.getElementById('admin-navigation');
  const toggle = document.querySelector('.admin-mobilebar__toggle');
  if (!menu || !toggle) return;

  toggle.addEventListener('click', () => {
    const abierto = menu.classList.toggle('admin-sidebar--open');
    toggle.setAttribute('aria-expanded', String(abierto));
    toggle.setAttribute(
      'aria-label',
      abierto ? 'Cerrar menú administrativo' : 'Abrir menú administrativo'
    );
  });
})();
