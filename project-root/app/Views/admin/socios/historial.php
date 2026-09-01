<?php ob_start(); ?>

<p style="color:var(--gris-texto);margin-top:-1em;"><?= esc($socio['apellido']) ?>, <?= esc($socio['nombre']) ?> — <?= esc($socio['email']) ?></p>

<div class="kpi-grid" style="grid-template-columns:repeat(2,minmax(160px,1fr));max-width:420px;">
  <div class="tarjeta kpi">
    <div class="kpi__valor"><?= (int) $historial['total_prestamos'] ?></div>
    <div class="kpi__etiqueta">Préstamos totales</div>
  </div>
  <div class="tarjeta kpi">
    <div class="kpi__valor" style="color:var(--sello-rojo)"><?= (int) $historial['vencidos'] ?></div>
    <div class="kpi__etiqueta">Vencidos (sanciones)</div>
  </div>
</div>

<div class="tarjeta">
  <table>
    <thead><tr><th>Libro</th><th>Ejemplar</th><th>Prestado</th><th>Vence</th><th>Devuelto</th><th>Estado</th></tr></thead>
    <tbody>
      <?php foreach ($historial['prestamos'] as $p): ?>
        <tr>
          <td><?= esc($p['titulo']) ?></td>
          <td class="codigo"><?= esc($p['codigo_inventario']) ?></td>
          <td><?= esc($p['fecha_prestamo']) ?></td>
          <td><?= esc($p['fecha_vencimiento']) ?></td>
          <td><?= esc($p['fecha_devolucion'] ?? '—') ?></td>
          <td><span class="sello sello--<?= esc($p['estado']) ?>"><?= esc($p['estado']) ?></span></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($historial['prestamos'])): ?>
        <tr><td colspan="6">Este socio todavía no registra préstamos.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => 'Historial del socio', 'contenido' => $contenido]) ?>
