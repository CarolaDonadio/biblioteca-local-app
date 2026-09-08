<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use App\Models\RegistroModel;

class SocioController extends BaseController
{
    protected UsuarioModel $socios;

    public function __construct()
    {
        $this->socios = new UsuarioModel();
    }

    public function index()
    {
        $busqueda = trim((string) $this->request->getGet('q'));
        $consulta = $this->socios->where('perfil', 'socio');

        if ($busqueda !== '') {
            $consulta->groupStart()
                ->like('dni', $busqueda)
                ->orLike('nombre_completo', $busqueda)
                ->orLike('mail', $busqueda)
                ->groupEnd();
        }

        $data['q'] = $busqueda;
        $data['socios'] = $consulta->orderBy('nombre_completo', 'ASC')->findAll();
        return view('admin/socios/index', $data);
    }

    public function new()
    {
        return view('admin/socios/form', ['socio' => null]);
    }

    public function create()
    {
        $data = $this->request->getPost(['nombre_completo', 'dni', 'mail', 'telefono']);
        $data['perfil'] = 'socio';
        $data['estado'] = 'activo';
        $data['password_hash'] = password_hash($this->request->getPost('password') ?: substr(md5(uniqid()), 0, 8), PASSWORD_DEFAULT);

        if (! $this->socios->save($data)) {
            return view('admin/socios/form', [
                'socio' => null,
                'errors' => $this->socios->errors(),
            ])->with('input', $this->request->getPost());
        }

        return redirect()->to('/admin/socios')->with('mensaje', 'Socio registrado.');
    }

    public function edit($id = null)
    {
        return view('admin/socios/form', ['socio' => $this->socios->find($id)]);
    }

    public function update($id = null)
    {
        $data = $this->request->getPost(['nombre_completo', 'mail', 'telefono', 'estado']);
        $errors = [];

        // Validar que el email no esté en uso por otro usuario
        $usuarioConEmail = $this->socios->where('mail', $data['mail'])->where('dni !=', $id)->first();
        if ($usuarioConEmail) {
            $errors['mail'] = 'Este email ya está registrado en otro usuario.';
        }

        if (!empty($errors)) {
            return view('admin/socios/form', [
                'socio' => $this->socios->find($id),
                'errors' => $errors,
            ]);
        }

        if (! $this->socios->skipValidation()->update($id, $data)) {
            return view('admin/socios/form', [
                'socio' => $this->socios->find($id),
                'errors' => $this->socios->errors(),
            ]);
        }

        return redirect()->to('/admin/socios')->with('mensaje', 'Socio actualizado.');
    }

    public function delete($id = null)
    {
        $this->socios->delete($id);
        return redirect()->to('/admin/socios')->with('mensaje', 'Socio eliminado.');
    }

    // Historial completo requerido por el MVP: préstamos pasados, sanciones, libros leídos
    public function historial($id)
    {
        $socio = $this->socios->find($id);
        
        if (!$socio) {
            return redirect()->to('/admin/socios')->with('error', 'Socio no encontrado.');
        }

        // Dividir nombre_completo en nombre y apellido para compatibilidad con la vista
        $nombres = explode(' ', $socio['nombre_completo'], 2);
        $socio['nombre'] = $nombres[0];
        $socio['apellido'] = $nombres[1] ?? '';
        $socio['email'] = $socio['mail'];

        $data['socio']     = $socio;
        $data['historial'] = (new RegistroModel())->historialPorSocio((int) $id);
        return view('admin/socios/historial', $data);
    }
}
