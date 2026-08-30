<?php ob_start(); ?>

<div class="toolbar">
  <form action="/admin/libros" method="get">
    <input type="text" name="q" placeholder="Buscar por título, autor o ISBN..." value="<?= esc($_GET['q'] ?? '') ?>">
    <button class="btn btn--outline" type="submit">Buscar</button>
  </form>
  <a href="/admin/libros/new" class="btn">+ Nuevo libro</a>
</div>

<div class="tarjeta">
  <table>
    <thead>
      <tr><th>ISBN</th><th>Título</th><th>Autor</th><th>Categoría</th><th></th></tr>
    </thead>
    <tbody>
      <?php foreach ($libros as $libro): ?>
        <tr>
          <td class="isbn"><?= esc($libro['isbn']) ?></td>
          <td><?= esc($libro['titulo']) ?></td>
          <td><?= esc($libro['autor']) ?></td>
          <td><?= esc($libro['categoria'] ?? '—') ?></td>
          <td style="text-align:right;white-space:nowrap;">
            <a href="/admin/libros/<?= $libro['id'] ?>/multimedia" class="btn btn--outline btn--chico">Multimedia</a>
            <a href="/admin/libros/<?= $libro['id'] ?>/edit" class="btn btn--outline btn--chico">Editar</a>
            <form action="/admin/libros/<?= $libro['id'] ?>/delete" method="post" style="display:inline;" data-confirmar="¿Eliminar este libro y sus ejemplares?">
              <?= csrf_field() ?>
              <input type="hidden" name="_method" value="DELETE">
              <button class="btn btn--peligro btn--chico" type="submit">Eliminar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($libros)): ?>
        <tr><td colspan="5">No se encontraron libros.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => 'Gestión del catálogo', 'contenido' => $contenido]) ?>
