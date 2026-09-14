<?php ob_start(); ?>

<div class="toolbar gestion-toolbar">
  <div>
    <p class="gestion-subtitulo">Solicitudes y devoluciones pendientes de atención.</p>
    <span class="gestion-estado" data-gestion-estado>Actualizando automáticamente</span>
  </div>
  <button class="btn btn--outline" type="button" data-gestion-actualizar>Actualizar ahora</button>
</div>

<div id="gestion-tablero" data-gestion-url="/admin/gestion/actualizar">
  <?= view('admin/gestion/_tablero', get_defined_vars()) ?>
</div>

<?php $contenido = ob_get_clean(); ?>
<?= view('layouts/admin_layout', [
  'titulo' => 'Gestión de préstamos',
  'contenido' => $contenido,
  'js_extra' => '<script src="/assets/js/admin/gestion.js"></script>',
]) ?>