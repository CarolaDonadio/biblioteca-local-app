<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RegistroModel;
use App\Models\ReservaModel;

class GestionController extends BaseController
{
    public function index()
    {
        return view('admin/gestion/index', $this->datosTablero());
    }

    public function actualizar()
    {
        return view('admin/gestion/_tablero', $this->datosTablero());
    }

    private function datosTablero(): array
    {
        $prestamos = new RegistroModel();

        return [
            'reservas'  => (new ReservaModel())->pendientesConDatos(),
            'prestamos' => $prestamos->activos(),
            'vencidos'  => $prestamos->vencidos(),
        ];
    }
}