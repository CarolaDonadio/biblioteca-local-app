<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UsuarioAdminModel;

/**
 * Gestión de roles y accesos administrativos.
 * Solo un 'superadmin' puede crear/editar otros usuarios administradores.
 */
class UsuarioAdminController extends BaseController
{
    protected UsuarioAdminModel $usuarios;

    public function __construct()
    {
        $this->usuarios = new UsuarioAdminModel();
    }

    private function requerirSuperadmin()
    {
        if (session()->get('admin_rol') !== 'superadmin') {
            return redirect()->to('/admin')->with('error', 'No tenés permisos para gestionar usuarios administrativos.');
        }
        return null;
    }

    public function index()
    {
        if ($redir = $this->requerirSuperadmin()) return $redir;
        $data['usuarios'] = $this->usuarios->orderBy('nombre', 'ASC')->findAll();
        return view('admin/usuarios/index', $data);
    }

    public function new()
    {
        if ($redir = $this->requerirSuperadmin()) return $redir;
        return view('admin/usuarios/form', ['usuario' => null]);
    }

    public function create()
    {
        if ($redir = $this->requerirSuperadmin()) return $redir;

        $data = $this->request->getPost(['nombre', 'email', 'rol']);
        $data['password_hash'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

        if (! $this->usuarios->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->usuarios->errors());
        }

        return redirect()->to('/admin/usuarios')->with('mensaje', 'Usuario administrativo creado.');
    }

    public function edit($id = null)
    {
        if ($redir = $this->requerirSuperadmin()) return $redir;
        return view('admin/usuarios/form', ['usuario' => $this->usuarios->find($id)]);
    }

    public function update($id = null)
    {
        if ($redir = $this->requerirSuperadmin()) return $redir;

        $data = $this->request->getPost(['nombre', 'email', 'rol']);
        $data['activo'] = $this->request->getPost('activo') ? 1 : 0;

        $password = $this->request->getPost('password');
        if ($password) {
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->usuarios->update($id, $data);
        return redirect()->to('/admin/usuarios')->with('mensaje', 'Usuario actualizado.');
    }

    public function delete($id = null)
    {
        if ($redir = $this->requerirSuperadmin()) return $redir;
        $this->usuarios->delete($id);
        return redirect()->to('/admin/usuarios')->with('mensaje', 'Usuario eliminado.');
    }
}
