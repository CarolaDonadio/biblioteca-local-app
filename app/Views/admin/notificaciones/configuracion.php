<?php ob_start(); ?>

<div class="tarjeta" style="padding:1.6em 1.8em;max-width:560px;">
  <p style="color:var(--gris-texto);font-size:.85rem;margin-top:0;">
    Los tokens de las APIs (Telegram, WhatsApp, SMTP) se configuran como variables de
    entorno en el archivo <code>.env</code> del servidor, no desde acá, por seguridad.
    Este panel solo controla qué canales están activos y los umbrales de recordatorio.
  </p>

  <form action="/admin/notificaciones/configuracion" method="post">
    <?= csrf_field() ?>
    <div class="campo">
      <label><input type="checkbox" name="canal_telegram" checked style="width:auto;display:inline;"> Telegram activo</label>
    </div>
    <div class="campo">
      <label><input type="checkbox" name="canal_whatsapp" checked style="width:auto;display:inline;"> WhatsApp activo</label>
    </div>
    <div class="campo">
      <label><input type="checkbox" name="canal_email" checked style="width:auto;display:inline;"> Email activo</label>
    </div>
    <div class="campo">
      <label for="dias_recordatorio">Recordar devolución con cuántos días de anticipación</label>
      <input type="number" id="dias_recordatorio" name="dias_recordatorio" value="2" min="1" max="7">
    </div>
    <button type="submit" class="btn">Guardar configuración</button>
  </form>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => 'Configuración de notificaciones', 'contenido' => $contenido]) ?>
