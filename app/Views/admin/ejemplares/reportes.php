<?php ob_start(); ?>

<div class="tarjeta" style="padding:1.4em 1.6em;max-width:480px;">
  <h3>Ejemplares por estado</h3>
  <table>
    <thead><tr><th>Estado</th><th>Cantidad</th></tr></thead>
    <tbody>
      <?php $total = 0; foreach ($por_estado as $fila): $total += (int) $fila['cantidad']; ?>
        <tr>
          <td><span class="sello sello--<?= esc($fila['estado']) ?>"><?= esc($fila['estado']) ?></span></td>
          <td><?= (int) $fila['cantidad'] ?></td>
        </tr>
      <?php endforeach; ?>
      <tr><td><strong>Total</strong></td><td><strong><?= $total ?></strong></td></tr>
    </tbody>
  </table>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => 'Reportes de inventario', 'contenido' => $contenido]) ?>
