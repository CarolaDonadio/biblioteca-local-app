<?php

namespace App\Models;

use CodeIgniter\Model;

class RecomendacionModel extends Model
{
    protected $table      = 'recomendaciones';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'socio_id',
        'libro_id',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function idsRecomendadosPorSocio(int $socioId): array
    {
        return array_map(
            static fn (array $fila): int => (int) $fila['libro_id'],
            $this->select('libro_id')->where('socio_id', $socioId)->findAll()
        );
    }

    public function yaRecomendo(int $socioId, int $libroId): bool
    {
        return $this->where('socio_id', $socioId)
            ->where('libro_id', $libroId)
            ->countAllResults() > 0;
    }
}