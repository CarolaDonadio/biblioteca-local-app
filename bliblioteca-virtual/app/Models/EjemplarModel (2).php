<?php

namespace App\Models;

use CodeIgniter\Model;

class EjemplarModel extends Model
{
    protected $table            = 'ejemplares';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'libro_id', 'codigo_inventario', 'estado', 'ubicacion', 'observaciones',
    ];

    protected $validationRules = [
        'libro_id'          => 'required|integer',
        'codigo_inventario' => 'required|max_length[40]|is_unique[ejemplares.codigo_inventario,id,{id}]',
        'estado'            => 'in_list[disponible,prestado,reservado,perdido,danado,baja]',
    ];

    /** Módulo "Administración e inventario": listado con nombre del libro incluido */
    public function conLibro()
    {
        return $this->select('ejemplares.*, libros.titulo, libros.isbn, libros.autor')
            ->join('libros', 'libros.id = ejemplares.libro_id');
    }

    /** Primer ejemplar disponible de un libro (usado por el motor de préstamos) */
    public function primeroDisponible(int $libroId): ?array
    {
        return $this->where('libro_id', $libroId)
            ->where('estado', 'disponible')
            ->orderBy('id', 'ASC')
            ->first();
    }

    public function marcarEstado(int $id, string $estado, ?string $observaciones = null): bool
    {
        $data = ['estado' => $estado];
        if ($observaciones !== null) {
            $data['observaciones'] = $observaciones;
        }
        return $this->update($id, $data);
    }

    /** Reporte básico solicitado por el MVP: conteo de ejemplares por estado */
    public function reportePorEstado(): array
    {
        return $this->select('estado, COUNT(*) as cantidad')
            ->groupBy('estado')
            ->findAll();
    }
}
