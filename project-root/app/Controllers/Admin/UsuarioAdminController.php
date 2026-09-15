<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;

/**
 * Gestión de usuarios administrativos (perfil 'bibliotecario').
 */
class UsuarioAdminController extends BaseController
{
    protected UsuarioModel $usuarios;

    public function __construct()
    {
        $this->usuarios = new UsuarioModel();
    }

    public function index()
    {
        $data['usuarios'] = $this->usuarios->where('perfil', 'bibliotecario')->orderBy('nombre_completo', 'ASC')->findAll();
        return view('admin/usuarios/index', $data);
    }

    public function new()
    {
        return view('admin/usuarios/form', ['usuario' => null]);
    }

    public function create()
    {
        $data = $this->request->getPost(['nombre_completo', 'mail', 'telefono']);
        $data['perfil'] = 'bibliotecario';
        $data['estado'] = 'activo';
        $data['password_hash'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

        if (! $this->usuarios->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->usuarios->errors());
        }

        return redirect()->to('/admin/usuarios')->with('mensaje', 'Usuario administrativo creado.');
    }

    public function edit($id = null)
    {
        return view('admin/usuarios/form', ['usuario' => $this->usuarios->find($id)]);
    }

    public function update($id = null)
    {
        $data = $this->request->getPost(['nombre_completo', 'mail', 'telefono', 'estado']);

        // is_unique[usuarios.mail] rechazaría el propio email si no se excluye el id actual
        $usuarioConEmail = $this->usuarios->where('mail', $data['mail'])->where('dni !=', $id)->first();
        if ($usuarioConEmail) {
            return view('admin/usuarios/form', [
                'usuario' => $this->usuarios->find($id),
                'errors'  => ['mail' => 'Este email ya está registrado en otro usuario.'],
            ]);
        }

        $password = $this->request->getPost('password');
        if ($password) {
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if (! $this->usuarios->skipValidation()->update($id, $data)) {
            return view('admin/usuarios/form', [
                'usuario' => $this->usuarios->find($id),
                'errors'  => $this->usuarios->errors(),
            ]);
        }

        return redirect()->to('/admin/usuarios')->with('mensaje', 'Usuario actualizado.');
    }

    public function delete($id = null)
    {
        if ((string) $id === (string) session()->get('admin_id')) {
            return redirect()->to('/admin/usuarios')->with('error', 'No podés eliminar tu propio usuario.');
        }

        if ($this->usuarios->where('perfil', 'bibliotecario')->countAllResults() <= 1) {
            return redirect()->to('/admin/usuarios')->with('error', 'Debe quedar al menos un administrador.');
        }

        $this->usuarios->delete($id);
        return redirect()->to('/admin/usuarios')->with('mensaje', 'Usuario eliminado.');
    }
}
