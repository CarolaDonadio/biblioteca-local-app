<?php ob_start(); ?>

<p style="color:var(--gris-texto);margin-top:-1em;">Cola de reservas en tiempo real, ordenada por libro y posición.</p>

<div class="tarjeta">
  <table>
    <thead><tr><th>Libro</th><th>Socio</th><th>Posición</th><th>Reservado el</th><th>Estado</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($reservas as $r): ?>
        <tr>
          <td><?= esc($r['titulo']) ?></td>
          <td><?= esc($r['apellido']) ?>, <?= esc($r['nombre']) ?></td>
          <td>#<?= (int) $r['posicion_cola'] ?></td>
          <td><?= esc($r['fecha_reserva']) ?></td>
          <td>
            <span class="sello sello--<?= $r['estado'] === 'confirmada' ? 'reservado' : ($r['estado'] === 'cancelada' ? 'vencido' : 'pendiente') ?>">
              <?= esc(ucfirst($r['estado'])) ?>
            </span>
          </td>
          <td style="text-align:right;white-space:nowrap;">
            <?php if ($r['estado'] === 'pendiente'): ?>
              <form action="/admin/reservas/<?= $r['id'] ?>/confirmar" method="post" style="display:inline;">
                <?= csrf_field() ?>
                <button class="btn btn--outline btn--chico" type="submit">Confirmar</button>
              </form>
              <form action="/admin/reservas/<?= $r['id'] ?>/cancelar" method="post" style="display:inline;" data-confirmar="¿Cancelar esta reserva?">
                <?= csrf_field() ?>
                <button class="btn btn--peligro btn--chico" type="submit">Cancelar</button>
              </form>
            <?php elseif ($r['estado'] === 'confirmada'): ?>
              <form action="/admin/reservas/<?= $r['id'] ?>/completar" method="post" style="display:inline;" data-confirmar="¿Confirmar que el socio retiró el libro? La reserva se marcará como completada.">
                <?= csrf_field() ?>
                <button class="btn btn--chico" type="submit">Marcar retirado</button>
              </form>
              <form action="/admin/reservas/<?= $r['id'] ?>/cancelar" method="post" style="display:inline;" data-confirmar="¿Cancelar esta reserva?">
                <?= csrf_field() ?>
                <button class="btn btn--peligro btn--chico" type="submit">Cancelar</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($reservas)): ?>
        <tr><td colspan="6">No hay reservas pendientes en este momento.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => 'Motor de reservas', 'contenido' => $contenido]) ?>
