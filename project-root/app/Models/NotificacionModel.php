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

    public function reintentar(int $id): bool
    {
        $notificacion = $this->find($id);

        if (! $notificacion) {
            return false;
        }

        return $this->update($id, [
            'estado_entrega' => 'pendiente',
        ]);
    }
}
