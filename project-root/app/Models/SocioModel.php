<?php

namespace App\Models;

use CodeIgniter\Model;

class SocioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'dni';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'dni',
        'nombre_completo',
        'telefono',
        'mail',
        'password_hash',
        'perfil',
        'estado',
    ];

    /**
     * Obtiene todos los socios activos ordenados por apellido
     */
    public function activos()
    {
        return $this->where('perfil', 'socio')
                    ->where('estado', 'activo')
                    ->orderBy('nombre_completo', 'ASC')
                    ->findAll();
    }
}
