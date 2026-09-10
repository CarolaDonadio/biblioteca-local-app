<?php

namespace App\Models;

use CodeIgniter\Model;

class LibroModel extends Model
{
    protected $table            = 'libros';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'isbn',
        'titulo',
        'autor',
        'editorial',
        'anio',
        'categoria',
        'cantidad',
        'sinopsis',
        'disponible',
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

    /**
     * Listado de libros con la cantidad de ejemplares libres calculada
     * a partir de los préstamos sin devolver.
     */
    public function conDisponibilidad(): array
    {
        $prestados = '(SELECT COUNT(*) FROM registros r WHERE r.idlibro = libros.id AND r.fechaDevolucion IS NULL)';

        return $this->select("libros.*, GREATEST(libros.cantidad - {$prestados}, 0) AS disponibles", false)
                    ->orderBy('titulo', 'ASC')
                    ->findAll();
    }

    /**
     * Ejemplares libres de un libro puntual.
     */
    public function disponiblesDe(int $id): int
    {
        $libro = $this->find($id);
        if (! $libro) {
            return 0;
        }

        $prestados = (new RegistroModel())->prestadosDeLibro($id);

        return max(0, (int) $libro['cantidad'] - $prestados);
    }
}