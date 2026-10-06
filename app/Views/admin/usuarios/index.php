<?php ob_start(); ?>

<div class="toolbar">
  <span></span>
  <a href="/admin/usuarios/new" class="btn">+ Nuevo usuario</a>
</div>

<div class="tarjeta">
  <table>
    <thead><tr><th>DNI</th><th>Nombre</th><th>Email</th><th>Estado</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($usuarios as $u): ?>
        <tr>
          <td class="mono"><?= esc($u['dni']) ?></td>
          <td><?= esc($u['nombre_completo']) ?></td>
          <td><?= esc($u['mail']) ?></td>
          <td><span class="sello sello--<?= $u['estado'] === 'activo' ? 'disponible' : 'vencido' ?>"><?= esc($u['estado']) ?></span></td>
          <td style="text-align:right;white-space:nowrap;">
            <a href="/admin/usuarios/<?= $u['dni'] ?>/edit" class="btn btn--outline btn--chico">Editar</a>
            <form action="/admin/usuarios/<?= $u['dni'] ?>/delete" method="post" style="display:inline;" data-confirmar="¿Eliminar este usuario?">
              <?= csrf_field() ?>
              <input type="hidden" name="_method" value="DELETE">
              <button class="btn btn--peligro btn--chico" type="submit">Eliminar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($usuarios)): ?>
        <tr><td colspan="5">No hay usuarios administrativos registrados todavía.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => 'Usuarios administrativos', 'contenido' => $contenido]) ?>
