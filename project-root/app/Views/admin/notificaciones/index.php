<?php ob_start(); ?>

<div class="toolbar">
  <span></span>
  <a href="/admin/notificaciones/configuracion" class="btn btn--outline">Configuración de canales</a>
</div>

<div class="tarjeta">
  <table>
    <thead><tr><th>Socio</th><th>Canal</th><th>Tipo</th><th>Mensaje</th><th>Estado</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($notificaciones as $n): ?>
        <tr>
          <td><?= esc($n['apellido']) ?>, <?= esc($n['nombre']) ?></td>
          <td class="mono"><?= esc($n['canal']) ?></td>
          <td><?= esc($n['tipo']) ?></td>
          <td style="max-width:280px;"><?= esc($n['mensaje']) ?></td>
          <td><span class="sello sello--<?= $n['estado_entrega'] === 'enviado' ? 'disponible' : 'fallido' ?>"><?= esc($n['estado_entrega']) ?></span></td>
          <td>
            <?php if ($n['estado_entrega'] !== 'enviado'): ?>
              <form action="/admin/notificaciones/reenviar/<?= $n['id'] ?>" method="post">
                <?= csrf_field() ?>
                <button class="btn btn--outline btn--chico" type="submit">Reenviar</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($notificaciones)): ?>
        <tr><td colspan="6">Todavía no se enviaron notificaciones.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => 'Notificaciones', 'contenido' => $contenido]) ?>
