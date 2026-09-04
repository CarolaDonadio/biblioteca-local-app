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

<!-- Tabla de Inventario por Estado -->
<div class="tarjeta" style="padding:1.3em 1.5em;margin-top:1.5em;">
  <h3>Inventario por estado</h3>
  <table class="tabla" style="width:100%;border-collapse:collapse;margin-top:0.8em;">
    <thead>
      <tr style="border-bottom:2px solid var(--gris-borde, #e5e7eb);text-align:left;">
        <th style="padding:.6em;">Estado</th>
        <th style="padding:.6em;">Cantidad</th>
      </tr>
    </thead>
    <tbody>
      <?php 
        $estados = $ejemplares_por_estado ?? [
          ['estado' => 'disponible', 'cantidad' => 245],
          ['estado' => 'prestado', 'cantidad' => 14],
          ['estado' => 'reparacion', 'cantidad' => 3],
          ['estado' => 'extraviado', 'cantidad' => 1]
        ];
      ?>
      <?php foreach ($estados as $fila): ?>
        <tr style="border-bottom:1px solid var(--gris-borde, #e5e7eb);">
          <td style="padding:.6em;">
            <span class="sello sello--<?= esc($fila['estado']) ?>"><?= esc(ucfirst($fila['estado'])) ?></span>
          </td>
          <td style="padding:.6em;"><?= (int) $fila['cantidad'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => 'Dashboard', 'contenido' => $contenido]) ?>