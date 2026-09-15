<?php

namespace App\Libraries;

use RuntimeException;

class TelegramService
{
    private const API_URL = 'https://api.telegram.org/bot';

    private string $botToken;
    private string $chatId;

    public function __construct()
    {
        $this->botToken = trim((string) env('TELEGRAM_BOT_TOKEN', ''));
        $this->chatId = trim((string) env('TELEGRAM_CHAT_ID', ''));
    }

    public function enviarMensaje(string $mensaje): array
    {
        $mensaje = trim($mensaje);

        if ($this->botToken === '') {
            throw new RuntimeException('Telegram bot token is not configured.');
        }

        if ($this->chatId === '') {
            throw new RuntimeException('Telegram chat ID is not configured.');
        }

        if ($mensaje === '') {
            throw new RuntimeException('Telegram message cannot be empty.');
        }

        try {
            $response = service('curlrequest')->post(
                self::API_URL . rawurlencode($this->botToken) . '/sendMessage',
                [
                    'form_params' => [
                        'chat_id' => $this->chatId,
                        'text'    => $mensaje,
                    ],
                    'timeout' => 10,
                ]
            );
        } catch (\Throwable $exception) {
            throw new RuntimeException('Telegram request failed.', 0, $exception);
        }

        $statusCode = $response->getStatusCode();

        if ($statusCode < 200 || $statusCode >= 300) {
            throw new RuntimeException('Telegram request returned an unsuccessful status.');
        }

        try {
            $payload = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new RuntimeException('Telegram returned invalid JSON.', 0, $exception);
        }

        if (($payload['ok'] ?? false) !== true) {
            throw new RuntimeException('Telegram rejected the message.');
        }

        return [
            'ok'         => true,
            'message_id' => $payload['result']['message_id'] ?? null,
        ];
    }
}
