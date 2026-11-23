<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Ordered = 'ordered';
    case Delivered = 'delivered';
    case PaymentPending = 'payment_pending';
    case Paid = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::Ordered => 'Sipariş verdi',
            self::Delivered => 'Sipariş teslim edildi',
            self::PaymentPending => 'Ödemesi bekleniyor',
            self::Paid => 'Ödeme yapıldı',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
