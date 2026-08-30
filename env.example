<?php ob_start(); ?>

<section class="pub-hero">
  <h1>Buscá en el catálogo de la biblioteca</h1>
  <p style="color:var(--gris-texto);">Consultá disponibilidad de libros físicos y accedé a material digital.</p>
  <form id="form-buscador" class="buscador" action="/catalogo/buscar" method="get">
    <input type="text" name="q" placeholder="Título, autor o ISBN..." value="<?= esc($termino ?? '') ?>">
    <select name="categoria">
      <option value="">Todas las categorías</option>
      <?php foreach ($categorias ?? [] as $c): if (empty($c['categoria'])) continue; ?>
        <option value="<?= esc($c['categoria']) ?>" <?= ($categoria ?? '') === $c['categoria'] ? 'selected' : '' ?>><?= esc($c['categoria']) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn">Buscar</button>
  </form>
</section>

<?php if (! empty($promociones)): ?>
<div class="promo-banner">
  <?php foreach ($promociones as $p): ?>
    <div class="promo-card">
      <span class="sello sello--promocion">promoción</span>
      <h3><?= esc($p['titulo']) ?></h3>
      <p style="font-size:.85rem;color:var(--gris-texto);"><?= esc(mb_strimwidth($p['descripcion'] ?? '', 0, 90, '…')) ?></p>
    </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="pub-contenido">
  <div id="catalogo-resultados">
    <?= view('publico/_fragmento_resultados', ['libros' => $libros]) ?>
  </div>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/public_layout', [
  'titulo' => 'Catálogo',
  'contenido' => $contenido,
  'js_extra' => '<script src="/assets/js/public/catalogo.js"></script>',
]) ?>
