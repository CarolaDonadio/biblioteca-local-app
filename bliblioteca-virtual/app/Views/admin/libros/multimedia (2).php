<?php ob_start(); ?>

<p style="color:var(--gris-texto);margin-top:-1em;"><?= esc($libro['titulo']) ?> — <?= esc($libro['autor']) ?></p>

<div class="tarjeta" style="padding:1.4em 1.6em;max-width:520px;margin-bottom:1.5em;">
  <h3>Subir material digital</h3>
  <form action="/admin/libros/<?= $libro['id'] ?>/multimedia" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="campo">
      <label for="tipo">Tipo</label>
      <select id="tipo" name="tipo" required>
        <option value="pdf">PDF</option>
        <option value="audiolibro">Audiolibro</option>
      </select>
    </div>
    <div class="campo">
      <label for="archivo">Archivo</label>
      <input type="file" id="archivo" name="archivo" required>
    </div>
    <button type="submit" class="btn">Subir</button>
  </form>
</div>

<div class="tarjeta">
  <table>
    <thead><tr><th>Tipo</th><th>Archivo</th><th>Tamaño</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($multimedia as $item): ?>
        <tr>
          <td><span class="sello sello--promocion"><?= esc($item['tipo']) ?></span></td>
          <td><a href="/<?= esc($item['archivo_url']) ?>" target="_blank">ver archivo</a></td>
          <td><?= $item['tamano_kb'] ? esc($item['tamano_kb']) . ' KB' : '—' ?></td>
          <td>
            <form action="/admin/multimedia/<?= $item['id'] ?>/eliminar" method="post" data-confirmar="¿Eliminar este archivo?">
              <?= csrf_field() ?>
              <button class="btn btn--peligro btn--chico" type="submit">Eliminar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($multimedia)): ?>
        <tr><td colspan="4">Este libro todavía no tiene material digital cargado.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => 'Multimedia', 'contenido' => $contenido]) ?>
