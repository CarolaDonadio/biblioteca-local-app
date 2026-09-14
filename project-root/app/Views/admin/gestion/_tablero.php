<section class="gestion-seccion">
  <div class="gestion-seccion__encabezado">
    <div>
      <h2>Solicitudes de libros</h2>
      <p>Aprueba, rechaza o finaliza el retiro de cada reserva.</p>
    </div>
    <span class="sello sello--pendiente"><?= count($reservas ?? []) ?> pendientes</span>
  </div>

  <div class="tarjeta gestion-tabla">
    <table>
      <thead><tr><th>Libro</th><th>Socio</th><th>Solicitado</th><th>Estado</th><th></th></tr></thead>
      <tbody>
        <?php foreach (($reservas ?? []) as $reserva): ?>
          <tr>
            <td><?= esc($reserva['titulo'] ?? 'Libro no encontrado') ?></td>
            <td><?= esc($reserva['socio_nombre'] ?? 'Socio no encontrado') ?></td>
            <td><?= esc($reserva['fecha_reserva'] ?? '') ?></td>
            <td><span class="sello sello--<?= $reserva['estado'] === 'confirmada' ? 'reservado' : 'pendiente' ?>"><?= esc(ucfirst($reserva['estado'])) ?></span></td>
            <td class="gestion-acciones">
              <?php if ($reserva['estado'] === 'pendiente'): ?>
                <form action="/admin/reservas/<?= $reserva['id'] ?>/confirmar" method="post">
                  <?= csrf_field() ?><button class="btn btn--outline btn--chico" type="submit">Aprobar</button>
                </form>
              <?php elseif ($reserva['estado'] === 'confirmada'): ?>
                <form action="/admin/reservas/<?= $reserva['id'] ?>/completar" method="post" data-confirmar="¿Finalizar el retiro de este libro?">
                  <?= csrf_field() ?><button class="btn btn--chico" type="submit">Finalizar retiro</button>
                </form>
              <?php endif; ?>
              <?php if (in_array($reserva['estado'], ['pendiente', 'confirmada'], true)): ?>
                <form action="/admin/reservas/<?= $reserva['id'] ?>/cancelar" method="post" data-confirmar="¿Rechazar esta solicitud?">
                  <?= csrf_field() ?><button class="btn btn--peligro btn--chico" type="submit">Rechazar</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($reservas)): ?>
          <tr><td colspan="5" class="gestion-vacio">No hay solicitudes pendientes.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>

<section class="gestion-seccion">
  <div class="gestion-seccion__encabezado">
    <div>
      <h2>Préstamos en curso</h2>
      <p>Registra la devolución para liberar el ejemplar.</p>
    </div>
    <span class="sello sello--prestado"><?= count($prestamos ?? []) ?> activos</span>
  </div>

  <div class="tarjeta gestion-tabla">
    <table>
      <thead><tr><th>Socio</th><th>Libro</th><th>Vence</th><th>Estado</th><th></th></tr></thead>
      <tbody>
        <?php foreach (($prestamos ?? []) as $prestamo): $vencido = $prestamo['fechaVence'] < date('Y-m-d'); ?>
          <tr>
            <td><?= esc($prestamo['socio_nombre'] ?? 'Socio eliminado') ?></td>
            <td><?= esc($prestamo['titulo'] ?? 'Libro eliminado') ?></td>
            <td><?= esc(date('d/m/Y', strtotime($prestamo['fechaVence']))) ?></td>
            <td><span class="sello sello--<?= $vencido ? 'vencido' : 'prestado' ?>"><?= $vencido ? 'Vencido' : 'En curso' ?></span></td>
            <td class="gestion-acciones">
              <form action="/admin/prestamos/<?= $prestamo['id'] ?>/devolver" method="post" data-confirmar="¿Finalizar y registrar la devolución?">
                <?= csrf_field() ?><button class="btn btn--chico" type="submit">Finalizar devolución</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($prestamos)): ?>
          <tr><td colspan="5" class="gestion-vacio">No hay préstamos activos.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>