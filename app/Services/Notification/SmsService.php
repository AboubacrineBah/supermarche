<?php

namespace App\Services\Notification;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService implements MessagingChannelInterface
{
    public function send(string $to, string $message): array
    {
        $provider = (string) config('services.sms.provider', 'log');
        $apiKey = (string) config('services.sms.api_key');

        if ($provider === 'log' || $apiKey === '') {
            Log::info('SMS (non configuré) : '.$message.' -> '.$to);

            return ['success' => true, 'message' => 'logged'];
        }

        // Fallback générique : à adapter selon l'agrégateur (Orange, AfricasTalking, Twilio).
        $response = Http::withHeaders(['Authorization' => 'Bearer '.$apiKey])->post(
            (string) config('services.sms.endpoint', ''),
            [
                'to' => $to,
                'message' => $message,
                'sender' => (string) config('services.sms.sender', 'SuguPro'),
            ]
        );

        if ($response->successful()) {
            return ['success' => true, 'message' => 'sent'];
        }

        Log::warning('SMS send failed', ['status' => $response->status(), 'body' => $response->body()]);

        return ['success' => false, 'message' => 'failed'];
    }
}
