<?php ob_start(); ?>

<div class="toolbar">
  <span style="color:var(--gris-texto);font-size:.85rem;"><?= count($vencidos) ?> préstamo(s) vencido(s)</span>
  <a href="/admin/prestamos/nuevo" class="btn">+ Registrar préstamo</a>
</div>

<div class="tarjeta">
  <table>
    <thead><tr><th>Socio</th><th>Libro</th><th>Ejemplar</th><th>Vence</th><th>Estado</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($prestamos as $p): $vencido = $p['fecha_vencimiento'] < date('Y-m-d'); ?>
        <tr>
          <td><?= esc($p['apellido']) ?>, <?= esc($p['nombre']) ?></td>
          <td><?= esc($p['titulo']) ?></td>
          <td class="codigo"><?= esc($p['codigo_inventario']) ?></td>
          <td><?= esc($p['fecha_vencimiento']) ?></td>
          <td><span class="sello sello--<?= $vencido ? 'vencido' : 'disponible' ?>"><?= $vencido ? 'vencido' : 'en curso' ?></span></td>
          <td style="text-align:right;white-space:nowrap;">
            <form action="/admin/prestamos/<?= $p['id'] ?>/renovar" method="post" style="display:inline;">
              <?= csrf_field() ?>
              <button class="btn btn--outline btn--chico" type="submit">Renovar</button>
            </form>
            <form action="/admin/prestamos/<?= $p['id'] ?>/devolver" method="post" style="display:inline;" data-confirmar="¿Registrar la devolución de este préstamo?">
              <?= csrf_field() ?>
              <button class="btn btn--chico" type="submit">Devolver</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($prestamos)): ?>
        <tr><td colspan="6">No hay préstamos activos.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => 'Préstamos y devoluciones', 'contenido' => $contenido]) ?>
