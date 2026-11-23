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
        return match ($this) {
            self::Ordered => 'border-transparent text-sky-800/60 hover:bg-sky-50 hover:text-sky-900',
            self::Delivered => 'border-transparent text-violet-800/60 hover:bg-violet-50 hover:text-violet-900',
            self::PaymentPending => 'border-transparent text-amber-900/60 hover:bg-amber-50 hover:text-amber-950',
            self::Paid => 'border-transparent text-emerald-800/60 hover:bg-emerald-50 hover:text-emerald-900',
        };
    }

    public function tabActiveClasses(): string
    {
        return match ($this) {
            self::Ordered => 'border-sky-600 bg-sky-600 text-white shadow-sm',
            self::Delivered => 'border-violet-600 bg-violet-600 text-white shadow-sm',
            self::PaymentPending => 'border-amber-500 bg-amber-500 text-white shadow-sm',
            self::Paid => 'border-emerald-600 bg-emerald-600 text-white shadow-sm',
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
