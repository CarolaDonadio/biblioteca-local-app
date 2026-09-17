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

<section class="tarjeta inventario-panel">
  <div class="inventario-panel__header">
    <div>
      <h2>Inventario por estado</h2>
      <p>Resumen del stock actual</p>
    </div>
    <?php $totalEjemplares = array_sum(array_map(fn($fila) => (int) ($fila['cantidad'] ?? 0), $ejemplares_por_estado ?? [])); ?>
    <div class="inventario-panel__total">
      <span>Total</span>
      <strong><?= $totalEjemplares ?></strong>
    </div>
  </div>

  <?php if (!empty($ejemplares_por_estado)): ?>
    <div class="inventario-panel__lista">
      <?php foreach ($ejemplares_por_estado as $fila): ?>
        <?php $cantidad = (int) ($fila['cantidad'] ?? 0); ?>
        <?php $porcentaje = $totalEjemplares > 0 ? round(($cantidad / $totalEjemplares) * 100) : 0; ?>
        <div class="inventario-panel__item">
          <div class="inventario-panel__meta">
            <span class="sello sello--<?= esc($fila['estado']) ?>"><?= esc($fila['estado']) ?></span>
            <strong><?= $cantidad ?></strong>
          </div>
          <div class="inventario-panel__barra" aria-label="<?= esc($fila['estado']) ?>: <?= $cantidad ?> ejemplares">
            <span style="width: <?= $porcentaje ?>%"></span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p style="color:var(--gris-texto); margin:0;">Todavía no hay ejemplares registrados.</p>
  <?php endif; ?>
</section>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => 'Dashboard', 'contenido' => $contenido]) ?>