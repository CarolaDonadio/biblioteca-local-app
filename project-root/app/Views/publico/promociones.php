<?php ob_start(); ?>

<div class="pub-contenido">
  <h1>Promociones vigentes</h1>
  <div class="catalogo-grid" style="grid-template-columns:repeat(auto-fill,minmax(240px,1fr));">
    <?php foreach ($promociones as $p): ?>
      <div class="tarjeta" style="padding:1.2em;">
        <?php if (! empty($p['imagen_url'])): ?>
          <img class="lazy" data-src="/<?= esc($p['imagen_url']) ?>" alt="<?= esc($p['titulo']) ?>" style="width:100%;border-radius:var(--radio);margin-bottom:.7em;">
        <?php endif; ?>
        <span class="sello sello--promocion">vigente hasta <?= esc($p['fecha_fin']) ?></span>
        <h3><?= esc($p['titulo']) ?></h3>
        <p style="font-size:.9rem;color:var(--gris-texto);"><?= esc($p['descripcion']) ?></p>
      </div>
    <?php endforeach; ?>
    <?php if (empty($promociones)): ?>
      <p>No hay promociones vigentes en este momento.</p>
    <?php endif; ?>
  </div>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/public_layout', ['titulo' => 'Promociones', 'contenido' => $contenido]) ?>
