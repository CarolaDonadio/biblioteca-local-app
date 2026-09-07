<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Protege todo el grupo de rutas /admin.
 * Requiere que exista session('admin_id') seteado por AuthController::autenticar().
 */
class AdminAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('admin_id')) {
            $session->setFlashdata('error', 'Debés iniciar sesión para acceder al panel.');
            return redirect()->to('/admin/login');
        }

        // Control de expiración de sesión administrativa (30 min de inactividad)
        $ultimaActividad = $session->get('admin_last_activity');
        if ($ultimaActividad && (time() - $ultimaActividad) > 1800) {
            $session->destroy();
            return redirect()->to('/admin/login')->with('error', 'La sesión expiró por inactividad.');
        }
        $session->set('admin_last_activity', time());
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nada por ahora.
    }
}
