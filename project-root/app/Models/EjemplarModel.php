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

    /**
     * Ejemplares con los datos del libro al que pertenecen (para el listado
     * de inventario, que muestra el título en cada fila).
     */
    public function conLibro()
    {
        return $this->select('ejemplares.*, libros.titulo, libros.autor')
                     ->join('libros', 'libros.id = ejemplares.libro_id');
    }

    /**
     * Cambia el estado de un ejemplar (perdido / dañado) dejando constancia
     * de la observación cargada por el bibliotecario.
     */
    public function marcarEstado(int $id, string $estado, ?string $observaciones = null): bool
    {
        return $this->update($id, [
            'estado'        => $estado,
            'observaciones' => $observaciones,
        ]);
    }

    /**
     * Normaliza el resumen para mantener el catálogo completo de estados.
     */
    public static function normalizarReportePorEstado(array $filas): array
    {
        $estados = ['disponible', 'prestado', 'reservado', 'perdido', 'danado', 'baja'];
        $cantidades = [];

        foreach ($filas as $fila) {
            $estado = strtolower((string) ($fila['estado'] ?? ''));
            if (in_array($estado, $estados, true)) {
                $cantidades[$estado] = (int) ($fila['cantidad'] ?? 0);
            }
        }

        $resultado = [];
        foreach ($estados as $estado) {
            $resultado[] = [
                'estado' => $estado,
                'cantidad' => $cantidades[$estado] ?? 0,
            ];
        }

        return $resultado;
    }

    /**
     * Reporte básico de inventario: cantidad de ejemplares por estado.
     */
    public function reportePorEstado(): array
    {
        $filas = $this->select('estado, COUNT(*) as cantidad')
                      ->groupBy('estado')
                      ->orderBy('estado', 'ASC')
                      ->findAll();

        return self::normalizarReportePorEstado($filas);
    }

    /**
     * Cuántos ejemplares disponibles tiene un libro puntual (útil para
     * préstamos/reservas).
     */
    public function disponiblesPorLibro(int $libroId): int
    {
        return $this->where('libro_id', $libroId)
                     ->where('estado', 'disponible')
                     ->countAllResults();
    }
}
