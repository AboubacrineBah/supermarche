<?php

namespace App\Enums;

enum StockMovementType: string
{
    case Opening = 'opening';
    case Purchase = 'purchase';
    case Sale = 'sale';
    case Adjustment = 'adjustment';
    case CustomerReturn = 'customer_return';
    case SupplierReturn = 'supplier_return';
    case Loss = 'loss';
    case TransferIn = 'transfer_in';
    case TransferOut = 'transfer_out';

    public function label(): string
    {
        return match ($this) {
            self::Opening => 'Stock initial',
            self::Purchase => 'Achat',
            self::Sale => 'Vente',
            self::Adjustment => 'Ajustement',
            self::CustomerReturn => 'Retour client',
            self::SupplierReturn => 'Retour fournisseur',
            self::Loss => 'Perte / casse',
            self::TransferIn => 'Transfert entrant',
            self::TransferOut => 'Transfert sortant',
        };
    }

    public function isInbound(): bool
    {
        return match ($this) {
            self::Opening, self::Purchase, self::CustomerReturn, self::TransferIn => true,
            self::Sale, self::SupplierReturn, self::Loss, self::TransferOut, self::Adjustment => false,
        };
    }
}
