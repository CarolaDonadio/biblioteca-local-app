<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PromocionModel;

class PromocionController extends BaseController
{
    protected PromocionModel $promociones;

    public function __construct()
    {
        $this->promociones = new PromocionModel();
    }

    public function index()
    {
        $data['promociones'] = $this->promociones->orderBy('fecha_inicio', 'DESC')->findAll();
        return view('admin/promociones/index', $data);
    }

    public function new()
    {
        return view('admin/promociones/form', ['promocion' => null]);
    }

    public function create()
    {
        $data = $this->request->getPost(['titulo', 'descripcion', 'fecha_inicio', 'fecha_fin']);
        $data['activo'] = $this->request->getPost('activo') ? 1 : 0;

        $imagen = $this->request->getFile('imagen');
        if ($imagen && $imagen->isValid()) {
            $nombre = $imagen->getRandomName();
            $imagen->move(FCPATH . 'assets/img/promociones', $nombre);
            $data['imagen_url'] = 'assets/img/promociones/' . $nombre;
        }

        if (! $this->promociones->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->promociones->errors());
        }

        return redirect()->to('/admin/promociones')->with('mensaje', 'Promoción publicada.');
    }

    public function edit($id = null)
    {
        return view('admin/promociones/form', ['promocion' => $this->promociones->find($id)]);
    }

    public function update($id = null)
    {
        $data = $this->request->getPost(['titulo', 'descripcion', 'fecha_inicio', 'fecha_fin']);
        $data['activo'] = $this->request->getPost('activo') ? 1 : 0;
        $this->promociones->update($id, $data);
        return redirect()->to('/admin/promociones')->with('mensaje', 'Promoción actualizada.');
    }

    public function delete($id = null)
    {
        $this->promociones->delete($id);
        return redirect()->to('/admin/promociones')->with('mensaje', 'Promoción eliminada.');
    }
}
