<?php ob_start(); ?>

<div class="pub-contenido" style="max-width:480px;">
  <h1>Asociarme a la biblioteca</h1>
  <div class="tarjeta" style="padding:1.6em 1.8em;">
    <?php if (session('errors')): ?>
      <div class="alerta alerta--error"><?php foreach (session('errors') as $e) echo esc($e) . '<br>'; ?></div>
    <?php endif; ?>

    <form action="/socio/registro" method="post">
      <?= csrf_field() ?>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1em;">
        <div class="campo">
          <label for="nombre">Nombre</label>
          <input type="text" id="nombre" name="nombre" value="<?= esc(old('nombre')) ?>" required>
        </div>
        <div class="campo">
          <label for="apellido">Apellido</label>
          <input type="text" id="apellido" name="apellido" value="<?= esc(old('apellido')) ?>" required>
        </div>
      </div>
      <div class="campo">
        <label for="dni">DNI</label>
        <input type="text" id="dni" name="dni" value="<?= esc(old('dni')) ?>" required>
      </div>
      <div class="campo">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= esc(old('email')) ?>" required>
      </div>
      <div class="campo">
        <label for="telefono">Teléfono (opcional)</label>
        <input type="text" id="telefono" name="telefono" value="<?= esc(old('telefono')) ?>">
      <div class="campo">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required minlength="6">
      </div>
      <button type="submit" class="btn" style="width:100%;">Crear cuenta</button>
    </form>
  </div>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/public_layout', ['titulo' => 'Asociarme', 'contenido' => $contenido]) ?>
