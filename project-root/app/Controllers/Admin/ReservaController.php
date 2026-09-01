<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ReservaModel;
use App\Models\PrestamoModel;

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
        $this->reservas->activar((int) $id);
        return redirect()->back()->with('mensaje', 'Reserva confirmada y socio notificado.');
    }

    public function cancelar($id)
    {
        $this->reservas->cancelar((int) $id);
        return redirect()->back()->with('mensaje', 'Reserva cancelada.');
    }

    /** El socio retira el ejemplar: se cierra la reserva y se registra el préstamo. */
    public function completar($id)
    {
        $reserva = $this->reservas->find($id);

        $prestamoModel = new PrestamoModel();
        try {
            $prestamoModel->registrarPrestamo($reserva['libro_id'], $reserva['socio_id'], session()->get('admin_id'));
            $this->reservas->completar((int) $id);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('mensaje', 'Reserva completada: préstamo registrado.');
    }
}
