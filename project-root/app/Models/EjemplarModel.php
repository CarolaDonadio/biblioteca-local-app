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
        if (! in_array($estado, ['perdido', 'danado'], true)) {
            throw new \InvalidArgumentException('El estado indicado no es válido para esta operación.');
        }

        $ejemplar = $this->find($id);
        if (! $ejemplar) {
            return false;
        }

        if (! $this->db->transBegin()) {
            throw new \RuntimeException('No se pudo actualizar el estado del ejemplar.');
        }

        try {
            $this->bloquearLibro((int) $ejemplar['libro_id']);
            $actual = $this->db->query(
                'SELECT * FROM ejemplares WHERE id = ? FOR UPDATE',
                [$id]
            )->getRowArray();

            if (! $actual
                || (int) $actual['libro_id'] !== (int) $ejemplar['libro_id']
                || $actual['estado'] !== 'disponible') {
                $this->db->transRollback();
                return false;
            }

            $actualizado = $this->db->table('ejemplares')
                ->where('id', $id)
                ->where('estado', 'disponible')
                ->update([
                    'estado'        => $estado,
                    'observaciones' => $observaciones,
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
            if (! $actualizado || $this->db->affectedRows() !== 1) {
                throw new \RuntimeException('No se pudo actualizar el estado del ejemplar.');
            }

            (new LibroModel())->sincronizarDisponibilidad((int) $ejemplar['libro_id']);

            if (! $this->db->transCommit()) {
                throw new \RuntimeException('No se pudo actualizar el estado del ejemplar.');
            }
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }

        return true;
    }

    public function actualizarAdministrativamente(int $id, array $data): void
    {
        $inicial = $this->find($id);
        if (! $inicial) {
            throw new \RuntimeException('El ejemplar no existe.');
        }

        $libroAnterior = (int) $inicial['libro_id'];
        $libroNuevo = isset($data['libro_id']) ? (int) $data['libro_id'] : $libroAnterior;
        $librosABloquear = array_values(array_unique([$libroAnterior, $libroNuevo]));
        sort($librosABloquear);

        if (! $this->db->transBegin()) {
            throw new \RuntimeException('No se pudo actualizar el ejemplar.');
        }

        try {
            foreach ($librosABloquear as $libroId) {
                $this->bloquearLibro($libroId);
            }

            $actual = $this->db->query(
                'SELECT * FROM ejemplares WHERE id = ? FOR UPDATE',
                [$id]
            )->getRowArray();
            if (! $actual || (int) $actual['libro_id'] !== $libroAnterior) {
                throw new \RuntimeException('El ejemplar cambió durante la edición. Volvé a intentarlo.');
            }

            $estadoNuevo = $data['estado'] ?? $actual['estado'];
            if (! in_array($estadoNuevo, ['disponible', 'prestado', 'reservado', 'perdido', 'danado', 'baja'], true)) {
                throw new \RuntimeException('El estado seleccionado no es válido.');
            }

            $ocupado = in_array($actual['estado'], ['prestado', 'reservado'], true);
            $cambiaLibro = $libroNuevo !== $libroAnterior;
            $cambiaEstado = $estadoNuevo !== $actual['estado'];
            if ($ocupado && ($cambiaLibro || $cambiaEstado)) {
                throw new \RuntimeException(
                    'No se puede modificar manualmente el libro o estado de un ejemplar prestado o reservado.'
                );
            }
            if (! $ocupado && in_array($estadoNuevo, ['prestado', 'reservado'], true)) {
                throw new \RuntimeException(
                    'Los estados prestado y reservado solo pueden asignarse desde sus operaciones correspondientes.'
                );
            }

            if (! $this->update($id, $data)) {
                throw new \RuntimeException('No se pudo actualizar el ejemplar.');
            }

            (new LibroModel())->sincronizarDisponibilidad($libroAnterior);
            if ($libroNuevo !== $libroAnterior) {
                (new LibroModel())->sincronizarDisponibilidad($libroNuevo);
            }

            if (! $this->db->transCommit()) {
                throw new \RuntimeException('No se pudo actualizar el ejemplar.');
            }
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    public function crearAdministrativamente(array $data): bool
    {
        $libroId = (int) ($data['libro_id'] ?? 0);
        if (! $this->db->transBegin()) {
            throw new \RuntimeException('No se pudo registrar el ejemplar.');
        }

        try {
            $this->bloquearLibro($libroId);

            if (! $this->insert($data)) {
                $this->db->transRollback();
                return false;
            }

            (new LibroModel())->sincronizarDisponibilidad($libroId);

            if (! $this->db->transCommit()) {
                throw new \RuntimeException('No se pudo registrar el ejemplar.');
            }
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }

        return true;
    }

    public function eliminarAdministrativamente(int $id): void
    {
        $inicial = $this->find($id);
        if (! $inicial) {
            throw new \RuntimeException('El ejemplar no existe.');
        }

        $libroId = (int) $inicial['libro_id'];
        if (! $this->db->transBegin()) {
            throw new \RuntimeException('No se pudo eliminar el ejemplar.');
        }

        try {
            $this->bloquearLibro($libroId);
            $actual = $this->db->query(
                'SELECT * FROM ejemplares WHERE id = ? FOR UPDATE',
                [$id]
            )->getRowArray();

            if (! $actual || (int) $actual['libro_id'] !== $libroId) {
                throw new \RuntimeException('El ejemplar cambió durante la operación. Volvé a intentarlo.');
            }
            if (in_array($actual['estado'], ['prestado', 'reservado'], true)) {
                throw new \RuntimeException('No se puede eliminar un ejemplar que está prestado o reservado.');
            }
            if (! $this->delete($id)) {
                throw new \RuntimeException('No se pudo eliminar el ejemplar.');
            }

            (new LibroModel())->sincronizarDisponibilidad($libroId);

            if (! $this->db->transCommit()) {
                throw new \RuntimeException('No se pudo eliminar el ejemplar.');
            }
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    /**
     * Bloquea el libro para serializar cambios de stock durante una transacción.
     */
    public function bloquearLibro(int $libroId): array
    {
        $libro = $this->db->query(
            'SELECT id, titulo FROM libros WHERE id = ? FOR UPDATE',
            [$libroId]
        )->getRowArray();

        if (! $libro) {
            throw new \RuntimeException('El libro seleccionado no existe.');
        }

        return $libro;
    }

    public function tieneDisponiblesPorLibro(int $libroId): bool
    {
        return $this->disponiblesPorLibro($libroId) > 0;
    }

    /**
     * Consume una copia disponible como préstamo directo o como reserva.
     * El llamador debe mantener una transacción abierta.
     */
    public function tomarDisponible(int $libroId, string $estadoDestino): bool
    {
        if (! in_array($estadoDestino, ['prestado', 'reservado'], true)) {
            throw new \InvalidArgumentException('El estado de destino no es válido.');
        }

        return $this->transicionarUno($libroId, 'disponible', $estadoDestino);
    }

    /**
     * Libera una copia ocupada al devolver un préstamo o cancelar una reserva.
     * El llamador debe mantener una transacción abierta.
     */
    public function liberarUno(int $libroId, string $estadoOrigen): bool
    {
        if (! in_array($estadoOrigen, ['prestado', 'reservado'], true)) {
            throw new \InvalidArgumentException('El estado de origen no es válido.');
        }

        return $this->transicionarUno($libroId, $estadoOrigen, 'disponible');
    }

    /**
     * Convierte la copia retenida por una reserva en un préstamo.
     * El llamador debe mantener una transacción abierta.
     */
    public function convertirReservadoAPrestado(int $libroId): bool
    {
        return $this->transicionarUno($libroId, 'reservado', 'prestado');
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

    private function transicionarUno(int $libroId, string $estadoOrigen, string $estadoDestino): bool
    {
        $this->bloquearLibro($libroId);

        $ejemplar = $this->db->query(
            'SELECT id FROM ejemplares
             WHERE libro_id = ? AND estado = ?
             ORDER BY id
             LIMIT 1
             FOR UPDATE',
            [$libroId, $estadoOrigen]
        )->getRowArray();

        if (! $ejemplar) {
            return false;
        }

        $actualizado = $this->db->table('ejemplares')
            ->where('id', (int) $ejemplar['id'])
            ->where('estado', $estadoOrigen)
            ->update([
                'estado'     => $estadoDestino,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return $actualizado && $this->db->affectedRows() === 1;
    }
}
