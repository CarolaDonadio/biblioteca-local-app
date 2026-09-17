<?php

namespace App\Models;

use App\Libraries\AutomaticNotificationService;
use CodeIgniter\Model;

class ReservaModel extends Model
{
    protected $table            = 'reservas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'libro_id',
        'socio_id',
        'fecha_solicitud',
        'estado',
        'fecha_confirmacion',
        'fecha_cancelacion',
        'fecha_completada',
        'procesada_por',
    ];

    protected $validationRules = [
        'libro_id' => 'required|is_natural_no_zero',
        'socio_id' => 'required|is_natural_no_zero',
        'estado'   => 'required|in_list[pendiente,confirmada,cancelada,completada]',
    ];

    public function solicitar(int $libroId, int $socioId): int
    {
        $libro = (new LibroModel())->find($libroId);
        if (! $libro) {
            throw new \RuntimeException('El libro seleccionado no existe.');
        }

        $socio = (new SocioModel())->where('dni', $socioId)->where('perfil', 'socio')->first();
        if (! $socio || $socio['estado'] !== 'activo') {
            throw new \RuntimeException('El socio no está habilitado para reservar libros.');
        }

        if ($this->existeActivaParaSocio($libroId, $socioId)) {
            throw new \RuntimeException('Ya tenés una reserva activa para este libro.');
        }

        if (! $this->insert([
            'libro_id'       => $libroId,
            'socio_id'       => $socioId,
            'estado'         => 'pendiente',
            'fecha_solicitud' => date('Y-m-d H:i:s'),
        ])) {
            throw new \RuntimeException('No se pudo registrar la reserva.');
        }

        $id = (int) $this->getInsertID();
        $mensaje = 'Se registró una reserva para «' . $libro['titulo'] . '».';
        $notificaciones = new AutomaticNotificationService();
        $notificaciones->notifyUser($socioId, 'reserva_solicitada', $mensaje);
        $notificaciones->notifyProfile('bibliotecario', 'reserva_nueva', $mensaje);

        return $id;
    }

    private function existeActivaParaSocio(int $libroId, int $socioId): bool
    {
        return $this->where('libro_id', $libroId)
            ->where('socio_id', $socioId)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->countAllResults() > 0;
    }

    /**
     * Obtiene reservas activas junto con la información del socio y del libro.
     *
     * La posición se calcula por libro entre las reservas pendientes y
     * confirmadas, sin imponer una restricción UNIQUE histórica.
     */
    public function pendientesConDatos(): array
    {
        $reservas = $this->select(
                'reservas.*, libros.titulo, usuarios.nombre_completo AS socio_nombre'
            )
            ->join('libros', 'libros.id = reservas.libro_id')
            ->join('usuarios', 'usuarios.dni = reservas.socio_id')
            ->whereIn('reservas.estado', ['pendiente', 'confirmada'])
            ->orderBy('reservas.libro_id', 'ASC')
            ->orderBy('reservas.fecha_solicitud', 'ASC')
            ->findAll();

        $posiciones = [];

        foreach ($reservas as &$reserva) {
            $nombre = preg_split('/\s+/', trim((string) $reserva['socio_nombre']), 2);
            $reserva['nombre'] = $nombre[0] ?? '';
            $reserva['apellido'] = $nombre[1] ?? '';
            $reserva['fecha_reserva'] = $reserva['fecha_solicitud'];

            $libroId = (int) $reserva['libro_id'];
            $posiciones[$libroId] = ($posiciones[$libroId] ?? 0) + 1;
            $reserva['posicion_cola'] = $posiciones[$libroId];
        }
        unset($reserva);

        return $reservas;
    }

    public function reservasPorSocio(int $socioId): array
    {
        return $this->select('reservas.*, libros.titulo, libros.autor')
            ->join('libros', 'libros.id = reservas.libro_id', 'left')
            ->where('reservas.socio_id', $socioId)
            ->whereIn('reservas.estado', ['pendiente', 'confirmada', 'cancelada', 'completada'])
            ->orderBy('reservas.fecha_solicitud', 'DESC')
            ->findAll();
    }

    public function existeActiva(int $libroId, int $socioId, ?int $exceptoId = null): bool
    {
        $builder = $this->where('libro_id', $libroId)
            ->where('socio_id', $socioId)
            ->whereIn('estado', ['pendiente', 'confirmada']);

        if ($exceptoId !== null) {
            $builder->where('id !=', $exceptoId);
        }

        return $builder->countAllResults() > 0;
    }

    public function confirmar(int $id, int $adminId): bool
    {
        $reserva = $this->find($id);

        if (! $reserva) {
            throw new \RuntimeException('Reserva no encontrada.');
        }

        if ($reserva['estado'] !== 'pendiente') {
            throw new \RuntimeException('Solo se pueden confirmar reservas pendientes.');
        }

        if ($this->existeActiva((int) $reserva['libro_id'], (int) $reserva['socio_id'], $id)) {
            throw new \RuntimeException('El socio ya tiene otra reserva activa para este libro.');
        }

        if (! $this->update($id, [
            'estado'             => 'confirmada',
            'fecha_confirmacion' => date('Y-m-d H:i:s'),
            'procesada_por'      => $adminId,
        ])) {
            throw new \RuntimeException('No se pudo confirmar la reserva.');
        }

        $libro = (new LibroModel())->find((int) $reserva['libro_id']);
        (new AutomaticNotificationService())->notifyUser(
            (int) $reserva['socio_id'],
            'reserva_confirmada',
            'Tu reserva de «' . ($libro['titulo'] ?? 'el libro solicitado') . '» fue confirmada.'
        );

        return true;
    }

    public function cancelar(int $id, int $adminId): bool
    {
        $reserva = $this->find($id);

        if (! $reserva) {
            throw new \RuntimeException('Reserva no encontrada.');
        }

        if (! in_array($reserva['estado'], ['pendiente', 'confirmada'], true)) {
            throw new \RuntimeException('La reserva ya no puede cancelarse.');
        }

        if (! $this->update($id, [
            'estado'           => 'cancelada',
            'fecha_cancelacion' => date('Y-m-d H:i:s'),
            'procesada_por'    => $adminId,
        ])) {
            throw new \RuntimeException('No se pudo cancelar la reserva.');
        }

        $libro = (new LibroModel())->find((int) $reserva['libro_id']);
        (new AutomaticNotificationService())->notifyUser(
            (int) $reserva['socio_id'],
            'reserva_cancelada',
            'Tu reserva de «' . ($libro['titulo'] ?? 'el libro solicitado') . '» fue cancelada.'
        );

        return true;
    }

    public function completar(int $id, int $adminId): bool
    {
        $reserva = $this->find($id);

        if (! $reserva) {
            throw new \RuntimeException('Reserva no encontrada.');
        }

        if ($reserva['estado'] !== 'confirmada') {
            throw new \RuntimeException('Solo se pueden completar reservas confirmadas.');
        }

        $this->db->transStart();

        try {
            (new RegistroModel())->registrarPrestamo(
                (int) $reserva['libro_id'],
                (int) $reserva['socio_id'],
                $adminId,
                false
            );

            if (! $this->update($id, [
                'estado'           => 'completada',
                'fecha_completada' => date('Y-m-d H:i:s'),
                'procesada_por'    => $adminId,
            ])) {
                throw new \RuntimeException('No se pudo completar la reserva.');
            }
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new \RuntimeException('No se pudo registrar el retiro del libro.');
        }

        $libro = (new LibroModel())->find((int) $reserva['libro_id']);
        (new AutomaticNotificationService())->notifyUser(
            (int) $reserva['socio_id'],
            'reserva_completada',
            'Se registró el retiro de «' . ($libro['titulo'] ?? 'el libro reservado') . '». '
                . 'La fecha de vencimiento es ' . date('d/m/Y', strtotime('+' . RegistroModel::DIAS_PRESTAMO . ' days')) . '.'
        );

        return true;
    }
}
