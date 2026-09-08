<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
<<<<<<< HEAD
 * Protege el portal del socio (/socio/home, /socio/panel, etc).
 * Requiere que exista session('socio_dni') seteado por SocioPortalController::autenticar().
=======
 * Protege el grupo de rutas /socio/panel.
 * Requiere que exista session('socio_id') seteado por SocioPortalController::autenticar().
>>>>>>> Rafael
 */
class SocioAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

<<<<<<< HEAD
        if (! $session->get('socio_dni')) {
            $session->setFlashdata('error', 'Debés iniciar sesión para acceder al portal.');
            return redirect()->to('/socio/login');
        }

        // Control de expiración de sesión de socio (1 hora de inactividad)
        $ultimaActividad = $session->get('socio_last_activity');
        if ($ultimaActividad && (time() - $ultimaActividad) > 3600) {
=======
        if (! $session->get('socio_id')) {
            $session->setFlashdata('error', 'Debés iniciar sesión para acceder a tu cuenta.');
            return redirect()->to('/socio/login');
        }

        // Control de expiración de sesión del socio (30 min de inactividad).
        $ultimaActividad = $session->get('socio_last_activity');
        if ($ultimaActividad && (time() - $ultimaActividad) > 1800) {
>>>>>>> Rafael
            $session->destroy();
            return redirect()->to('/socio/login')->with('error', 'La sesión expiró por inactividad.');
        }
        $session->set('socio_last_activity', time());
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nada por ahora.
    }
}
