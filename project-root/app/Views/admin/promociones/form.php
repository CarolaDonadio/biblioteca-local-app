<?php ob_start(); ?>

<?php
$esEdicion = !empty($promocion);
$accion = $esEdicion
    ? '/admin/promociones/' . $promocion['id'] . '/update'
    : '/admin/promociones';
?>

<div class="pub-contenido">

    <h1>
        <?= $esEdicion ? 'Editar promoción' : 'Nueva promoción' ?>
    </h1>

    <?php if (session()->getFlashdata('errors')): ?>

        <div class="alerta alerta--error">

            <?php foreach ((array) session()->getFlashdata('errors') as $error): ?>

                <p><?= esc($error) ?></p>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>


    <form
        action="<?= $accion ?>"
        method="post"
        enctype="multipart/form-data"
        class="formulario"
    >

        <?= csrf_field() ?>


        <?php if ($esEdicion): ?>

            <input
                type="hidden"
                name="_method"
                value="PUT"
            >

        <?php endif; ?>


        <!-- TÍTULO -->

        <div class="campo">

            <label for="titulo">
                Título de la promoción
            </label>

            <input
                type="text"
                id="titulo"
                name="titulo"
                value="<?= esc(old('titulo', $promocion['titulo'] ?? '')) ?>"
                maxlength="255"
                required
            >

        </div>


        <!-- DESCRIPCIÓN -->

        <div class="campo">

            <label for="descripcion">
                Descripción
            </label>

            <textarea
                id="descripcion"
                name="descripcion"
                rows="5"
            ><?= esc(old('descripcion', $promocion['descripcion'] ?? '')) ?></textarea>

        </div>


        <!-- FECHA DE INICIO -->

        <div class="campo">

            <label for="fecha_inicio">
                Fecha de inicio
            </label>

            <input
                type="date"
                id="fecha_inicio"
                name="fecha_inicio"
                value="<?= esc(old('fecha_inicio', $promocion['fecha_inicio'] ?? '')) ?>"
                required
            >

        </div>


        <!-- FECHA DE FIN -->

        <div class="campo">

            <label for="fecha_fin">
                Fecha de finalización
            </label>

            <input
                type="date"
                id="fecha_fin"
                name="fecha_fin"
                value="<?= esc(old('fecha_fin', $promocion['fecha_fin'] ?? '')) ?>"
                required
            >

        </div>


        <!-- IMAGEN -->

        <div class="campo">

            <label for="imagen">
                Imagen promocional
            </label>

            <input
                type="file"
                id="imagen"
                name="imagen"
                accept="image/jpeg,image/png,image/webp"
            >

            <small>
                Formatos permitidos: JPG, PNG o WEBP.
            </small>

        </div>


        <!-- IMAGEN ACTUAL -->

        <?php if ($esEdicion && !empty($promocion['imagen_url'])): ?>

            <div class="campo">

                <label>
                    Imagen actual
                </label>

                <div>

                    <img
                        src="/<?= esc($promocion['imagen_url']) ?>"
                        alt="<?= esc($promocion['titulo']) ?>"
                        style="max-width:300px;border-radius:10px;"
                    >

                </div>

            </div>

        <?php endif; ?>


        <!-- CONDICIONES -->

        <div class="campo">

            <label for="condiciones">
                Condiciones de la promoción
            </label>

            <textarea
                id="condiciones"
                name="condiciones"
                rows="5"
                placeholder="Ejemplo: Promoción válida para socios activos."
            ><?= esc(old('condiciones', $promocion['condiciones'] ?? '')) ?></textarea>

        </div>


        <!-- BOTONES -->

        <div class="formulario__acciones">

            <button
                type="submit"
                class="btn"
            >
                <?= $esEdicion
                    ? 'Guardar cambios'
                    : 'Crear promoción'
                ?>
            </button>

            <a
                href="/admin/promociones"
                class="btn btn--outline"
            >
                Cancelar
            </a>

        </div>

    </form>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');

    function validarFechas() {

        if (
            fechaInicio.value &&
            fechaFin.value &&
            fechaFin.value < fechaInicio.value
        ) {
            fechaFin.setCustomValidity(
                'La fecha de finalización no puede ser anterior a la fecha de inicio.'
            );
        } else {
            fechaFin.setCustomValidity('');
        }
    }

    fechaInicio.addEventListener('change', validarFechas);
    fechaFin.addEventListener('change', validarFechas);

});
</script>


<?php $contenido = ob_get_clean(); ?>

<?= view('layouts/admin_layout', [
    'titulo' => $esEdicion
        ? 'Editar promoción'
        : 'Nueva promoción',
    'contenido' => $contenido
]) ?>
