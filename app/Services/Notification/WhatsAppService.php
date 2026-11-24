<?php

namespace App\Services\Notification;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService implements MessagingChannelInterface
{
    public function send(string $to, string $message): array
    {
        $phoneNumberId = (string) config('services.whatsapp.phone_number_id');
        $token = (string) config('services.whatsapp.token');

        if ($phoneNumberId === '' || $token === '') {
            Log::info('WhatsApp (non configuré) : '.$message.' -> '.$to);

            return ['success' => true, 'message' => 'logged'];
        }

        $response = Http::withToken($token)->post(
            "https://graph.facebook.com/v19.0/{$phoneNumberId}/messages",
            [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'text',
                'text' => ['body' => $message],
            ]
        );

        if ($response->successful()) {
            return ['success' => true, 'message' => 'sent'];
        }

        Log::warning('WhatsApp send failed', ['status' => $response->status(), 'body' => $response->body()]);

        return ['success' => false, 'message' => 'failed'];
    }
}
