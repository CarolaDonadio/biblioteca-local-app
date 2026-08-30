<?php

namespace App\Models;

use CodeIgniter\Model;

class MultimediaModel extends Model
{
    protected $table            = 'multimedia';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'libro_id', 'tipo', 'archivo_url', 'tamano_kb', 'duracion_seg',
    ];

    protected $validationRules = [
        'libro_id'    => 'required|integer',
        'tipo'        => 'required|in_list[pdf,audiolibro]',
        'archivo_url' => 'required|max_length[255]',
    ];

    public function porLibro(int $libroId)
    {
        return $this->where('libro_id', $libroId)->findAll();
    }
}
