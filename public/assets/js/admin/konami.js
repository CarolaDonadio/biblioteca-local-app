(function () {
  var secuencia = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight', 'b', 'a'];
  var posicion = 0;

  function cerrar(overlay) {
    overlay.remove();
    document.removeEventListener('keydown', overlay._onEsc);
  }

  function mostrarPopup() {
    if (document.getElementById('konami-overlay')) return;

    var overlay = document.createElement('div');
    overlay.id = 'konami-overlay';
    overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.75);display:flex;align-items:center;justify-content:center;z-index:9999;';

    var caja = document.createElement('div');
    caja.style.cssText = 'position:relative;background:#fff;padding:12px;border-radius:10px;box-shadow:0 10px 40px rgba(0,0,0,.5);max-width:90vw;max-height:90vh;';

    var boton = document.createElement('button');
    boton.type = 'button';
    boton.setAttribute('aria-label', 'Cerrar');
    boton.textContent = '\u00d7';
    boton.style.cssText = 'position:absolute;top:-14px;right:-14px;width:32px;height:32px;border-radius:50%;border:none;background:#222;color:#fff;font-size:20px;cursor:pointer;line-height:1;';

    var img = document.createElement('img');
    img.src = '/assets/images/easter.jpeg';
    img.alt = 'Easter egg';
    img.style.cssText = 'display:block;max-width:calc(90vw - 24px);max-height:calc(90vh - 24px);border-radius:6px;';

    caja.appendChild(boton);
    caja.appendChild(img);
    overlay.appendChild(caja);
    document.body.appendChild(overlay);

    overlay._onEsc = function (e) { if (e.key === 'Escape') cerrar(overlay); };
    document.addEventListener('keydown', overlay._onEsc);
    boton.addEventListener('click', function () { cerrar(overlay); });
    overlay.addEventListener('click', function (e) { if (e.target === overlay) cerrar(overlay); });
  }

  document.addEventListener('keydown', function (e) {
    var tecla = e.key.length === 1 ? e.key.toLowerCase() : e.key;
    if (tecla === secuencia[posicion]) {
      posicion++;
      if (posicion === secuencia.length) {
        posicion = 0;
        mostrarPopup();
      }
    } else {
      posicion = tecla === secuencia[0] ? 1 : 0;
    }
  });
})();
