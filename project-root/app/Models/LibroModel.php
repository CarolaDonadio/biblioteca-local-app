<?php

namespace App\Models;

use CodeIgniter\Model;

class LibroModel extends Model
{
    protected $table            = 'libros';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    // Mapeo directo con las columnas de la tabla 'libros' de tu SQL
    protected $allowedFields = [
        'isbn',
        'titulo',
        'autor',
        'editorial',
        'anio',
        'categoria',
        'cantidad',
        'sinopsis',
        'disponible'
    ];

    /**
     * Método para buscar libros en el Catálogo Público
     * Filtra por título, autor, ISBN o categoría
     */
    public function buscarLibros(string $termino = '', ?string $categoria = null)
    {
        $builder = $this;

        if (!empty($termino)) {
            $builder->groupStart()
                    ->like('titulo', $termino)
                    ->orLike('autor', $termino)
                    ->orLike('isbn', $termino)
                    ->groupEnd();
        }

        if (!empty($categoria)) {
            $builder->where('categoria', $categoria);
        }

        return $builder->orderBy('titulo', 'ASC')->findAll();
    }
}