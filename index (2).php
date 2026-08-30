<?php

namespace App\Models;

use CodeIgniter\Model;

class SugerenciaModel extends Model
{
    protected $table            = 'sugerencias';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'socio_id', 'titulo_sugerido', 'autor_sugerido', 'comentario', 'estado', 'fecha',
    ];
}
