<?php ob_start(); ?>

<div class="tarjeta" style="padding:1.6em 1.8em;max-width:760px;margin-bottom:1.5em;">
  <h3>Reservar libro para un socio</h3>
  <form action="/admin/reservas" method="post" style="display:grid;grid-template-columns:1fr 1fr auto;gap:1em;align-items:end;">
    <?= csrf_field() ?>
    <div class="campo">
      <label for="libro_id">Libro</label>
      <select id="libro_id" name="libro_id" required>
        <option value="">Seleccioná un libro</option>
        <?php foreach (($libros ?? []) as $libro): ?>
          <option value="<?= esc($libro['id'], 'attr') ?>"><?= esc($libro['titulo']) ?> — <?= esc($libro['autor']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="campo">
      <label for="socio_id">Socio</label>
      <select id="socio_id" name="socio_id" required>
        <option value="">Seleccioná un socio</option>
        <?php foreach (($socios ?? []) as $socio): ?>
          <option value="<?= esc($socio['dni'], 'attr') ?>"><?= esc($socio['nombre_completo']) ?> (DNI <?= esc($socio['dni']) ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="btn">Crear reserva</button>
  </form>
</div>

<p style="color:var(--gris-texto);margin-top:-1em;">Cola de reservas en tiempo real, ordenada por libro y posición.</p>

<div class="tarjeta">
  <table>
    <thead><tr><th>Libro</th><th>Socio</th><th>Posición</th><th>Reservado el</th><th>Estado</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($reservas as $r): ?>
        <tr>
          <td><?= esc($r['titulo']) ?></td>
          <td><?= esc($r['socio_nombre'] ?? 'Socio no encontrado') ?></td>
          <td>#<?= (int) $r['posicion_cola'] ?></td>
          <td><?= esc($r['fecha_reserva']) ?></td>
          <td>
            <span class="sello sello--<?= $r['estado'] === 'confirmada' ? 'reservado' : ($r['estado'] === 'cancelada' ? 'vencido' : 'pendiente') ?>">
              <?= esc(ucfirst($r['estado'])) ?>
            </span>
          </td>
          <td style="text-align:right;white-space:nowrap;">
            <?php if ($r['estado'] === 'pendiente'): ?>
              <form action="/admin/reservas/<?= $r['id'] ?>/confirmar" method="post" style="display:inline;">
                <?= csrf_field() ?>
                <button class="btn btn--outline btn--chico" type="submit">Confirmar</button>
              </form>
              <form action="/admin/reservas/<?= $r['id'] ?>/cancelar" method="post" style="display:inline;" data-confirmar="¿Cancelar esta reserva?">
                <?= csrf_field() ?>
                <button class="btn btn--peligro btn--chico" type="submit">Cancelar</button>
              </form>
            <?php elseif ($r['estado'] === 'confirmada'): ?>
              <form action="/admin/reservas/<?= $r['id'] ?>/completar" method="post" style="display:inline;" data-confirmar="¿Confirmar que el socio retiró el libro? La reserva se marcará como completada.">
                <?= csrf_field() ?>
                <button class="btn btn--chico" type="submit">Marcar retirado</button>
              </form>
              <form action="/admin/reservas/<?= $r['id'] ?>/cancelar" method="post" style="display:inline;" data-confirmar="¿Cancelar esta reserva?">
                <?= csrf_field() ?>
                <button class="btn btn--peligro btn--chico" type="submit">Cancelar</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($reservas)): ?>
        <tr><td colspan="6">No hay reservas pendientes en este momento.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', ['titulo' => 'Motor de reservas', 'contenido' => $contenido]) ?>
