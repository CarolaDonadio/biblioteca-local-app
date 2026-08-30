<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PrestamoModel;
use App\Models\LibroModel;
use App\Models\SocioModel;
use App\Models\NotificacionModel;

class PrestamoController extends BaseController
{
    protected PrestamoModel $prestamos;

    public function __construct()
    {
        $this->prestamos = new PrestamoModel();
    }

    public function index()
    {
        $data['prestamos'] = $this->prestamos->activos();
        $data['vencidos']  = $this->prestamos->vencidos();
        return view('admin/prestamos/index', $data);
    }

    public function nuevo()
    {
        $data['libros'] = (new LibroModel())->orderBy('titulo', 'ASC')->findAll();
        $data['socios'] = (new SocioModel())->where('estado', 'activo')->orderBy('apellido', 'ASC')->findAll();
        return view('admin/prestamos/nuevo', $data);
    }

    public function registrar()
    {
        $libroId = (int) $this->request->getPost('libro_id');
        $socioId = (int) $this->request->getPost('socio_id');
        $adminId = session()->get('admin_id');

        try {
            $this->prestamos->registrarPrestamo($libroId, $socioId, $adminId);
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/admin/prestamos')->with('mensaje', 'Préstamo registrado correctamente.');
    }

    public function devolver($id)
    {
        try {
            $this->prestamos->registrarDevolucion((int) $id);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to('/admin/prestamos')->with('mensaje', 'Devolución registrada. Se notificó al siguiente en la cola si correspondía.');
    }

    public function renovar($id)
    {
        try {
            $this->prestamos->renovar((int) $id);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to('/admin/prestamos')->with('mensaje', 'Préstamo renovado.');
    }
}
