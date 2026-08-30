<?php ob_start(); ?>

<div class="toolbar">
  <a href="/admin/inventario/reportes" class="btn btn--outline">Ver reportes</a>
  <a href="/admin/ejemplares/new" class="btn">+ Nuevo ejemplar</a>
</div>

<div class="tarjeta">
  <table>
    <thead><tr><th>Código</th><th>Libro</th><th>Ubicación</th><th>Estado</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($ejemplares as $e): ?>
        <tr>
          <td class="codigo"><?= esc($e['codigo_inventario']) ?></td>
          <td><?= esc($e['titulo']) ?></td>
          <td><?= esc($e['ubicacion'] ?? '—') ?></td>
          <td><span class="sello sello--<?= esc($e['estado']) ?>"><?= esc($e['estado']) ?></span></td>
          <td style="text-align:right;white-space:nowrap;">
            <a href="/admin/ejemplares/<?= $e['id'] ?>/edit" class="btn btn--outline btn--chico">Editar</a>

            <form action="/admin/ejemplares/<?= $e['id'] ?>/marcar-perdido" method="post" style="display:inline;" data-confirmar="¿Marcar este ejemplar como perdido?">
              <?= csrf_field() ?>
              <button class="btn btn--peligro btn--chico" type="submit">Perdido</button>
            </form>
            <form action="/admin/ejemplares/<?= $e['id'] ?>/marcar-danado" method="post" style="display:inline;" data-confirmar="¿Marcar este ejemplar como dañado?">
              <?= csrf_field() ?>
              <button class="btn btn--peligro btn--chico" type="submit">Dañado</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($ejemplares)): ?>
        <tr><td colspan="5">No hay ejemplares cargados todavía.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => 'Inventario de ejemplares', 'contenido' => $contenido]) ?>
