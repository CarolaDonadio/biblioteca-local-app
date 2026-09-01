<div class="catalogo-grid">
  <?php foreach ($libros as $libro): ?>
    <a href="/catalogo/libro/<?= $libro['id'] ?>" class="ficha-libro">
      <?php if (! empty($libro['portada_url'])): ?>
        <img class="lazy ficha-libro__portada" data-src="/<?= esc($libro['portada_url']) ?>" alt="Portada de <?= esc($libro['titulo']) ?>">
      <?php else: ?>
        <div class="ficha-libro__portada">sin portada</div>
      <?php endif; ?>
      <div class="ficha-libro__cuerpo">
        <div class="ficha-libro__titulo"><?= esc($libro['titulo']) ?></div>
        <div class="ficha-libro__autor"><?= esc($libro['autor']) ?></div>
      </div>
    </a>
  <?php endforeach; ?>
  <?php if (empty($libros)): ?>
    <p>No se encontraron resultados para tu búsqueda.</p>
  <?php endif; ?>
</div>
