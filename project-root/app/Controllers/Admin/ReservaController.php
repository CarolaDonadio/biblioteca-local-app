<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LibroModel;
use App\Models\ReservaModel;
use App\Models\SocioModel;

class ReservaController extends BaseController
{
    protected ReservaModel $reservas;
    protected LibroModel $libros;
    protected SocioModel $socios;

    public function __construct()
    {
        $this->reservas = new ReservaModel();
        $this->libros   = new LibroModel();
        $this->socios   = new SocioModel();
    }

    public function index()
    {
        // El panel recibe la cola completa en tiempo real, agrupada por libro.
        $data['reservas'] = $this->reservas->pendientesConDatos();
        $data['libros'] = $this->libros->conDisponibilidad();
        $data['socios'] = $this->socios->activos();
        return view('admin/reservas/index', $data);
    }

    public function crear()
    {
        $reglas = [
            'libro_id' => 'required|is_natural_no_zero',
            'socio_id' => 'required|is_natural_no_zero',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('error', 'Seleccioná un libro y un socio para crear la reserva.');
        }

        $libroId = (int) $this->request->getPost('libro_id');
        $socioId = (int) $this->request->getPost('socio_id');

        try {
            $this->reservas->solicitar($libroId, $socioId);
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/admin/reservas')->with('mensaje', 'Reserva creada correctamente.');
    }

    public function confirmar($id)
    {
        try {
            $this->reservas->confirmar((int) $id, (int) session()->get('admin_id'));
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('mensaje', 'Reserva confirmada.');
    }

    public function cancelar($id)
    {
        try {
            $this->reservas->cancelar((int) $id, (int) session()->get('admin_id'));
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('mensaje', 'Reserva cancelada.');
    }

    /** Marca como completada una reserva cuyo retiro se registró por separado. */
    public function completar($id)
    {
        try {
            $this->reservas->completar((int) $id, (int) session()->get('admin_id'));
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('mensaje', 'Reserva completada.');
    }
}
