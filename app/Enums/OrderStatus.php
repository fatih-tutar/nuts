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
            self::PaymentPending => 'Ödeme bekliyor',
            self::Paid => 'Ödendi',
        };
    }

    /** Idle / active Tailwind classes for filter tabs. */
    public function tabClasses(bool $active): string
    {
        return $active ? $this->tabActiveClasses() : $this->tabIdleClasses();
    }

    public function tabIdleClasses(): string
    {
        return match ($this) {
            self::Ordered => 'bg-sky-50 text-sky-800 ring-sky-200 hover:bg-sky-100',
            self::Delivered => 'bg-violet-50 text-violet-800 ring-violet-200 hover:bg-violet-100',
            self::PaymentPending => 'bg-amber-50 text-amber-900 ring-amber-200 hover:bg-amber-100',
            self::Paid => 'bg-emerald-50 text-emerald-800 ring-emerald-200 hover:bg-emerald-100',
        };
    }

    public function tabActiveClasses(): string
    {
        return match ($this) {
            self::Ordered => 'bg-sky-600 text-white ring-sky-600 shadow-sm',
            self::Delivered => 'bg-violet-600 text-white ring-violet-600 shadow-sm',
            self::PaymentPending => 'bg-amber-500 text-white ring-amber-500 shadow-sm',
            self::Paid => 'bg-emerald-600 text-white ring-emerald-600 shadow-sm',
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
