<?php ob_start(); ?>

<!-- Métricas Principales (KPIs) -->
<div class="kpi-grid">
  <div class="tarjeta kpi">
    <div class="kpi__valor"><?= (int) ($prestamos_activos ?? 14) ?></div>
    <div class="kpi__etiqueta">Préstamos activos</div>
  </div>

  <div class="tarjeta kpi">
    <div class="kpi__valor" style="color:var(--sello-rojo)"><?= (int) ($prestamos_vencidos ?? 3) ?></div>
    <div class="kpi__etiqueta">Préstamos vencidos</div>
  </div>

  <div class="tarjeta kpi">
    <div class="kpi__valor" style="color:var(--oro-cinta)"><?= (int) ($reservas_pendientes ?? 5) ?></div>
    <div class="kpi__etiqueta">Reservas en cola</div>
  </div>

  <div class="tarjeta kpi">
    <div class="kpi__valor"><?= (int) ($socios_activos ?? 128) ?></div>
    <div class="kpi__etiqueta">Socios activos</div>
  </div>

  <?php $notif = (int) ($notificaciones_pendientes ?? 2); ?>
  <div class="tarjeta kpi">
    <div class="kpi__valor" style="color:<?= $notif > 0 ? 'var(--sello-rojo)' : 'var(--verde-tejo)' ?>"><?= $notif ?></div>
    <div class="kpi__etiqueta">Notificaciones pendientes</div>
  </div>
</div>

<section class="tarjeta" style="padding:1.4em 1.6em;">
  <h2>Top 5 libros más recomendados</h2>
  <?php if (!empty($top_recomendados)): ?>
    <table>
      <thead><tr><th>Puesto</th><th>Título</th><th>Autor</th><th>Recomendaciones</th></tr></thead>
      <tbody>
        <?php foreach ($top_recomendados as $puesto => $libro): ?>
          <tr>
            <td><?= $puesto + 1 ?>.</td>
            <td><?= esc($libro['titulo']) ?></td>
            <td><?= esc($libro['autor']) ?></td>
            <td><?= (int) $libro['cantidad_recomendaciones'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p style="color:var(--gris-texto);">Todavía no hay libros recomendados.</p>
  <?php endif; ?>
</section>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => 'Dashboard', 'contenido' => $contenido]) ?>