<?php ob_start(); ?>

<?php
  $lista_prestamos = $prestamos ?? [];
  $vencidos_lista  = $vencidos ?? [];
  $hoy             = date('Y-m-d');
?>

<div class="toolbar">
  <span style="color:var(--gris-texto);font-size:.85rem;"><?= count($vencidos_lista) ?> préstamo(s) vencido(s)</span>
  <a href="/admin/prestamos/nuevo" class="btn">+ Registrar préstamo</a>
</div>

<div class="tarjeta">
  <table>
    <thead>
      <tr>
        <th>Socio</th>
        <th>Libro</th>
        <th>ISBN</th>
        <th>Retiro</th>
        <th>Vence</th>
        <th>Estado</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($lista_prestamos as $p): $vencido = $p['fechaVence'] < $hoy; ?>
        <tr>
          <td><?= esc($p['socio_nombre'] ?? 'Socio eliminado') ?> <small style="color:var(--gris-texto);">(<?= esc($p['dniUsuario']) ?>)</small></td>
          <td><?= esc($p['titulo'] ?? 'Libro eliminado') ?></td>
          <td class="codigo"><?= esc($p['isbn'] ?? '—') ?></td>
          <td><?= esc(date('d/m/Y', strtotime($p['fechaPrestamo']))) ?></td>
          <td><?= esc(date('d/m/Y', strtotime($p['fechaVence']))) ?></td>
          <td>
            <span class="sello sello--<?= $vencido ? 'vencido' : 'disponible' ?>">
              <?= $vencido ? 'vencido' : 'en curso' ?>
            </span>
          </td>
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

      <?php if (empty($lista_prestamos)): ?>
        <tr>
          <td colspan="7" style="text-align:center;padding:1.5em;">No hay préstamos activos.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => 'Préstamos y devoluciones', 'contenido' => $contenido]) ?>