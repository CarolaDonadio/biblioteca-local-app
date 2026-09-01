<?php ob_start(); ?>

<div class="pub-contenido" style="max-width:420px;">
  <h1>Ingresar a mi cuenta</h1>
  <div class="tarjeta" style="padding:1.6em 1.8em;">
    <form action="/socio/login" method="post">
      <?= csrf_field() ?>
      <div class="campo">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required autofocus>
      </div>
      <div class="campo">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit" class="btn" style="width:100%;">Ingresar</button>
    </form>
    <p style="font-size:.85rem;margin-top:1em;">¿No tenés cuenta? <a href="/socio/registro">Asociate acá</a></p>
  </div>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/public_layout', ['titulo' => 'Ingresar', 'contenido' => $contenido]) ?>
