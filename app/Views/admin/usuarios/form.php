<?php ob_start(); $editando = $usuario !== null; ?>

<div class="tarjeta" style="padding:1.6em 1.8em;max-width:600px;">
  <?php if (isset($errors)): ?>
    <div class="alerta alerta--error"><?php foreach ($errors as $e) echo esc($e) . '<br>'; ?></div>
  <?php endif; ?>

  <form action="<?= $editando ? "/admin/usuarios/{$usuario['dni']}" : '/admin/usuarios' ?>" method="post">
    <?= csrf_field() ?>
    <?php if ($editando): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>

    <div class="campo">
      <label for="nombre">Nombre completo</label>
      <input type="text" id="nombre" name="nombre_completo" required value="<?= esc($usuario['nombre_completo'] ?? old('nombre_completo')) ?>">
    </div>
    <div class="campo">
      <label for="dni">DNI</label>
      <input type="text" id="dni" name="dni" required value="<?= esc($usuario['dni'] ?? old('dni')) ?>" <?= $editando ? 'disabled' : '' ?>>
    </div>
    <div class="campo">
      <label for="email">Email</label>
      <input type="email" id="email" name="mail" required value="<?= esc($usuario['mail'] ?? old('mail')) ?>">
    </div>
    <div class="campo">
      <label for="telefono">Teléfono</label>
      <input type="text" id="telefono" name="telefono" value="<?= esc($usuario['telefono'] ?? old('telefono')) ?>">
    </div>
    <?php if (! $editando): ?>
    <div class="campo">
      <label for="password">Contraseña</label>
      <input type="password" id="password" name="password" required>
    </div>
    <?php else: ?>
    <div class="campo">
      <label for="password">Nueva contraseña</label>
      <input type="password" id="password" name="password" placeholder="Dejar vacío para no cambiarla">
    </div>
    <div class="campo">
      <label for="estado">Estado</label>
      <select id="estado" name="estado">
        <?php foreach (['activo', 'suspendido'] as $estado): ?>
          <option value="<?= $estado ?>" <?= $usuario['estado'] === $estado ? 'selected' : '' ?>><?= $estado ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <?php endif; ?>

    <button type="submit" class="btn"><?= $editando ? 'Guardar cambios' : 'Crear usuario' ?></button>
    <a href="/admin/usuarios" class="btn btn--outline">Cancelar</a>
  </form>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => $editando ? 'Editar usuario' : 'Nuevo usuario', 'contenido' => $contenido]) ?>
