<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('admin_id')) {
            return redirect()->to('/admin');
        }
        return view('admin/auth/login');
    }

    public function autenticar()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $model   = new UsuarioModel();
        $usuario = $model->verificarCredenciales($email, $password);

        if (! $usuario) {
            return redirect()->back()->withInput()->with('error', 'Credenciales inválidas.');
        }

        session()->set([
            'admin_id'            => $usuario['dni'],
            'admin_nombre'        => $usuario['nombre_completo'],
            'admin_rol'           => $usuario['perfil'],
            'admin_last_activity' => time(),
        ]);

        return redirect()->to('/admin');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }
}
