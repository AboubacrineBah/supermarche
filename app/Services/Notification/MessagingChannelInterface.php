<?php

namespace App\Services\Notification;

interface MessagingChannelInterface
{
    /**
     * Envoie un message texte à un numéro de téléphone.
     *
     * @return array{success: bool, message: string}
     */
    public function send(string $to, string $message): array;
}
