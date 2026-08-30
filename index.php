<?php

namespace App\Models;

use CodeIgniter\Model;

class PromocionModel extends Model
{
    protected $table            = 'promociones';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'titulo', 'descripcion', 'imagen_url', 'fecha_inicio', 'fecha_fin', 'activo',
    ];

    protected $validationRules = [
        'titulo'       => 'required|max_length[200]',
        'fecha_inicio' => 'required|valid_date',
        'fecha_fin'    => 'required|valid_date',
    ];

    /** Módulo público: solo promociones activas y vigentes hoy */
    public function vigentes()
    {
        $hoy = date('Y-m-d');
        return $this->where('activo', 1)
            ->where('fecha_inicio <=', $hoy)
            ->where('fecha_fin >=', $hoy)
            ->orderBy('fecha_inicio', 'DESC')
            ->findAll();
    }
}
