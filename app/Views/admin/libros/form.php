<?php ob_start(); $editando = $libro !== null; ?>

<div class="tarjeta" style="padding:1.6em 1.8em;max-width:640px;">
  <?php if (isset($errors)): ?>
    <div class="alerta alerta--error"><?php foreach ($errors as $e) echo esc($e) . '<br>'; ?></div>
  <?php endif; ?>

  <form action="<?= $editando ? "/admin/libros/{$libro['id']}" : '/admin/libros' ?>" method="post">
    <?= csrf_field() ?>
    <?php if ($editando): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>

    <div class="campo">
      <label for="isbn">ISBN</label>
      <input type="text" id="isbn" name="isbn" required value="<?= esc($libro['isbn'] ?? old('isbn')) ?>">
    </div>
    <div class="campo">
      <label for="titulo">Título</label>
      <input type="text" id="titulo" name="titulo" required value="<?= esc($libro['titulo'] ?? old('titulo')) ?>">
    </div>
    <div class="campo">
      <label for="autor">Autor</label>
      <input type="text" id="autor" name="autor" required value="<?= esc($libro['autor'] ?? old('autor')) ?>">
    </div>
    <div class="campo">
      <label for="editorial">Editorial</label>
      <input type="text" id="editorial" name="editorial" value="<?= esc($libro['editorial'] ?? old('editorial')) ?>">
    </div>
    <div class="campo">
      <label for="anio">Año</label>
      <input type="number" id="anio" name="anio" value="<?= esc($libro['anio'] ?? old('anio')) ?>">
    </div>
    <div class="campo">
      <label for="categoria">Categoría</label>
      <input type="text" id="categoria" name="categoria" value="<?= esc($libro['categoria'] ?? old('categoria')) ?>">
    </div>
    <div class="campo">
      <label for="sinopsis">Sinopsis</label>
      <textarea id="sinopsis" name="sinopsis" rows="4"><?= esc($libro['sinopsis'] ?? old('sinopsis')) ?></textarea>
    </div>

    <button type="submit" class="btn"><?= $editando ? 'Guardar cambios' : 'Crear libro' ?></button>
    <a href="/admin/libros" class="btn btn--outline">Cancelar</a>
  </form>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => $editando ? 'Editar libro' : 'Nuevo libro', 'contenido' => $contenido]) ?>
