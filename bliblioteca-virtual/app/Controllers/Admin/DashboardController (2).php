<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PrestamoModel;
use App\Models\ReservaModel;
use App\Models\EjemplarModel;
use App\Models\NotificacionModel;
use App\Models\SocioModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $data = [
            'prestamos_activos'   => (new PrestamoModel())->where('estado', 'activo')->countAllResults(),
            'prestamos_vencidos'  => count((new PrestamoModel())->vencidos()),
            'reservas_pendientes' => (new ReservaModel())->whereIn('estado', ['pendiente', 'disponible_para_retiro'])->countAllResults(),
            'ejemplares_por_estado' => (new EjemplarModel())->reportePorEstado(),
            'notificaciones_pendientes' => (new NotificacionModel())->whereIn('estado_entrega', ['pendiente', 'fallido'])->countAllResults(),
            'socios_activos'      => (new SocioModel())->where('estado', 'activo')->countAllResults(),
        ];

        return view('admin/dashboard', $data);
    }
}
