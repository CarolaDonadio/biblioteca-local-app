<?php ob_start(); ?>

<div class="pub-contenido">
  <div style="margin-bottom:1.5em;">
    <a href="/socio/panel" style="text-decoration:none;color:var(--azul-primario, #2563eb);font-weight:600;">← Volver a mi cuenta</a>
  </div>

  <h1>Mis préstamos activos e historial</h1>

  <div class="tarjeta" style="padding:1.6em 1.8em;margin-top:1em;">
    <h3>Libros en tu posesión</h3>

    <?php if (!empty($prestamos) && is_array($prestamos)): ?>
      <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;margin-top:1em;font-size:.95rem;">
          <thead>
            <tr style="border-bottom:2px solid #e5e7eb;text-align:left;">
              <th style="padding:.7em;">Libro</th>
              <th style="padding:.7em;">Fecha de Préstamo</th>
              <th style="padding:.7em;">Fecha de Devolución</th>
              <th style="padding:.7em;">Estado</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($prestamos as $item): ?>
              <tr style="border-bottom:1px solid #e5e7eb;">
                <td style="padding:.7em;"><strong><?= esc($item['titulo']) ?></strong></td>
                <td style="padding:.7em;"><?= esc($item['fecha_prestamo']) ?></td>
                <td style="padding:.7em;"><?= esc($item['fecha_devolucion']) ?></td>
                <td style="padding:.7em;">
                  <span class="sello" style="background:#dcfce7;color:#166534;padding:.2em .6em;border-radius:4px;">En término</span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <!-- Estado de visualización preliminar si la BD no devuelve préstamos todavía -->
      <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;margin-top:1em;font-size:.95rem;">
          <thead>
            <tr style="border-bottom:2px solid #e5e7eb;text-align:left;">
              <th style="padding:.7em;">Libro</th>
              <th style="padding:.7em;">Fecha de Préstamo</th>
              <th style="padding:.7em;">Fecha de Vencimiento</th>
              <th style="padding:.7em;">Estado</th>
            </tr>
          </thead>
          <tbody>
            <tr style="border-bottom:1px solid #e5e7eb;">
              <td style="padding:.7em;"><strong>El Principito</strong></td>
              <td style="padding:.7em;">01/09/2026</td>
              <td style="padding:.7em;">15/09/2026</td>
              <td style="padding:.7em;">
                <span class="sello" style="background:#dcfce7;color:#166534;padding:.2em .6em;border-radius:4px;font-size:.8rem;">En término</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/public_layout', ['titulo' => 'Mis préstamos', 'contenido' => $contenido]) ?>