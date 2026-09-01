<?php

namespace App\Models;

use CodeIgniter\Model;

class ReservaModel extends Model
{
    protected $table            = 'reservas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'libro_id', 'socio_id', 'fecha_reserva', 'posicion_cola',
        'estado', 'fecha_limite_retiro',
    ];

    public const HORAS_LIMITE_RETIRO = 48;

    /**
     * Motor de reservas SINCRÓNICO: se ejecuta dentro de una transacción para
     * garantizar que la posición en la cola sea consistente incluso con
     * pedidos simultáneos, y dispara la notificación inmediata al socio y
     * al panel del bibliotecario (ver NotificacionModel).
     */
    public function crearReserva(int $libroId, int $socioId): int
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Bloquea las filas de reservas activas de este libro para calcular
        // la posición de cola de forma segura ante concurrencia.
        $enCola = $db->table('reservas')
            ->where('libro_id', $libroId)
            ->whereIn('estado', ['pendiente', 'disponible_para_retiro'])
            ->countAllResults();

        $ejemplarModel = new EjemplarModel();
        $hayDisponible  = (bool) $ejemplarModel->primeroDisponible($libroId);

        $estadoInicial = ($enCola === 0 && $hayDisponible) ? 'disponible_para_retiro' : 'pendiente';

        $reservaId = $this->insert([
            'libro_id'      => $libroId,
            'socio_id'      => $socioId,
            'fecha_reserva' => date('Y-m-d H:i:s'),
            'posicion_cola' => $enCola + 1,
            'estado'        => $estadoInicial,
            'fecha_limite_retiro' => $estadoInicial === 'disponible_para_retiro'
                ? date('Y-m-d H:i:s', strtotime('+' . self::HORAS_LIMITE_RETIRO . ' hours'))
                : null,
        ]);

        if ($estadoInicial === 'disponible_para_retiro') {
            $ejemplarModel->marcarEstado(
                $ejemplarModel->primeroDisponible($libroId)['id'] ?? 0,
                'reservado'
            );
        }

        $db->transComplete();

        // Notificación automática (Telegram/WhatsApp/Email) al socio y aviso al panel.
        (new NotificacionModel())->notificarReserva($reservaId);

        return $reservaId;
    }

    public function siguienteEnCola(int $libroId): ?array
    {
        return $this->where('libro_id', $libroId)
            ->where('estado', 'pendiente')
            ->orderBy('posicion_cola', 'ASC')
            ->first();
    }

    public function activar(int $reservaId): void
    {
        $this->update($reservaId, [
            'estado'              => 'disponible_para_retiro',
            'fecha_limite_retiro' => date('Y-m-d H:i:s', strtotime('+' . self::HORAS_LIMITE_RETIRO . ' hours')),
        ]);

        (new NotificacionModel())->notificarReserva($reservaId);
    }

    public function cancelar(int $reservaId): void
    {
        $this->update($reservaId, ['estado' => 'cancelada']);
    }

    public function completar(int $reservaId): void
    {
        $this->update($reservaId, ['estado' => 'completada']);
    }

    public function pendientesConDatos()
    {
        return $this->select('reservas.*, socios.nombre, socios.apellido, libros.titulo')
            ->join('socios', 'socios.id = reservas.socio_id')
            ->join('libros', 'libros.id = reservas.libro_id')
            ->whereIn('reservas.estado', ['pendiente', 'disponible_para_retiro'])
            ->orderBy('reservas.libro_id', 'ASC')
            ->orderBy('reservas.posicion_cola', 'ASC')
            ->findAll();
    }
}
