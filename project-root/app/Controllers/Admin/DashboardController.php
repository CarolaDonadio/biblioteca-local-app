<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RegistroModel;
use App\Models\LibroModel;
use App\Models\ReservaModel;
use App\Models\SocioModel;

class DashboardController extends BaseController
{
    protected RegistroModel $prestamos;
    protected LibroModel $libros;
    protected ReservaModel $reservas;
    protected SocioModel $socios;

    public function __construct()
    {
        $this->prestamos = new RegistroModel();
        $this->libros    = new LibroModel();
        $this->reservas = new ReservaModel();
        $this->socios    = new SocioModel();
    }

    public function index()
    {       $pactivos = count($this->prestamos->activos());
            $pvencidos = count($this->prestamos->vencidos());
            $data = [
                'prestamos_activos'         => $pactivos,
                'prestamos_vencidos'        => $pvencidos,
                'reservas_pendientes'      => 0, // count($this->reservas->pendientes()),
                'socios_activos'           => count($this->socios->activos()),
                'ejemplares_por_estado'      => [
                    ['estado' => 'disponible', 'cantidad' => $this->libros->disponibles() - ($pactivos + $pvencidos)],
                    ['estado' => 'prestado',   'cantidad' => $pactivos + $pvencidos],
                    ['estado' => 'reservado',   'cantidad' => 0]
                ],
            ];
             return view('admin/dashboard', $data);
    }
}
