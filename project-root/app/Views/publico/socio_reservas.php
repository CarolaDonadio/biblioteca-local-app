<?php ob_start(); ?>

<div class="pub-contenido">
  <div style="margin-bottom:1.5em;">
    <a href="/socio/panel" style="text-decoration:none;color:var(--azul-primario, #2563eb);font-weight:600;">← Volver a mi cuenta</a>
  </div>

  <h1>Mis reservas</h1>

  <div class="tarjeta" style="padding:1.6em 1.8em;margin-top:1em;">
    <?php if (!empty($reservas) && is_array($reservas)): ?>
      <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;margin-top:1em;font-size:.95rem;">
          <thead>
            <tr style="border-bottom:2px solid #e5e7eb;text-align:left;">
              <th style="padding:.7em;">Libro</th>
              <th style="padding:.7em;">Autor</th>
              <th style="padding:.7em;">Fecha de solicitud</th>
              <th style="padding:.7em;">Estado</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reservas as $reserva): ?>
              <tr style="border-bottom:1px solid #e5e7eb;">
                <td style="padding:.7em;"><strong><?= esc($reserva['titulo'] ?? 'Libro no encontrado') ?></strong></td>
                <td style="padding:.7em;"><?= esc($reserva['autor'] ?? 'Sin autor') ?></td>
                <td style="padding:.7em;"><?= esc($reserva['fecha_solicitud'] ?? '') ?></td>
                <td style="padding:.7em;">
                  <span class="sello sello--<?= $reserva['estado'] === 'confirmada' ? 'reservado' : ($reserva['estado'] === 'cancelada' ? 'vencido' : 'pendiente') ?>"><?= esc(ucfirst($reserva['estado'])) ?></span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p style="color:var(--gris-texto);margin:0;">Todavía no registraste reservas.</p>
    <?php endif; ?>
  </div>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/public_layout', ['titulo' => 'Mis reservas', 'contenido' => $contenido]) ?>
