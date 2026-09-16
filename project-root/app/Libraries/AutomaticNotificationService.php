<?php

namespace App\Libraries;

use App\Models\NotificacionModel;
use App\Models\UsuarioModel;
use Config\Email;

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
            'canal'          => 'email',
            'tipo'           => $tipo,
            'mensaje'        => $mensaje,
            'estado_entrega' => 'pendiente',
        ], true);

        if (! $id) {
            return;
        }

        try {
            $this->sendEmail((string) $usuario['mail'], $tipo, $mensaje);
            $notificaciones->update($id, ['estado_entrega' => 'enviado']);
        } catch (\Throwable $exception) {
            $notificaciones->update($id, ['estado_entrega' => 'fallido']);
            log_message('error', 'No se pudo enviar la notificación automática {id}: {error}', [
                'id'    => $id,
                'error' => $exception->getMessage(),
            ]);
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

    private function sendEmail(string $destinatario, string $tipo, string $mensaje): void
    {
        $config = config(Email::class);
        if ($config->fromEmail === '') {
            throw new \RuntimeException('Email sender is not configured.');
        }

        $email = service('email');
        $email->setFrom($config->fromEmail, $config->fromName ?: 'Biblioteca Virtual');
        $email->setTo($destinatario);
        $email->setSubject('Biblioteca Virtual: ' . $tipo);
        $email->setMessage($mensaje);

        if (! $email->send()) {
            throw new \RuntimeException('Email delivery failed.');
        }
    }
}
