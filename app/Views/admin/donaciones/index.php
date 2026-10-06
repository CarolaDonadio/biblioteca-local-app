<?php ob_start(); ?>

<div class="toolbar">
  <span style="color:var(--gris-texto);font-size:.85rem;"><?= count($donaciones ?? []) ?> donación(es) registrada(s)</span>
  <a href="/admin/donaciones/new" class="btn">+ Registrar donación</a>
</div>

<div class="tarjeta">
  <table>
    <thead><tr><th>Donante</th><th>Tipo</th><th>Descripción</th><th>Cantidad</th><th>Fecha</th><th>Estado</th><th></th></tr></thead>
    <tbody>
      <?php foreach (($donaciones ?? []) as $donacion): ?>
        <tr>
          <td><?= esc($donacion['donante']) ?></td>
          <td><?= esc(ucfirst($donacion['tipo'])) ?></td>
          <td><?= esc($donacion['descripcion']) ?></td>
          <td><?= (int) $donacion['cantidad'] ?></td>
          <td><?= esc(date('d/m/Y', strtotime($donacion['fecha_donacion']))) ?></td>
          <td><span class="sello sello--<?= $donacion['estado'] === 'recibida' ? 'disponible' : ($donacion['estado'] === 'rechazada' ? 'vencido' : 'pendiente') ?>"><?= esc(ucfirst($donacion['estado'])) ?></span></td>
          <td style="text-align:right;white-space:nowrap;">
            <a href="/admin/donaciones/<?= $donacion['id'] ?>/edit" class="btn btn--outline btn--chico">Editar</a>
            <form action="/admin/donaciones/<?= $donacion['id'] ?>" method="post" style="display:inline;" data-confirmar="¿Eliminar esta donación?">
              <?= csrf_field() ?><input type="hidden" name="_method" value="DELETE">
              <button class="btn btn--peligro btn--chico" type="submit">Eliminar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($donaciones)): ?>
        <tr><td colspan="7" style="text-align:center;padding:1.5em;">No hay donaciones registradas.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => 'Donaciones', 'contenido' => $contenido]) ?>