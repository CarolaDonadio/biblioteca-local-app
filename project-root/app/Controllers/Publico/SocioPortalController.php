<?php

namespace App\Controllers\Publico;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;

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
        $email    = (string) $this->request->getPost('email');
        $password = (string) $this->request->getPost('password');

        $model = new UsuarioModel();
        // Sólo perfil "socio": un email de bibliotecario no debe poder entrar por acá.
        $usuario = $model->verificarCredenciales($email, $password, ['socio']);

        if (! $usuario) {
            return redirect()->back()->withInput()->with('error', 'Email o contraseña incorrectos.');
        }

        session()->regenerate();
        session()->set([
            'socio_id'            => $usuario['dni'],
            'socio_nombre'        => $usuario['nombre_completo'],
            'socio_last_activity' => time(),
        ]);

        return redirect()->to('/socio/panel');
    }

    public function registro()
    {
        if (session()->get('socio_id')) {
            return redirect()->to('/socio/panel');
        }

        return view('publico/socio_registro');
    }

    public function guardarRegistro()
    {
        $nombre    = trim((string) $this->request->getPost('nombre'));
        $apellido  = trim((string) $this->request->getPost('apellido'));

        $datos = [
            'dni'             => $this->request->getPost('dni'),
            'nombre_completo' => trim($nombre . ' ' . $apellido),
            'mail'            => $this->request->getPost('email'),
            'telefono'        => $this->request->getPost('telefono'),
            'password'        => (string) $this->request->getPost('password'),
        ];

        $model    = new UsuarioModel();
        $resultado = $model->crearSocio($datos);

        if (! $resultado['success']) {
            return redirect()->back()->withInput()->with('errors', $resultado['errors']);
        }

        return redirect()->to('/socio/login')->with('mensaje', 'Cuenta creada. Ya podés iniciar sesión.');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/');
    }
}
