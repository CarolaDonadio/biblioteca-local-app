<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificacionModel extends Model
{
    protected $table            = 'notificaciones';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'dniUsuario',
        'tipo',
        'mensaje',
        'canal',
        'estado_entrega',
    ];

    public function porUsuario(int $dniUsuario, array $tipos = [], int $limite = 50): array
    {
        $consulta = $this->where('dniUsuario', $dniUsuario)
            ->where('canal', 'sistema')
            ->orderBy('created_at', 'DESC');

        if ($tipos !== []) {
            $consulta->whereIn('tipo', $tipos);
        }

        return $consulta->findAll($limite);
    }
}
