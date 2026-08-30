<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EjemplarModel;
use App\Models\LibroModel;

class EjemplarController extends BaseController
{
    protected EjemplarModel $ejemplares;

    public function __construct()
    {
        $this->ejemplares = new EjemplarModel();
    }

    public function index()
    {
        $data['ejemplares'] = $this->ejemplares->conLibro()->orderBy('libros.titulo', 'ASC')->findAll();
        return view('admin/ejemplares/index', $data);
    }

    public function new()
    {
        $data['libros'] = (new LibroModel())->orderBy('titulo', 'ASC')->findAll();
        return view('admin/ejemplares/form', ['ejemplar' => null, 'libros' => $data['libros']]);
    }

    public function create()
    {
        $data = $this->request->getPost(['libro_id', 'codigo_inventario', 'ubicacion']);

        if (! $this->ejemplares->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->ejemplares->errors());
        }

        return redirect()->to('/admin/ejemplares')->with('mensaje', 'Ejemplar registrado en el inventario.');
    }

    public function edit($id = null)
    {
        $ejemplar = $this->ejemplares->find($id);
        $libros   = (new LibroModel())->orderBy('titulo', 'ASC')->findAll();
        return view('admin/ejemplares/form', ['ejemplar' => $ejemplar, 'libros' => $libros]);
    }

    public function update($id = null)
    {
        $data = $this->request->getPost(['libro_id', 'codigo_inventario', 'ubicacion', 'estado']);
        $this->ejemplares->update($id, $data);
        return redirect()->to('/admin/ejemplares')->with('mensaje', 'Ejemplar actualizado.');
    }

    public function delete($id = null)
    {
        $this->ejemplares->delete($id);
        return redirect()->to('/admin/ejemplares')->with('mensaje', 'Ejemplar dado de baja.');
    }

    // ------------------------------------------------------------
    // Registro de ejemplares perdidos o dañados (requerido por el MVP)
    // ------------------------------------------------------------
    public function marcarPerdido($id)
    {
        $observaciones = $this->request->getPost('observaciones');
        $this->ejemplares->marcarEstado($id, 'perdido', $observaciones);
        return redirect()->back()->with('mensaje', 'Ejemplar marcado como perdido.');
    }

    public function marcarDanado($id)
    {
        $observaciones = $this->request->getPost('observaciones');
        $this->ejemplares->marcarEstado($id, 'danado', $observaciones);
        return redirect()->back()->with('mensaje', 'Ejemplar marcado como dañado.');
    }

    // Reportes básicos de inventario solicitados por el MVP
    public function reportes()
    {
        $data['por_estado'] = $this->ejemplares->reportePorEstado();
        return view('admin/ejemplares/reportes', $data);
    }
}
