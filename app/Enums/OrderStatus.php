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

    public function shortLabel(): string
    {
        return match ($this) {
            self::Ordered => 'Sipariş verdi',
            self::Delivered => 'Teslim edildi',
            self::PaymentPending => 'Ödeme bekleniyor',
            self::Paid => 'Ödeme yapıldı',
        };
    }

    /** Idle / active Tailwind classes for status tabs. */
    public function tabClasses(bool $active): string
    {
        return $active ? $this->tabActiveClasses() : $this->tabIdleClasses();
    }

    public function tabIdleClasses(): string
    {
        return 'border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300';
    }

    public function tabActiveClasses(): string
    {
        return match ($this) {
            self::Ordered => 'border-sky-500 text-sky-800 bg-sky-50',
            self::Delivered => 'border-violet-500 text-violet-800 bg-violet-50',
            self::PaymentPending => 'border-amber-500 text-amber-900 bg-amber-50',
            self::Paid => 'border-emerald-500 text-emerald-800 bg-emerald-50',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Ordered => 'bg-sky-100 text-sky-800',
            self::Delivered => 'bg-violet-100 text-violet-800',
            self::PaymentPending => 'bg-amber-100 text-amber-900',
            self::Paid => 'bg-emerald-100 text-emerald-800',
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
