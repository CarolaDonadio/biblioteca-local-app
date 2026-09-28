<?php ob_start(); ?>

<div class="pub-contenido">
  <h1>Hola, <?= esc($socio['nombre']) ?></h1>

  <div class="tarjeta" style="padding:1.4em 1.6em;max-width:760px;margin-bottom:1.5em;">
    <h3>Avisos de tu cuenta</h3>
    <?php if (! empty($notificaciones)): ?>
      <ul style="margin-bottom:0;">
        <?php foreach ($notificaciones as $notificacion): ?>
          <li>
            <?= esc($notificacion['mensaje']) ?>
            <span style="color:var(--gris-texto);font-size:.85rem;">
              — <?= esc($notificacion['created_at']) ?>
            </span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p style="color:var(--gris-texto);margin-bottom:0;">No hay avisos recientes.</p>
    <?php endif; ?>
  </div>

  <div class="kpi-grid socio-summary-grid">
    <div class="tarjeta kpi">
      <div class="kpi__valor"><?= (int) $historial['total_prestamos'] ?></div>
      <div class="kpi__etiqueta">Préstamos totales</div>
    </div>
    <div class="tarjeta kpi">
      <div class="kpi__valor"><a href="/socio/panel/prestamos" style="text-decoration:none;color:inherit;">ver</a></div>
      <div class="kpi__etiqueta">Mis préstamos activos</div>
    </div>
    <div class="tarjeta kpi">
      <div class="kpi__valor"><a href="/socio/panel/reservas" style="text-decoration:none;color:inherit;">ver</a></div>
      <div class="kpi__etiqueta">Mis reservas</div>
    </div>
  </div>

  <div class="tarjeta" style="padding:1.4em 1.6em;max-width:760px;margin-top:1.5em;">
    <h3>Mis reservas</h3>
    <?php $misReservas = $reservas ?? []; ?>
    <?php if (! empty($misReservas)): ?>
      <div style="overflow-x:auto;">
        <table>
          <thead>
            <tr>
              <th>Libro</th>
              <th>Autor</th>
              <th>Solicitada</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($misReservas as $reserva): ?>
              <tr>
                <td><?= esc($reserva['titulo'] ?? 'Libro no encontrado') ?></td>
                <td><?= esc($reserva['autor'] ?? 'Sin autor') ?></td>
                <td><?= esc($reserva['fecha_solicitud'] ?? '') ?></td>
                <td><span class="sello sello--<?= $reserva['estado'] === 'confirmada' ? 'reservado' : ($reserva['estado'] === 'cancelada' ? 'vencido' : 'pendiente') ?>"><?= esc(ucfirst($reserva['estado'])) ?></span></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p style="color:var(--gris-texto);margin:0;">Todavía no tenés reservas activas.</p>
    <?php endif; ?>
  </div>

</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/public_layout', ['titulo' => 'Mi cuenta', 'contenido' => $contenido]) ?>
