<?php ob_start(); ?>

<div class="pub-contenido">
  <h1>Hola, <?= esc($socio['nombre']) ?></h1>

  <div class="kpi-grid" style="grid-template-columns:repeat(2,minmax(160px,1fr));max-width:420px;">
    <div class="tarjeta kpi">
      <div class="kpi__valor"><?= (int) $historial['total_prestamos'] ?></div>
      <div class="kpi__etiqueta">Préstamos totales</div>
    </div>
    <div class="tarjeta kpi">
      <div class="kpi__valor"><a href="/socio/panel/prestamos" style="text-decoration:none;color:inherit;">ver</a></div>
      <div class="kpi__etiqueta">Mis préstamos activos</div>
    </div>
  </div>

  <div class="tarjeta" style="padding:1.4em 1.6em;max-width:520px;margin-top:1.5em;">
    <h3>Sugerir un libro para la biblioteca</h3>
    <form action="/socio/panel/sugerir" method="post">
      <?= csrf_field() ?>
      <div class="campo">
        <label for="titulo_sugerido">Título</label>
        <input type="text" id="titulo_sugerido" name="titulo_sugerido" required>
      </div>
      <div class="campo">
        <label for="autor_sugerido">Autor (opcional)</label>
        <input type="text" id="autor_sugerido" name="autor_sugerido">
      </div>
      <div class="campo">
        <label for="comentario">Comentario</label>
        <textarea id="comentario" name="comentario" rows="2"></textarea>
      </div>
      <button type="submit" class="btn">Enviar sugerencia</button>
    </form>
  </div>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/public_layout', ['titulo' => 'Mi cuenta', 'contenido' => $contenido]) ?>
