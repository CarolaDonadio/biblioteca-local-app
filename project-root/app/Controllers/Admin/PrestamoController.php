<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RegistroModel;
use App\Models\LibroModel;
use App\Models\SocioModel;

class PrestamoController extends BaseController
{
    protected RegistroModel $prestamos;
    protected LibroModel $libros;
    protected SocioModel $socios;

    public function __construct()
    {
        $this->prestamos = new RegistroModel();
        $this->libros    = new LibroModel();
        $this->socios    = new SocioModel();
    }

    public function index()
    {
        return view('admin/prestamos/index', [
            'prestamos' => $this->prestamos->activos(),
            'vencidos'  => $this->prestamos->vencidos(),
        ]);
    }

    public function nuevo()
    {
        return view('admin/prestamos/nuevo', [
            'libros' => $this->libros->conDisponibilidad(),
            'socios' => $this->socios->activos(),
        ]);
    }

    /**
     * Devuelve en JSON los ejemplares libres de un libro (usado por prestamos.js).
     */
    public function disponibilidad($id)
    {
        $libro = $this->libros->find((int) $id);
        if (! $libro) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Libro no encontrado.']);
        }

        return $this->response->setJSON([
            'id'          => (int) $libro['id'],
            'titulo'      => $libro['titulo'],
            'cantidad'    => (int) $libro['cantidad'],
            'disponibles' => $this->libros->disponiblesDe((int) $id),
        ]);
    }

    public function registrar()
    {
        $reglas = [
            'libro_id' => 'required|is_natural_no_zero',
            'socio_id' => 'required|is_natural_no_zero',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()
                ->with('error', 'Seleccioná un libro y un socio para registrar el préstamo.');
        }

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

        return redirect()->to('/admin/prestamos')->with('mensaje', 'Devolución registrada.');
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
