<?php ob_start(); ?>

<div class="pub-contenido">
  <div class="detalle-libro">
    <div class="detalle-libro__portada">
      <?php if (! empty($libro['portada_url'])): ?>
        <img src="/<?= esc($libro['portada_url']) ?>" alt="Portada de <?= esc($libro['titulo']) ?>">
      <?php else: ?>
        <div class="placeholder tarjeta" style="aspect-ratio:2/3;display:flex;align-items:center;justify-content:center;color:var(--gris-texto);">sin portada</div>
      <?php endif; ?>
    </div>

    <div>
      <h1><?= esc($libro['titulo']) ?></h1>
      <p style="color:var(--gris-texto);"><?= esc($libro['autor']) ?> <?= $libro['editorial'] ? '· ' . esc($libro['editorial']) : '' ?> <?= $libro['anio'] ? '· ' . esc($libro['anio']) : '' ?></p>
      <p class="isbn" style="color:var(--gris-texto);font-size:.85rem;">ISBN <?= esc($libro['isbn']) ?></p>

      <p><span class="sello sello--<?= $disponibilidad['disponibles'] > 0 ? 'disponible' : 'pendiente' ?>">
        <?= $disponibilidad['disponibles'] ?> de <?= $disponibilidad['total'] ?> ejemplares disponibles
      </span></p>

      <?php if ($libro['sinopsis']): ?>
        <p><?= nl2br(esc($libro['sinopsis'])) ?></p>
      <?php endif; ?>

      <?php if (session('socio_id')): ?>
        <form action="/socio/panel/reservar/<?= $libro['id'] ?>" method="post" data-confirmar-reserva="¿Reservar este libro?">
          <?= csrf_field() ?>
          <button type="submit" class="btn"><?= $disponibilidad['disponibles'] > 0 ? 'Reservar ahora' : 'Sumarme a la cola de espera' ?></button>
        </form>
      <?php else: ?>
        <p><a href="/socio/login" class="btn">Iniciá sesión para reservar</a></p>
      <?php endif; ?>

      <?php if (! empty($multimedia)): ?>
        <h3 style="margin-top:1.5em;">Material digital</h3>
        <ul>
          <?php foreach ($multimedia as $m): ?>
            <li><span class="sello sello--promocion"><?= esc($m['tipo']) ?></span> <a href="/<?= esc($m['archivo_url']) ?>" target="_blank">abrir</a></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/public_layout', [
  'titulo' => $libro['titulo'],
  'contenido' => $contenido,
  'js_extra' => '<script src="/assets/js/public/reservas.js"></script>',
]) ?>
