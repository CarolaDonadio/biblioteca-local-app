<?php ob_start(); ?>

<div class="pub-contenido">
  <h1>Mis préstamos</h1>
  <div class="tarjeta">
    <table>
      <thead><tr><th>Libro</th><th>Ejemplar</th><th>Prestado</th><th>Vence</th><th>Estado</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($prestamos as $p): ?>
          <tr>
            <td><?= esc($p['titulo']) ?></td>
            <td class="codigo"><?= esc($p['codigo_inventario']) ?></td>
            <td><?= esc($p['fecha_prestamo']) ?></td>
            <td><?= esc($p['fecha_vencimiento']) ?></td>
            <td><span class="sello sello--<?= esc($p['estado']) ?>"><?= esc($p['estado']) ?></span></td>
            <td>
              <?php if ($p['estado'] === 'activo'): ?>
                <form action="/socio/panel/renovar/<?= $p['id'] ?>" method="post">
                  <?= csrf_field() ?>
                  <button class="btn btn--outline btn--chico" type="submit">Renovar</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($prestamos)): ?>
          <tr><td colspan="6">Todavía no tenés préstamos registrados.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/public_layout', ['titulo' => 'Mis préstamos', 'contenido' => $contenido]) ?>
