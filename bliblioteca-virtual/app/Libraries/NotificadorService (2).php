<?php

namespace App\Libraries;

/**
 * Punto único de integración con los canales externos de mensajería.
 * Mantiene la lógica de APIs (Telegram Bot API, WhatsApp Business API,
 * SMTP para email) fuera de los modelos, para poder cambiar de proveedor
 * sin tocar la lógica de negocio del NotificacionModel.
 *
 * Las credenciales se leen desde variables de entorno (.env), nunca
 * hardcodeadas, para evitar exponerlas en el repositorio.
 */
class NotificadorService
{
    public function enviar(string $canal, array $socio, string $mensaje): bool
    {
        try {
            return match ($canal) {
                'telegram' => $this->enviarTelegram($socio['telegram_chat_id'], $mensaje),
                'whatsapp' => $this->enviarWhatsapp($socio['whatsapp_numero'], $mensaje),
                'email'    => $this->enviarEmail($socio['email'], $mensaje),
                default    => false,
            };
        } catch (\Throwable $e) {
            log_message('error', 'NotificadorService: ' . $e->getMessage());
            return false;
        }
    }

    private function enviarTelegram(string $chatId, string $mensaje): bool
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        if (! $token || ! $chatId) {
            return false;
        }

        $client = \Config\Services::curlrequest();
        $response = $client->post("https://api.telegram.org/bot{$token}/sendMessage", [
            'json' => ['chat_id' => $chatId, 'text' => $mensaje],
        ]);

        return $response->getStatusCode() === 200;
    }

    private function enviarWhatsapp(string $numero, string $mensaje): bool
    {
        $token = env('WHATSAPP_API_TOKEN');
        $phoneId = env('WHATSAPP_PHONE_ID');
        if (! $token || ! $numero) {
            return false;
        }

        $client = \Config\Services::curlrequest();
        $response = $client->post("https://graph.facebook.com/v19.0/{$phoneId}/messages", [
            'headers' => ['Authorization' => "Bearer {$token}"],
            'json'    => [
                'messaging_product' => 'whatsapp',
                'to'   => $numero,
                'type' => 'text',
                'text' => ['body' => $mensaje],
            ],
        ]);

        return $response->getStatusCode() === 200;
    }

    private function enviarEmail(string $email, string $mensaje): bool
    {
        $emailService = \Config\Services::email();
        $emailService->setTo($email);
        $emailService->setFrom(env('mail.fromEmail', 'no-responder@biblioteca.local'), 'Mi Biblioteca Virtual');
        $emailService->setSubject('Notificación de la biblioteca');
        $emailService->setMessage($mensaje);

        return $emailService->send();
    }
}
