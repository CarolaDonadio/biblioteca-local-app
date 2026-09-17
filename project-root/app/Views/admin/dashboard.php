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

<section class="tarjeta panel-inventario">
  <div class="panel-inventario__header">
    <div>
      <h2>Inventario por estado</h2>
      <p>Distribución del stock actual</p>
    </div>
    <?php $totalEjemplares = array_sum(array_map(fn($fila) => (int) ($fila['cantidad'] ?? 0), $ejemplares_por_estado ?? [])); ?>
    <div class="panel-inventario__total">
      <strong><?= $totalEjemplares ?></strong>
      <span>total</span>
    </div>
  </div>

  <?php if (!empty($ejemplares_por_estado)): ?>
    <?php foreach ($ejemplares_por_estado as $fila): ?>
      <?php $cantidad = (int) ($fila['cantidad'] ?? 0); ?>
      <?php $porcentaje = $totalEjemplares > 0 ? round(($cantidad / $totalEjemplares) * 100) : 0; ?>
      <div class="inventario-item">
        <div class="inventario-item__meta">
          <span class="sello sello--<?= esc($fila['estado']) ?>"><?= esc(ucfirst($fila['estado'])) ?></span>
          <strong><?= $cantidad ?></strong>
        </div>
        <div class="inventario-item__barra" aria-label="<?= esc($fila['estado']) ?>: <?= $cantidad ?> ejemplares">
          <span style="width: <?= $porcentaje ?>%;"></span>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p style="color:var(--gris-texto); margin:0;">Todavía no hay ejemplares registrados.</p>
  <?php endif; ?>
</section>

<section class="tarjeta" style="padding:1.4em 1.6em;max-width:760px;">
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