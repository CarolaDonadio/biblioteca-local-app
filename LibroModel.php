<?php

namespace App\Controllers\Publico;

use App\Controllers\BaseController;
use App\Models\SocioModel;
use App\Models\ReservaModel;
use App\Models\PrestamoModel;
use App\Models\SugerenciaModel;

class SocioPortalController extends BaseController
{
    public function login()
    {
        if (session()->get('socio_id')) {
            return redirect()->to('/socio/panel');
        }
        return view('publico/socio_login');
    }

    public function autenticar()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $socio = (new SocioModel())->where('email', $email)->first();

        if (! $socio || ! password_verify($password, $socio['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Email o contraseña incorrectos.');
        }

        if ($socio['estado'] !== 'activo') {
            return redirect()->back()->with('error', 'Tu cuenta de socio no está activa. Consultá en la biblioteca.');
        }

        session()->set(['socio_id' => $socio['id'], 'socio_nombre' => $socio['nombre']]);
        return redirect()->to('/socio/panel');
    }

    public function registro()
    {
        return view('publico/socio_registro');
    }

    public function guardarRegistro()
    {
        $data = $this->request->getPost(['nombre', 'apellido', 'dni', 'email', 'telefono']);
        $data['password_hash'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        $data['fecha_alta']    = date('Y-m-d H:i:s');
        $data['estado']        = 'activo';

        $model = new SocioModel();
        if (! $model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to('/socio/login')->with('mensaje', 'Cuenta creada. Ya podés iniciar sesión.');
    }

    public function logout()
    {
        session()->remove(['socio_id', 'socio_nombre']);
        return redirect()->to('/');
    }

    public function panel()
    {
        $socioId = session()->get('socio_id');
        $data['socio']     = (new SocioModel())->find($socioId);
        $data['historial'] = (new SocioModel())->historial($socioId);
        return view('publico/socio_panel', $data);
    }

    public function misPrestamos()
    {
        $socioId = session()->get('socio_id');
        $data['prestamos'] = (new PrestamoModel())
            ->select('prestamos.*, libros.titulo, ejemplares.codigo_inventario')
            ->join('ejemplares', 'ejemplares.id = prestamos.ejemplar_id')
            ->join('libros', 'libros.id = ejemplares.libro_id')
            ->where('prestamos.socio_id', $socioId)
            ->orderBy('prestamos.fecha_prestamo', 'DESC')
            ->findAll();

        return view('publico/socio_prestamos', $data);
    }

    /** El socio dispara el motor de reservas sincrónico desde el catálogo público */
    public function reservar($libroId)
    {
        $socioId = session()->get('socio_id');

        try {
            (new ReservaModel())->crearReserva((int) $libroId, $socioId);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'No se pudo procesar la reserva. Probá de nuevo.');
        }

        return redirect()->to('/socio/panel')->with('mensaje', 'Reserva registrada. Te avisamos por notificación cuando esté lista.');
    }

    public function renovar($prestamoId)
    {
        try {
            (new PrestamoModel())->renovar((int) $prestamoId);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->back()->with('mensaje', 'Préstamo renovado.');
    }

    public function sugerirLibro()
    {
        $data = $this->request->getPost(['titulo_sugerido', 'autor_sugerido', 'comentario']);
        $data['socio_id'] = session()->get('socio_id');
        $data['fecha']    = date('Y-m-d H:i:s');

        (new SugerenciaModel())->save($data);

        return redirect()->back()->with('mensaje', '¡Gracias! Tu sugerencia fue enviada a la biblioteca.');
    }
}
