<?php ob_start(); ?>

<div class="toolbar">
  <span></span>
  <a href="/admin/socios/new" class="btn">+ Nuevo socio</a>
</div>

<div class="tarjeta">
  <table>
    <thead><tr><th>DNI</th><th>Nombre</th><th>Email</th><th>Estado</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($socios as $s): ?>
        <tr>
          <td class="mono"><?= esc($s['dni']) ?></td>
          <td><?= esc($s['apellido']) ?>, <?= esc($s['nombre']) ?></td>
          <td><?= esc($s['email']) ?></td>
          <td><span class="sello sello--<?= $s['estado'] === 'activo' ? 'disponible' : 'vencido' ?>"><?= esc($s['estado']) ?></span></td>
          <td style="text-align:right;white-space:nowrap;">
            <a href="/admin/socios/<?= $s['id'] ?>/historial" class="btn btn--outline btn--chico">Historial</a>
            <a href="/admin/socios/<?= $s['id'] ?>/edit" class="btn btn--outline btn--chico">Editar</a>
            <form action="/admin/socios/<?= $s['id'] ?>/delete" method="post" style="display:inline;" data-confirmar="¿Eliminar este socio?">
              <?= csrf_field() ?>
              <input type="hidden" name="_method" value="DELETE">
              <button class="btn btn--peligro btn--chico" type="submit">Eliminar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($socios)): ?>
        <tr><td colspan="5">No hay socios registrados todavía.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => 'Socios', 'contenido' => $contenido]) ?>
