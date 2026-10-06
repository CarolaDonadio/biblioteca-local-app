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

    /** Listado de libros con ejemplares en estado realmente disponible. */
    public function conDisponibilidad(): array
    {
        $libros = $this->orderBy('titulo', 'ASC')->findAll();
        $ejemplares = new EjemplarModel();

        foreach ($libros as &$libro) {
            $libro['disponibles'] = $ejemplares->disponiblesPorLibro((int) $libro['id']);
            $libro['disponible'] = $libro['disponibles'] > 0 ? 1 : 0;
        }
        unset($libro);

        return $libros;
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

        return (new EjemplarModel())->disponiblesPorLibro($id);
    }

    public function sincronizarDisponibilidad(int $id): void
    {
        $disponibles = (new EjemplarModel())->disponiblesPorLibro($id);
        $this->update($id, ['disponible' => $disponibles > 0 ? 1 : 0]);
    }
}