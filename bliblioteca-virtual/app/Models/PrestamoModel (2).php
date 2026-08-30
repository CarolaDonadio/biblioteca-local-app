<?php

namespace App\Models;

use CodeIgniter\Model;

class PrestamoModel extends Model
{
    protected $table            = 'prestamos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'ejemplar_id', 'socio_id', 'admin_id', 'fecha_prestamo',
        'fecha_vencimiento', 'fecha_devolucion', 'estado', 'renovaciones',
    ];

    public const DIAS_PRESTAMO      = 14;
    public const MAX_RENOVACIONES   = 2;

    /**
     * Registra un préstamo de forma atómica: toma el ejemplar disponible,
     * lo marca como prestado y crea el registro de préstamo.
     * Devuelve el id del préstamo o lanza excepción si no hay ejemplares libres.
     */
    public function registrarPrestamo(int $libroId, int $socioId, ?int $adminId = null): int
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $ejemplarModel = new EjemplarModel();
        $ejemplar = $ejemplarModel->primeroDisponible($libroId);

        if (! $ejemplar) {
            $db->transComplete();
            throw new \RuntimeException('No hay ejemplares disponibles para este libro.');
        }

        $ejemplarModel->marcarEstado($ejemplar['id'], 'prestado');

        $prestamoId = $this->insert([
            'ejemplar_id'       => $ejemplar['id'],
            'socio_id'          => $socioId,
            'admin_id'          => $adminId,
            'fecha_prestamo'    => date('Y-m-d H:i:s'),
            'fecha_vencimiento' => date('Y-m-d', strtotime('+' . self::DIAS_PRESTAMO . ' days')),
            'estado'            => 'activo',
        ]);

        $db->transComplete();

        return $prestamoId;
    }

    /**
     * Registra la devolución. Si había reservas en cola para ese libro,
     * el ejemplar pasa a "reservado" en lugar de "disponible" y se activa
     * la primera reserva de la cola (ver ReservaModel::activarSiguiente).
     */
    public function registrarDevolucion(int $prestamoId): void
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $prestamo = $this->find($prestamoId);
        if (! $prestamo || $prestamo['estado'] !== 'activo') {
            $db->transComplete();
            throw new \RuntimeException('El préstamo no existe o ya fue devuelto.');
        }

        $this->update($prestamoId, [
            'fecha_devolucion' => date('Y-m-d H:i:s'),
            'estado'           => 'devuelto',
        ]);

        $ejemplarModel = new EjemplarModel();
        $ejemplar = $ejemplarModel->find($prestamo['ejemplar_id']);

        $reservaModel = new ReservaModel();
        $siguienteReserva = $reservaModel->siguienteEnCola($ejemplar['libro_id']);

        if ($siguienteReserva) {
            $ejemplarModel->marcarEstado($ejemplar['id'], 'reservado');
            $reservaModel->activar($siguienteReserva['id']);
        } else {
            $ejemplarModel->marcarEstado($ejemplar['id'], 'disponible');
        }

        $db->transComplete();
    }

    public function renovar(int $prestamoId): bool
    {
        $prestamo = $this->find($prestamoId);
        if (! $prestamo || $prestamo['estado'] !== 'activo') {
            throw new \RuntimeException('Este préstamo no se puede renovar.');
        }
        if ($prestamo['renovaciones'] >= self::MAX_RENOVACIONES) {
            throw new \RuntimeException('Se alcanzó el máximo de renovaciones.');
        }

        return $this->update($prestamoId, [
            'fecha_vencimiento' => date('Y-m-d', strtotime($prestamo['fecha_vencimiento'] . ' +' . self::DIAS_PRESTAMO . ' days')),
            'renovaciones'      => $prestamo['renovaciones'] + 1,
        ]);
    }

    public function activos()
    {
        return $this->select('prestamos.*, socios.nombre, socios.apellido, libros.titulo, ejemplares.codigo_inventario')
            ->join('socios', 'socios.id = prestamos.socio_id')
            ->join('ejemplares', 'ejemplares.id = prestamos.ejemplar_id')
            ->join('libros', 'libros.id = ejemplares.libro_id')
            ->where('prestamos.estado', 'activo')
            ->orderBy('prestamos.fecha_vencimiento', 'ASC')
            ->findAll();
    }

    public function vencidos()
    {
        return $this->where('estado', 'activo')
            ->where('fecha_vencimiento <', date('Y-m-d'))
            ->findAll();
    }
}
