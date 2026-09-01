<?php

namespace App\Models;

use CodeIgniter\Model;

class LibroModel extends Model
{
    protected $table            = 'libros';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'isbn', 'titulo', 'autor', 'editorial', 'anio',
        'categoria', 'sinopsis', 'portada_url',
    ];

    protected $validationRules = [
        'isbn'   => 'required|max_length[20]|is_unique[libros.isbn,id,{id}]',
        'titulo' => 'required|max_length[250]',
        'autor'  => 'required|max_length[150]',
        'anio'   => 'permit_empty|integer',
    ];

    /**
     * Buscador público: filtra por título, autor, ISBN o categoría.
     * Usado por el módulo de "Consulta pública".
     */
    public function buscar(string $termino, ?string $categoria = null)
    {
        $builder = $this->groupStart()
            ->like('titulo', $termino)
            ->orLike('autor', $termino)
            ->orLike('isbn', $termino)
            ->groupEnd();

        if ($categoria) {
            $builder->where('categoria', $categoria);
        }

        return $builder->orderBy('titulo', 'ASC')->findAll();
    }

    /**
     * Disponibilidad en tiempo real: cuántos ejemplares libres tiene un libro.
     */
    public function disponibilidad(int $libroId): array
    {
        $db = \Config\Database::connect();

        $total = $db->table('ejemplares')->where('libro_id', $libroId)->countAllResults();
        $disponibles = $db->table('ejemplares')
            ->where('libro_id', $libroId)
            ->where('estado', 'disponible')
            ->countAllResults();

        return [
            'total'       => $total,
            'disponibles' => $disponibles,
            'en_prestamo' => $total - $disponibles,
        ];
    }
}
