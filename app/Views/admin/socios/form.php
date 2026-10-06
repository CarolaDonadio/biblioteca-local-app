<?php ob_start(); $editando = $socio !== null; ?>

<div class="tarjeta" style="padding:1.6em 1.8em;max-width:600px;">
  <?php if (isset($errors)): ?>
    <div class="alerta alerta--error"><?php foreach ($errors as $e) echo esc($e) . '<br>'; ?></div>
  <?php endif; ?>

  <form action="<?= $editando ? "/admin/socios/{$socio['dni']}" : '/admin/socios' ?>" method="post">
    <?= csrf_field() ?>
    <?php if ($editando): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>

    <div class="campo">
      <div class="campo">
        <label for="nombre">Nombre completo</label>
        <input type="text" id="nombre" name="nombre_completo" required value="<?= esc($socio['nombre_completo'] ?? old('nombre_completo')) ?>">
      </div>
    </div>
    <div class="campo">
      <label for="dni">DNI</label>
      <input type="text" id="dni" name="dni" required value="<?= esc($socio['dni'] ?? old('dni')) ?>" <?= $editando ? 'disabled' : '' ?>>
    </div>
    <div class="campo">
      <label for="email">Email</label>
      <input type="email" id="email" name="mail" required value="<?= esc($socio['mail'] ?? old('mail')) ?>">
    </div>
    <div class="campo">
      <label for="telefono">Teléfono</label>
      <input type="text" id="telefono" name="telefono" value="<?= esc($socio['telefono'] ?? old('telefono')) ?>">
    </div>
    <?php if (! $editando): ?>
    <div class="campo">
      <label for="password">Contraseña inicial</label>
      <input type="password" id="password" name="password" placeholder="Si se deja vacío se genera una automáticamente">
    </div>
    <?php else: ?>
    <div class="campo">
      <label for="estado">Estado</label>
      <select id="estado" name="estado">
        <?php foreach (['activo','suspendido','vencido'] as $estado): ?>
          <option value="<?= $estado ?>" <?= $socio['estado'] === $estado ? 'selected' : '' ?>><?= $estado ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <?php endif; ?>

    <button type="submit" class="btn"><?= $editando ? 'Guardar cambios' : 'Registrar socio' ?></button>
    <a href="/admin/socios" class="btn btn--outline">Cancelar</a>
  </form>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => $editando ? 'Editar socio' : 'Nuevo socio', 'contenido' => $contenido]) ?>
