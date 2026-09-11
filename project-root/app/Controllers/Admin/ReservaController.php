<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ReservaModel;

class ReservaController extends BaseController
{
    protected ReservaModel $reservas;

    public function __construct()
    {
        $this->reservas = new ReservaModel();
    }

    public function index()
    {
        // El panel recibe la cola completa en tiempo real, agrupada por libro.
        $data['reservas'] = $this->reservas->pendientesConDatos();
        return view('admin/reservas/index', $data);
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
