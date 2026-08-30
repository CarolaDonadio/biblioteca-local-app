<?php ob_start(); ?>

<div class="toolbar">
  <span></span>
  <a href="/admin/promociones/new" class="btn">+ Nueva promoción</a>
</div>

<div class="tarjeta">
  <table>
    <thead><tr><th>Título</th><th>Vigencia</th><th>Activa</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($promociones as $p): ?>
        <tr>
          <td><?= esc($p['titulo']) ?></td>
          <td><?= esc($p['fecha_inicio']) ?> → <?= esc($p['fecha_fin']) ?></td>
          <td><span class="sello sello--<?= $p['activo'] ? 'disponible' : 'vencido' ?>"><?= $p['activo'] ? 'sí' : 'no' ?></span></td>
          <td style="text-align:right;white-space:nowrap;">
            <a href="/admin/promociones/<?= $p['id'] ?>/edit" class="btn btn--outline btn--chico">Editar</a>
            <form action="/admin/promociones/<?= $p['id'] ?>/delete" method="post" style="display:inline;" data-confirmar="¿Eliminar esta promoción?">
              <?= csrf_field() ?>
              <input type="hidden" name="_method" value="DELETE">
              <button class="btn btn--peligro btn--chico" type="submit">Eliminar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($promociones)): ?>
        <tr><td colspan="4">No hay promociones cargadas.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => 'Promociones', 'contenido' => $contenido]) ?>
