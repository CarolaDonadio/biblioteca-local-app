<?php

namespace App\Models;

use CodeIgniter\Model;

class DonacionModel extends Model
{
    protected $table      = 'donaciones';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'donante',
        'tipo',
        'descripcion',
        'cantidad',
        'fecha_donacion',
        'estado',
        'observaciones',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}