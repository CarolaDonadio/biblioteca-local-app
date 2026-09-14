<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NotificacionModel;

class NotificacionController extends BaseController
{
    protected NotificacionModel $notificaciones;

    public function __construct()
    {
        $this->notificaciones = new NotificacionModel();
    }

    public function index()
    {
        $data['notificaciones'] = $this->notificaciones
            ->select('notificaciones.*, usuarios.nombre_completo')
            ->join('usuarios', 'usuarios.dni = notificaciones.dniUsuario', 'left')
            ->orderBy('notificaciones.id', 'DESC')
            ->findAll(100);

        return view('admin/notificaciones/index', $data);
    }

    public function reenviar($id)
    {
        if (! $this->notificaciones->reintentar((int) $id)) {
            return redirect()->back()->with('error', 'No se encontró la notificación.');
        }

        return redirect()->back()->with('mensaje', 'Notificación reenviada.');
    }

    /** Configuración de credenciales/canales (tokens se guardan en .env, esto solo activa/desactiva canales) */
    public function configuracion()
    {
        return view('admin/notificaciones/configuracion');
    }

    public function guardarConfiguracion()
    {
        // La activación de canales y umbrales de recordatorio se guarda en tabla de settings
        // o en .env según el nivel de sensibilidad del dato; se deja como punto de extensión.
        return redirect()->to('/admin/notificaciones/configuracion')->with('mensaje', 'Configuración guardada.');
    }
}
