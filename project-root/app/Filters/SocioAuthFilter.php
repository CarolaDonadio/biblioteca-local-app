<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Protege el grupo de rutas /socio/panel.
 * Requiere que exista session('socio_id') seteado por SocioPortalController::autenticar().
 */
class SocioAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('socio_id')) {
            $session->setFlashdata('error', 'Debés iniciar sesión para acceder a tu cuenta.');
            return redirect()->to('/socio/login');
        }

        // Control de expiración de sesión del socio (30 min de inactividad).
        $ultimaActividad = $session->get('socio_last_activity');
        if ($ultimaActividad && (time() - $ultimaActividad) > 1800) {
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
