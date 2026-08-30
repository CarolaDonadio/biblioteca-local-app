<?php ob_start(); $editando = $promocion !== null; ?>

<div class="tarjeta" style="padding:1.6em 1.8em;max-width:560px;">
  <?php if (isset($errors)): ?>
    <div class="alerta alerta--error"><?php foreach ($errors as $e) echo esc($e) . '<br>'; ?></div>
  <?php endif; ?>

  <form action="<?= $editando ? "/admin/promociones/{$promocion['id']}" : '/admin/promociones' ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php if ($editando): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>

    <div class="campo">
      <label for="titulo">Título</label>
      <input type="text" id="titulo" name="titulo" required value="<?= esc($promocion['titulo'] ?? old('titulo')) ?>">
    </div>
    <div class="campo">
      <label for="descripcion">Descripción</label>
      <textarea id="descripcion" name="descripcion" rows="3"><?= esc($promocion['descripcion'] ?? old('descripcion')) ?></textarea>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1em;">
      <div class="campo">
        <label for="fecha_inicio">Vigencia desde</label>
        <input type="date" id="fecha_inicio" name="fecha_inicio" required value="<?= esc($promocion['fecha_inicio'] ?? old('fecha_inicio')) ?>">
      </div>
      <div class="campo">
        <label for="fecha_fin">Vigencia hasta</label>
        <input type="date" id="fecha_fin" name="fecha_fin" required value="<?= esc($promocion['fecha_fin'] ?? old('fecha_fin')) ?>">
      </div>
    </div>
    <div class="campo">
      <label for="imagen">Imagen promocional</label>
      <input type="file" id="imagen" name="imagen">
    </div>
    <div class="campo">
      <label><input type="checkbox" name="activo" <?= ($promocion['activo'] ?? true) ? 'checked' : '' ?> style="width:auto;display:inline;"> Activa</label>
    </div>

    <button type="submit" class="btn"><?= $editando ? 'Guardar cambios' : 'Publicar promoción' ?></button>
    <a href="/admin/promociones" class="btn btn--outline">Cancelar</a>
  </form>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => $editando ? 'Editar promoción' : 'Nueva promoción', 'contenido' => $contenido]) ?>
