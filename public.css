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
            <span class="sello sello--<?= $r['estado'] === 'disponible_para_retiro' ? 'reservado' : 'pendiente' ?>">
              <?= $r['estado'] === 'disponible_para_retiro' ? 'listo para retirar' : 'en cola' ?>
            </span>
          </td>
          <td style="text-align:right;white-space:nowrap;">
            <?php if ($r['estado'] === 'pendiente'): ?>
              <form action="/admin/reservas/<?= $r['id'] ?>/confirmar" method="post" style="display:inline;">
                <?= csrf_field() ?>
                <button class="btn btn--outline btn--chico" type="submit">Confirmar</button>
              </form>
            <?php else: ?>
              <form action="/admin/reservas/<?= $r['id'] ?>/completar" method="post" style="display:inline;" data-confirmar="¿El socio retira el ejemplar ahora? Se registrará el préstamo.">
                <?= csrf_field() ?>
                <button class="btn btn--chico" type="submit">Marcar retirado</button>
              </form>
            <?php endif; ?>
            <form action="/admin/reservas/<?= $r['id'] ?>/cancelar" method="post" style="display:inline;" data-confirmar="¿Cancelar esta reserva?">
              <?= csrf_field() ?>
              <button class="btn btn--peligro btn--chico" type="submit">Cancelar</button>
            </form>
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
