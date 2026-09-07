<?php

namespace App\Models;

use CodeIgniter\Model;

class RegistroModel extends Model
{
    protected $table            = 'registros';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'idlibro',
        'dniUsuario',
        'fechaPrestamo',
        'fechaVence',
        'fechaDevolucion',
    ];
}
