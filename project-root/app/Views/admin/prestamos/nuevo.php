<?php ob_start(); ?>

<div class="tarjeta" style="padding:1.6em 1.8em;max-width:520px;">
  <form action="/admin/prestamos" method="post">
    <?= csrf_field() ?>
    <div class="campo">
      <label for="buscar-libro">Buscar libro</label>
      <input type="search" id="buscar-libro" placeholder="ISBN, título, autor o categoría" autocomplete="off">
      <small id="contador-libros" style="color:var(--gris-texto);"></small>
    </div>
    <div class="campo">
      <label for="select-libro">Libro</label>
      <select id="select-libro" name="libro_id" required>
        <option value="">Seleccioná un libro...</option>
        <?php foreach (($libros ?? []) as $l): $libres = (int) ($l['disponibles'] ?? 0); ?>
          <option value="<?= $l['id'] ?>"
                  data-disponibles="<?= $libres ?>"
                  data-buscar="<?= esc(trim(($l['isbn'] ?? '') . ' ' . $l['titulo'] . ' ' . $l['autor'] . ' ' . ($l['categoria'] ?? '')), 'attr') ?>"
                  <?= $libres < 1 ? 'disabled' : '' ?>>
            <?= esc($l['titulo']) ?> — <?= esc($l['autor']) ?> (<?= $libres ?> disp.)
          </option>
        <?php endforeach; ?>
      </select>
      <small id="aviso-disponibilidad" style="color:var(--gris-texto);"></small>
    </div>
    <div class="campo">
      <label for="buscar-socio">Buscar socio</label>
      <input type="search" id="buscar-socio" placeholder="DNI, nombre o email" autocomplete="off">
      <small id="contador-socios" style="color:var(--gris-texto);"></small>
    </div>
    <div class="campo">
      <label for="socio_id">Socio</label>
      <select id="socio_id" name="socio_id" required>
        <option value="">Seleccioná un socio...</option>
        <?php foreach (($socios ?? []) as $s): ?>
          <option value="<?= esc($s['dni'], 'attr') ?>"
                  data-buscar="<?= esc(trim($s['dni'] . ' ' . $s['nombre_completo'] . ' ' . ($s['mail'] ?? '')), 'attr') ?>">
            <?= esc($s['nombre_completo']) ?> (DNI <?= esc($s['dni']) ?>)<?= empty($s['mail']) ? '' : ' · ' . esc($s['mail']) ?>
          </option>
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
