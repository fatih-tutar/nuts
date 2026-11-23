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

    public function tabLabel(): string
    {
        return match ($this) {
            self::Ordered => 'Verdi',
            self::Delivered => 'Teslim',
            self::PaymentPending => 'Bekliyor',
            self::Paid => 'Ödendi',
        };
    }

    /** Idle / active Tailwind classes for underline status tabs. */
    public function tabClasses(bool $active): string
    {
        return $active ? $this->tabActiveClasses() : $this->tabIdleClasses();
    }

    public function tabIdleClasses(): string
    {
        return match ($this) {
            self::Ordered => 'border-transparent text-sky-700/70 hover:text-sky-800 hover:border-sky-200',
            self::Delivered => 'border-transparent text-violet-700/70 hover:text-violet-800 hover:border-violet-200',
            self::PaymentPending => 'border-transparent text-amber-800/70 hover:text-amber-900 hover:border-amber-200',
            self::Paid => 'border-transparent text-emerald-700/70 hover:text-emerald-800 hover:border-emerald-200',
        };
    }

    public function tabActiveClasses(): string
    {
        return match ($this) {
            self::Ordered => 'border-sky-600 text-sky-800',
            self::Delivered => 'border-violet-600 text-violet-800',
            self::PaymentPending => 'border-amber-500 text-amber-900',
            self::Paid => 'border-emerald-600 text-emerald-800',
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
