<?php

namespace App\Enums;

enum PurchaseOrderStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case PartiallyReceived = 'partial';
    case Received = 'received';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Sent => 'Envoyé',
            self::PartiallyReceived => 'Partiellement reçu',
            self::Received => 'Reçu',
            self::Cancelled => 'Annulé',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'text-bg-secondary',
            self::Sent => 'text-bg-info',
            self::PartiallyReceived => 'text-bg-warning',
            self::Received => 'text-bg-success',
            self::Cancelled => 'text-bg-danger',
        };
    }

    public function isEditable(): bool
    {
        return $this === self::Draft;
    }

    public function isReceivable(): bool
    {
        return in_array($this, [self::Draft, self::Sent, self::PartiallyReceived], true);
    }
}
