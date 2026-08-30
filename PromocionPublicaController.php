<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SocioModel;

class SocioController extends BaseController
{
    protected SocioModel $socios;

    public function __construct()
    {
        $this->socios = new SocioModel();
    }

    public function index()
    {
        $data['socios'] = $this->socios->orderBy('apellido', 'ASC')->findAll();
        return view('admin/socios/index', $data);
    }

    public function new()
    {
        return view('admin/socios/form', ['socio' => null]);
    }

    public function create()
    {
        $data = $this->request->getPost(['nombre', 'apellido', 'dni', 'email', 'telefono', 'telegram_chat_id', 'whatsapp_numero']);
        $data['password_hash'] = password_hash($this->request->getPost('password') ?: substr(md5(uniqid()), 0, 8), PASSWORD_DEFAULT);

        if (! $this->socios->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->socios->errors());
        }

        return redirect()->to('/admin/socios')->with('mensaje', 'Socio registrado.');
    }

    public function edit($id = null)
    {
        return view('admin/socios/form', ['socio' => $this->socios->find($id)]);
    }

    public function update($id = null)
    {
        $data = $this->request->getPost(['nombre', 'apellido', 'dni', 'email', 'telefono', 'telegram_chat_id', 'whatsapp_numero', 'estado']);
        $this->socios->update($id, $data);
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
        $data['socio']     = $this->socios->find($id);
        $data['historial'] = $this->socios->historial((int) $id);
        return view('admin/socios/historial', $data);
    }
}
