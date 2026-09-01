<?php ob_start(); $editando = $ejemplar !== null; ?>

<div class="tarjeta" style="padding:1.6em 1.8em;max-width:560px;">
  <?php if (isset($errors)): ?>
    <div class="alerta alerta--error"><?php foreach ($errors as $e) echo esc($e) . '<br>'; ?></div>
  <?php endif; ?>

  <form action="<?= $editando ? "/admin/ejemplares/{$ejemplar['id']}" : '/admin/ejemplares' ?>" method="post">
    <?= csrf_field() ?>
    <?php if ($editando): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>

    <div class="campo">
      <label for="libro_id">Libro</label>
      <select id="libro_id" name="libro_id" required>
        <option value="">Seleccioná un libro...</option>
        <?php foreach ($libros as $l): ?>
          <option value="<?= $l['id'] ?>" <?= (($ejemplar['libro_id'] ?? null) == $l['id']) ? 'selected' : '' ?>>
            <?= esc($l['titulo']) ?> — <?= esc($l['autor']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="campo">
      <label for="codigo_inventario">Código de inventario</label>
      <input type="text" id="codigo_inventario" name="codigo_inventario" required value="<?= esc($ejemplar['codigo_inventario'] ?? old('codigo_inventario')) ?>">
    </div>
    <div class="campo">
      <label for="ubicacion">Ubicación (estantería, sala)</label>
      <input type="text" id="ubicacion" name="ubicacion" value="<?= esc($ejemplar['ubicacion'] ?? old('ubicacion')) ?>">
    </div>
    <?php if ($editando): ?>
    <div class="campo">
      <label for="estado">Estado</label>
      <select id="estado" name="estado">
        <?php foreach (['disponible','prestado','reservado','perdido','danado','baja'] as $estado): ?>
          <option value="<?= $estado ?>" <?= $ejemplar['estado'] === $estado ? 'selected' : '' ?>><?= $estado ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <?php endif; ?>

    <button type="submit" class="btn"><?= $editando ? 'Guardar cambios' : 'Registrar ejemplar' ?></button>
    <a href="/admin/ejemplares" class="btn btn--outline">Cancelar</a>
  </form>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => $editando ? 'Editar ejemplar' : 'Nuevo ejemplar', 'contenido' => $contenido]) ?>
