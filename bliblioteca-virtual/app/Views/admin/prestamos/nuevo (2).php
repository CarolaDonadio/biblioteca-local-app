<?php ob_start(); ?>

<div class="tarjeta" style="padding:1.6em 1.8em;max-width:520px;">
  <form action="/admin/prestamos" method="post">
    <?= csrf_field() ?>
    <div class="campo">
      <label for="select-libro">Libro</label>
      <select id="select-libro" name="libro_id" required>
        <option value="">Seleccioná un libro...</option>
        <?php foreach ($libros as $l): ?>
          <option value="<?= $l['id'] ?>"><?= esc($l['titulo']) ?> — <?= esc($l['autor']) ?></option>
        <?php endforeach; ?>
      </select>
      <small id="aviso-disponibilidad" style="color:var(--gris-texto);"></small>
    </div>
    <div class="campo">
      <label for="socio_id">Socio</label>
      <select id="socio_id" name="socio_id" required>
        <option value="">Seleccioná un socio...</option>
        <?php foreach ($socios as $s): ?>
          <option value="<?= $s['id'] ?>"><?= esc($s['apellido']) ?>, <?= esc($s['nombre']) ?> (<?= esc($s['dni']) ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>

    <button type="submit" class="btn">Registrar préstamo</button>
    <a href="/admin/prestamos" class="btn btn--outline">Cancelar</a>
  </form>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', [
  'titulo' => 'Nuevo préstamo',
  'contenido' => $contenido,
  'js_extra' => '<script src="/assets/js/admin/prestamos.js"></script>',
]) ?>
