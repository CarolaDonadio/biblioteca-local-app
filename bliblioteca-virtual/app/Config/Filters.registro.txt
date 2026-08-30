Este proyecto asume un esqueleto estándar de CodeIgniter 4 instalado vía Composer
(composer create-project codeigniter4/appstarter).

Los archivos de este paquete van DENTRO de ese esqueleto (se pisan/combinan las
carpetas app/ y public/). El único archivo del framework que hay que EDITAR a mano
(no viene incluido acá porque pertenece al core) es:

    app/Config/Filters.php

Agregar lo siguiente dentro de la propiedad $aliases:

    public array $aliases = [
        ...
        'adminAuth' => \App\Filters\AdminAuthFilter::class,
        'socioAuth' => \App\Filters\SocioAuthFilter::class,
    ];

No hace falta agregarlos a $globals ni $methods: se aplican explícitamente
por grupo de rutas en app/Config/Routes.php con ['filter' => 'adminAuth']
y ['filter' => 'socioAuth'].
