<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\NotificadorService;

class NotificacionModel extends Model
{
    protected $table            = 'notificaciones';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'socio_id', 'canal', 'tipo', 'mensaje', 'referencia_id',
        'estado_entrega', 'fecha_envio',
    ];

    /** Crea y envía (o encola) una notificación por el mejor canal disponible del socio. */
    public function enviar(int $socioId, string $tipo, string $mensaje, ?int $referenciaId = null): int
    {
        $socio = (new SocioModel())->find($socioId);
        $canal = $this->elegirCanal($socio);

        $id = $this->insert([
            'socio_id'      => $socioId,
            'canal'         => $canal,
            'tipo'          => $tipo,
            'mensaje'       => $mensaje,
            'referencia_id' => $referenciaId,
            'estado_entrega'=> 'pendiente',
        ]);

        $this->despachar($id);

        return $id;
    }

    /** Prioriza Telegram > WhatsApp > Email según los datos de contacto cargados. */
    private function elegirCanal(array $socio): string
    {
        if (! empty($socio['telegram_chat_id'])) {
            return 'telegram';
        }
        if (! empty($socio['whatsapp_numero'])) {
            return 'whatsapp';
        }
        return 'email';
    }

    private function despachar(int $notificacionId): void
    {
        $notificacion = $this->find($notificacionId);
        $socio = (new SocioModel())->find($notificacion['socio_id']);

        $servicio = new NotificadorService();
        $enviado  = $servicio->enviar($notificacion['canal'], $socio, $notificacion['mensaje']);

        $this->update($notificacionId, [
            'estado_entrega' => $enviado ? 'enviado' : 'fallido',
            'fecha_envio'    => date('Y-m-d H:i:s'),
        ]);
    }

    public function reintentar(int $notificacionId): void
    {
        $this->update($notificacionId, ['estado_entrega' => 'pendiente']);
        $this->despachar($notificacionId);
    }

    /** Notificación específica del motor de reservas (confirma turno o posición en cola). */
    public function notificarReserva(int $reservaId): void
    {
        $reserva = (new ReservaModel())->find($reservaId);
        $libro   = (new LibroModel())->find($reserva['libro_id']);

        $mensaje = $reserva['estado'] === 'disponible_para_retiro'
            ? "Tu reserva de \"{$libro['titulo']}\" está lista. Tenés 48hs para retirarla."
            : "Reserva registrada para \"{$libro['titulo']}\". Posición en cola: #{$reserva['posicion_cola']}.";

        $this->enviar($reserva['socio_id'], 'reserva', $mensaje, $reservaId);
    }

    public function notificarDevolucionProxima(int $prestamoId): void
    {
        $prestamoModel = new PrestamoModel();
        $prestamo = $prestamoModel->find($prestamoId);
        $this->enviar(
            $prestamo['socio_id'],
            'devolucion',
            'Recordatorio: tu préstamo vence el ' . $prestamo['fecha_vencimiento'] . '.',
            $prestamoId
        );
    }

    public function pendientesOFallidas()
    {
        return $this->select('notificaciones.*, socios.nombre, socios.apellido')
            ->join('socios', 'socios.id = notificaciones.socio_id')
            ->whereIn('estado_entrega', ['pendiente', 'fallido'])
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
}
