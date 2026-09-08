<?php ob_start(); ?>

<div class="pub-contenido">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2em;">
    <div>
      <h1>Hola, <?= esc($socio['nombre_completo']) ?></h1>
      <p style="color:var(--gris-texto);margin-top:-.5em;"><?= esc($socio['mail']) ?></p>
    </div>
    <a href="/socio/logout" class="btn btn--outline">Cerrar sesión</a>
  </div>

  <?php if (session('mensaje')): ?>
    <div class="alerta alerta--exito" data-auto-cerrar><?= esc(session('mensaje')) ?></div>
  <?php endif; ?>
  <?php if (session('error')): ?>
    <div class="alerta alerta--error" data-auto-cerrar><?= esc(session('error')) ?></div>
  <?php endif; ?>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:2em;margin-bottom:2em;">
    <!-- Panel de edición de perfil -->
    <div class="tarjeta" style="padding:1.6em 1.8em;">
      <h3>Mi Información</h3>
      <form action="/socio/actualizarPerfil" method="post">
        <?= csrf_field() ?>
        <div class="campo">
          <label for="nombre_completo">Nombre completo</label>
          <input type="text" id="nombre_completo" name="nombre_completo" required value="<?= esc($socio['nombre_completo']) ?>">
        </div>
        <div class="campo">
          <label for="telefono">Teléfono</label>
          <input type="tel" id="telefono" name="telefono" value="<?= esc($socio['telefono'] ?? '') ?>">
        </div>
        <button type="submit" class="btn" style="width:100%;">Guardar cambios</button>
      </form>
    </div>

    <!-- Estadísticas -->
    <div>
      <div class="kpi-grid" style="grid-template-columns:1fr;">
        <div class="tarjeta kpi">
          <div class="kpi__valor"><?= count($registros) ?></div>
          <div class="kpi__etiqueta">Préstamos registrados</div>
        </div>
      </div>
      <div class="tarjeta" style="padding:1.6em 1.8em;margin-top:1em;">
        <h4 style="margin-top:0;">Información de cuenta</h4>
        <dl style="font-size:.9rem;line-height:1.6;">
          <dt style="font-weight:600;color:var(--gris-texto);">DNI</dt>
          <dd style="margin-bottom:1em;" class="mono"><?= esc($socio['dni']) ?></dd>
          <dt style="font-weight:600;color:var(--gris-texto);">Estado</dt>
          <dd><span class="sello sello--<?= $socio['estado'] === 'activo' ? 'disponible' : 'vencido' ?>"><?= esc($socio['estado']) ?></span></dd>
        </dl>
      </div>
    </div>
  </div>

  <!-- Historial de Préstamos -->
  <div class="tarjeta" style="padding:1.6em 1.8em;">
    <h3>Historial de Préstamos</h3>
    <div style="overflow-x:auto;">
      <table>
        <thead>
          <tr>
            <th>Libro</th>
            <th>Autor</th>
            <th>Fecha de Préstamo</th>
            <th>Fecha de Vencimiento</th>
            <th>Fecha de Devolución</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody>
          <?php if (! empty($registros)): ?>
            <?php foreach ($registros as $registro): ?>
              <tr>
                <td><?= esc($registro['titulo']) ?></td>
                <td><?= esc($registro['autor']) ?></td>
                <td><?= esc($registro['fechaPrestamo']) ?></td>
                <td><?= esc($registro['fechaVence']) ?></td>
                <td><?= esc($registro['fechaDevolucion'] ?? '—') ?></td>
                <td>
                  <?php
                    $estado = 'activo';
                    if ($registro['fechaDevolucion']) {
                      $estado = 'devuelto';
                    } elseif (strtotime($registro['fechaVence']) < time()) {
                      $estado = 'vencido';
                    }
                  ?>
                  <span class="sello sello--<?= $estado === 'devuelto' ? 'disponible' : ($estado === 'vencido' ? 'vencido' : 'info') ?>"><?= ucfirst($estado) ?></span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" style="text-align:center;padding:2em;color:var(--gris-texto);">
                No tenés préstamos registrados todavía.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/public_layout', ['titulo' => 'Mi cuenta', 'contenido' => $contenido]) ?>
