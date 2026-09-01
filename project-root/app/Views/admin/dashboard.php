<?php ob_start(); ?>

<div class="kpi-grid">
  <div class="tarjeta kpi">
    <div class="kpi__valor"><?= (int) $prestamos_activos ?></div>
    <div class="kpi__etiqueta">Préstamos activos</div>
  </div>
  <div class="tarjeta kpi">
    <div class="kpi__valor" style="color:var(--sello-rojo)"><?= (int) $prestamos_vencidos ?></div>
    <div class="kpi__etiqueta">Préstamos vencidos</div>
  </div>
  <div class="tarjeta kpi">
    <div class="kpi__valor" style="color:var(--oro-cinta)"><?= (int) $reservas_pendientes ?></div>
    <div class="kpi__etiqueta">Reservas en cola</div>
  </div>
  <div class="tarjeta kpi">
    <div class="kpi__valor"><?= (int) $socios_activos ?></div>
    <div class="kpi__etiqueta">Socios activos</div>
  </div>
  <div class="tarjeta kpi">
    <div class="kpi__valor" style="color:<?= $notificaciones_pendientes > 0 ? 'var(--sello-rojo)' : 'var(--verde-tejo)' ?>"><?= (int) $notificaciones_pendientes ?></div>
    <div class="kpi__etiqueta">Notificaciones pendientes</div>
  </div>
</div>

<div class="tarjeta" style="padding:1.3em 1.5em;">
  <h3>Inventario por estado</h3>
  <table>
    <thead><tr><th>Estado</th><th>Cantidad</th></tr></thead>
    <tbody>
      <?php foreach ($ejemplares_por_estado as $fila): ?>
        <tr>
          <td><span class="sello sello--<?= esc($fila['estado']) ?>"><?= esc($fila['estado']) ?></span></td>
          <td><?= (int) $fila['cantidad'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => 'Dashboard', 'contenido' => $contenido]) ?>
