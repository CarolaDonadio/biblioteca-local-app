<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UsuarioAdminModel;

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

        $model   = new UsuarioAdminModel();
        $usuario = $model->verificarCredenciales($email, $password);

        if (! $usuario) {
            return redirect()->back()->withInput()->with('error', 'Credenciales inválidas.');
        }

        session()->set([
            'admin_id'            => $usuario['id'],
            'admin_nombre'        => $usuario['nombre'],
            'admin_rol'           => $usuario['rol'],
            'admin_last_activity' => time(),
        ]);

        $db = \Config\Database::connect();
        $db->table('logs_acceso')->insert([
            'usuario_admin_id' => $usuario['id'],
            'accion'           => 'login',
            'ip'               => $this->request->getIPAddress(),
            'fecha'            => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }
}
