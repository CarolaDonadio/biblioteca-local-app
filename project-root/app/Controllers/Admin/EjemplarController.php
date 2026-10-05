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

        try {
            $creado = $this->ejemplares->crearAdministrativamente($data);
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        if (! $creado) {
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

        try {
            $this->ejemplares->actualizarAdministrativamente((int) $id, $data);
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/admin/ejemplares')->with('mensaje', 'Ejemplar actualizado.');
    }

    public function delete($id = null)
    {
        try {
            $this->ejemplares->eliminarAdministrativamente((int) $id);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to('/admin/ejemplares')->with('mensaje', 'Ejemplar dado de baja.');
    }

    // ------------------------------------------------------------
    // Registro de ejemplares perdidos o dañados (requerido por el MVP)
    // ------------------------------------------------------------
    public function marcarPerdido($id)
    {
        $observaciones = $this->request->getPost('observaciones');
        try {
            $actualizado = $this->ejemplares->marcarEstado((int) $id, 'perdido', $observaciones);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        if (! $actualizado) {
            return redirect()->back()->with(
                'error',
                'Solo se pueden marcar como perdidos ejemplares que estén disponibles.'
            );
        }
        return redirect()->back()->with('mensaje', 'Ejemplar marcado como perdido.');
    }

    public function marcarDanado($id)
    {
        $observaciones = $this->request->getPost('observaciones');
        try {
            $actualizado = $this->ejemplares->marcarEstado((int) $id, 'danado', $observaciones);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        if (! $actualizado) {
            return redirect()->back()->with(
                'error',
                'Solo se pueden marcar como dañados ejemplares que estén disponibles.'
            );
        }
        return redirect()->back()->with('mensaje', 'Ejemplar marcado como dañado.');
    }

    // Reportes básicos de inventario solicitados por el MVP
    public function reportes()
    {
        $data['por_estado'] = $this->ejemplares->reportePorEstado();
        return view('admin/ejemplares/reportes', $data);
    }
}
