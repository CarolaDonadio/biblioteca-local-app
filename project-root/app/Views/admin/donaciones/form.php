<?php ob_start(); ?>

<?php
$esEdicion = ! empty($donacion);
$accion = $esEdicion ? '/admin/donaciones/' . $donacion['id'] : '/admin/donaciones';
$val = static fn (string $campo, mixed $predeterminado = '') => esc(old($campo, $donacion[$campo] ?? $predeterminado));
?>

<div class="tarjeta" style="padding:1.6em 1.8em;max-width:680px;">
  <?php if (session()->getFlashdata('errors')): ?>
    <div class="alerta alerta--error">
      <?php foreach ((array) session()->getFlashdata('errors') as $error): ?><p><?= esc($error) ?></p><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form action="<?= $accion ?>" method="post">
    <?= csrf_field() ?>
    <?php if ($esEdicion): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>

    <div class="campo"><label for="donante">Donante</label><input id="donante" name="donante" value="<?= $val('donante') ?>" maxlength="150" required></div>

    <div class="formulario__fila">
      <div class="campo"><label for="tipo">Tipo</label><select id="tipo" name="tipo" required>
        <?php foreach (['libros', 'material', 'dinero', 'mobiliario', 'otro'] as $tipo): ?><option value="<?= $tipo ?>" <?= old('tipo', $donacion['tipo'] ?? '') === $tipo ? 'selected' : '' ?>><?= ucfirst($tipo) ?></option><?php endforeach; ?>
      </select></div>
      <div class="campo"><label for="cantidad">Cantidad</label><input type="number" id="cantidad" name="cantidad" value="<?= $val('cantidad', 1) ?>" min="1" required></div>
    </div>

    <div class="campo"><label for="descripcion">Descripción</label><textarea id="descripcion" name="descripcion" rows="3" required><?= $val('descripcion') ?></textarea></div>

    <div class="formulario__fila">
      <div class="campo"><label for="fecha_donacion">Fecha de donación</label><input type="date" id="fecha_donacion" name="fecha_donacion" value="<?= $val('fecha_donacion', date('Y-m-d')) ?>" required></div>
      <div class="campo"><label for="estado">Estado</label><select id="estado" name="estado" required>
        <?php foreach (['recibida', 'pendiente', 'rechazada'] as $estado): ?><option value="<?= $estado ?>" <?= old('estado', $donacion['estado'] ?? 'recibida') === $estado ? 'selected' : '' ?>><?= ucfirst($estado) ?></option><?php endforeach; ?>
      </select></div>
    </div>

    <div class="campo"><label for="observaciones">Observaciones</label><textarea id="observaciones" name="observaciones" rows="4"><?= $val('observaciones') ?></textarea></div>

    <div class="formulario__acciones"><button type="submit" class="btn"><?= $esEdicion ? 'Guardar cambios' : 'Registrar donación' ?></button><a href="/admin/donaciones" class="btn btn--outline">Cancelar</a></div>
  </form>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => $esEdicion ? 'Editar donación' : 'Nueva donación', 'contenido' => $contenido]) ?>