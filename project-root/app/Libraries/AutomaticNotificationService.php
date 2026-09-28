<?php

namespace App\Libraries;

use App\Models\NotificacionModel;
use App\Models\UsuarioModel;

class AutomaticNotificationService
{
    public function notifyUser(int $dniUsuario, string $tipo, string $mensaje): void
    {
        $usuario = (new UsuarioModel())->find($dniUsuario);
        if (! $usuario) {
            return;
        }

        $notificaciones = new NotificacionModel();
        $id = $notificaciones->insert([
            'dniUsuario'     => $dniUsuario,
            'canal'          => 'sistema',
            'tipo'           => $tipo,
            'mensaje'        => $mensaje,
            'estado_entrega' => 'enviado',
        ], true);

        if (! $id) {
            log_message('error', 'No se pudo registrar el aviso interno de tipo {tipo}.', [
                'tipo' => $tipo,
            ]);
            return;
        }
    }

    public function notifyProfile(string $perfil, string $tipo, string $mensaje): void
    {
        $usuarios = (new UsuarioModel())
            ->where('perfil', $perfil)
            ->where('estado', 'activo')
            ->findAll();

        foreach ($usuarios as $usuario) {
            $this->notifyUser((int) $usuario['dni'], $tipo, $mensaje);
        }
    }
}
