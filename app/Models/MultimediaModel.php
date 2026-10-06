<?php

namespace App\Models;

use CodeIgniter\Model;

class MultimediaModel extends Model
{
    protected $table            = 'multimedia';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'libro_id',
        'tipo',
        'archivo_url',
        'tamano_kb',
    ];

    public function porLibro(int $libroId): array
    {
        return $this->where('libro_id', $libroId)
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }
}
