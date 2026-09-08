<?php

namespace App\Models;

use CodeIgniter\Model;

class PromocionModel extends Model
{
    protected $table = 'promociones';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'titulo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'imagen_url',
        'condiciones'
    ];

    protected $useTimestamps = true;

    public function vigentes()
    {
        return $this->where('fecha_inicio <=', date('Y-m-d'))
                    ->where('fecha_fin >=', date('Y-m-d'))
                    ->orderBy('fecha_fin', 'ASC')
                    ->findAll();
    }
}