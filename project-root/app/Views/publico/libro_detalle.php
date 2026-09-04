<?php ob_start(); ?>

<div class="pub-contenido">
  <div style="margin-bottom:1.5em;">
    <a href="/catalogo" style="text-decoration:none;color:var(--azul-primario, #2563eb);font-weight:600;">← Volver al catálogo</a>
  </div>

  <div class="tarjeta" style="padding:2em;display:grid;grid-template-columns:repeat(auto-fit, minmax(260px, 1fr));gap:2em;">
    
    <!-- Imagen / Portada -->
    <div style="text-align:center;background:#f3f4f6;border-radius:var(--radio, 8px);padding:2em;display:flex;align-items:center;justify-content:center;min-height:300px;">
      <?php if (!empty($libro['imagen_url'])): ?>
        <img class="lazy" data-src="/<?= esc($libro['imagen_url']) ?>" alt="<?= esc($libro['titulo'] ?? 'Libro') ?>" style="max-width:100%;max-height:360px;object-fit:contain;border-radius:var(--radio);">
      <?php else: ?>
        <span style="font-size:4.5rem;" role="img" aria-label="Libro">📖</span>
      <?php endif; ?>
    </div>

    <!-- Información bibliográfica -->
    <div style="display:flex;flex-direction:column;justify-content:space-between;">
      <div>
        <span class="sello sello--promocion"><?= esc($libro['categoria'] ?? 'General') ?></span>

        <h1 style="margin:.4em 0 .2em 0;"><?= esc($libro['titulo'] ?? 'El Principito') ?></h1>
        
        <p style="color:var(--gris-texto);font-size:1.1rem;margin-bottom:1.5em;">
          Autor: <strong><?= esc($libro['autor'] ?? 'Antoine de Saint-Exupéry') ?></strong>
        </p>

        <!-- Ficha de Datos -->
        <div class="tarjeta" style="padding:1em;background:#fafafa;margin-bottom:1.5em;display:grid;grid-template-columns:repeat(auto-fit, minmax(120px, 1fr));gap:1em;font-size:.9rem;">
          <div>
            <span style="color:var(--gris-texto);display:block;">ISBN</span>
            <strong><?= esc($libro['isbn'] ?? '978-9875666870') ?></strong>
          </div>
          <div>
            <span style="color:var(--gris-texto);display:block;">Editorial</span>
            <strong><?= esc($libro['editorial'] ?? 'Salamandra') ?></strong>
          </div>
          <div>
            <span style="color:var(--gris-texto);display:block;">Año</span>
            <strong><?= esc($libro['anio'] ?? '1943') ?></strong>
          </div>
          <div>
            <span style="color:var(--gris-texto);display:block;">Disponibilidad</span>
            <?php if (!isset($libro['disponible']) || $libro['disponible']): ?>
              <span style="color:#166534;font-weight:bold;">Disponible</span>
            <?php else: ?>
              <span style="color:#991b1b;font-weight:bold;">Prestado</span>
            <?php endif; ?>
          </div>
        </div>

        <div style="margin-bottom:1.5em;">
          <h3>Sinopsis</h3>
          <p style="color:var(--gris-texto);line-height:1.6;font-size:.95rem;">
            <?= esc($libro['sinopsis'] ?? 'Un pequeño príncipe viaja por el universo descubriendo la extraña forma en que los adultos ven la vida y la importancia de lo esencial.') ?>
          </p>
        </div>
      </div>

      <!-- Botón de Acción -->
      <div>
        <?php if (session('socio_id')): ?>
          <form action="/socio/reservar/<?= esc($libro['id'] ?? 1) ?>" method="post">
            <?= csrf_field() ?>
            <button type="submit" class="btn" style="width:100%;">Reservar este libro</button>
          </form>
        <?php else: ?>
          <a href="/socio/login" class="btn" style="display:block;text-align:center;text-decoration:none;">Ingresá a tu cuenta para reservar</a>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/public_layout', ['titulo' => $libro['titulo'] ?? 'Detalle del Libro', 'contenido' => $contenido]) ?>