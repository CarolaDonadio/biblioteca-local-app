<?php ob_start(); ?>

<div class="tarjeta" style="padding:1.6em 1.8em;max-width:520px;">
  <form action="/admin/prestamos" method="post">
    <?= csrf_field() ?>
    <div class="campo combobox" data-combobox>
      <label for="buscar-libro">Libro</label>
      <input type="text" id="buscar-libro" class="combobox__input" placeholder="Buscar por ISBN, título, autor o categoría"
             autocomplete="off" role="combobox" aria-expanded="false" aria-controls="lista-libro" aria-autocomplete="list">
      <select id="select-libro" name="libro_id" hidden>
        <option value="">Seleccioná un libro...</option>
        <?php foreach (($libros ?? []) as $l): $libres = (int) ($l['disponibles'] ?? 0); ?>
          <option value="<?= $l['id'] ?>"
                  data-disponibles="<?= $libres ?>"
                  data-buscar="<?= esc(trim(($l['isbn'] ?? '') . ' ' . $l['titulo'] . ' ' . $l['autor'] . ' ' . ($l['categoria'] ?? '')), 'attr') ?>"
                  data-label="<?= esc($l['titulo'] . ' — ' . $l['autor'] . ' (' . $libres . ' disp.)', 'attr') ?>"
                  <?= $libres < 1 ? 'disabled' : '' ?>>
            <?= esc($l['titulo']) ?> — <?= esc($l['autor']) ?> (<?= $libres ?> disp.)
          </option>
        <?php endforeach; ?>
      </select>
      <ul id="lista-libro" class="combobox__lista" role="listbox" hidden></ul>
      <small id="aviso-disponibilidad" style="color:var(--gris-texto);"></small>
    </div>
    <div class="campo combobox" data-combobox>
      <label for="buscar-socio">Socio</label>
      <input type="text" id="buscar-socio" class="combobox__input" placeholder="Buscar por DNI, nombre o email"
             autocomplete="off" role="combobox" aria-expanded="false" aria-controls="lista-socio" aria-autocomplete="list">
      <select id="socio_id" name="socio_id" hidden>
        <option value="">Seleccioná un socio...</option>
        <?php foreach (($socios ?? []) as $s): ?>
          <option value="<?= esc($s['dni'], 'attr') ?>"
                  data-buscar="<?= esc(trim($s['dni'] . ' ' . $s['nombre_completo'] . ' ' . ($s['mail'] ?? '')), 'attr') ?>"
                  data-label="<?= esc($s['nombre_completo'] . ' (DNI ' . $s['dni'] . ')' . (empty($s['mail']) ? '' : ' · ' . $s['mail']), 'attr') ?>">
            <?= esc($s['nombre_completo']) ?> (DNI <?= esc($s['dni']) ?>)<?= empty($s['mail']) ? '' : ' · ' . esc($s['mail']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <ul id="lista-socio" class="combobox__lista" role="listbox" hidden></ul>
    </div>

    <small id="error-formulario" style="display:block;color:var(--sello-rojo);margin-bottom:.8em;"></small>
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
