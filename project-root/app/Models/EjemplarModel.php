<?php

namespace App\Models;

use CodeIgniter\Model;

class EjemplarModel extends Model
{
    protected $table            = 'ejemplares';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'libro_id',
        'codigo_inventario',
        'ubicacion',
        'estado',
        'observaciones',
    ];

    protected $validationRules = [
        'libro_id'          => 'required|is_natural_no_zero',
        'codigo_inventario' => 'required|max_length[50]|is_unique[ejemplares.codigo_inventario,id,{id}]',
        'ubicacion'         => 'permit_empty|max_length[150]',
        'estado'            => 'permit_empty|in_list[disponible,prestado,reservado,perdido,danado,baja]',
    ];

    protected $validationMessages = [
        'codigo_inventario' => [
            'is_unique' => 'Ya existe un ejemplar con ese código de inventario.',
        ],
        'libro_id' => [
            'required' => 'Elegí a qué libro pertenece este ejemplar.',
        ],
    ];

    /**
     * Ejemplares con el título/autor del libro asociado, para el listado del inventario.
     */
    public function conLibro()
    {
        return $this->select('ejemplares.*, libros.titulo, libros.autor')
                     ->join('libros', 'libros.id = ejemplares.libro_id');
    }

    /**
     * Marca un ejemplar como perdido/dañado (o cualquier otro estado del enum),
     * dejando asentadas las observaciones cargadas por el bibliotecario.
     */
    public function marcarEstado(int $id, string $estado, ?string $observaciones = null): bool
    {
        $datos = ['estado' => $estado];

        if ($observaciones !== null && trim($observaciones) !== '') {
            $datos['observaciones'] = $observaciones;
        }

        return $this->update($id, $datos);
    }

    /**
     * Reporte básico de inventario: cantidad de ejemplares agrupados por estado.
     *
     * @return list<array{estado: string, cantidad: int}>
     */
    public function reportePorEstado(): array
    {
        return $this->select('estado, COUNT(*) as cantidad')
                     ->groupBy('estado')
                     ->orderBy('estado', 'ASC')
                     ->findAll();
    }
}
