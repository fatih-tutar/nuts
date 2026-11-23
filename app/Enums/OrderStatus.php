<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Ordered = 'ordered';
    case AwaitingPayment = 'awaiting_payment';
    case Paid = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::Ordered => 'Sipariş alındı',
            self::AwaitingPayment => 'Teslim edildi, ödeme bekleniyor',
            self::Paid => 'Tamamlandı',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::Ordered => 'Alındı',
            self::AwaitingPayment => 'Ödeme bekleniyor',
            self::Paid => 'Tamamlandı',
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
            self::AwaitingPayment => 'border-amber-500 text-amber-900 bg-amber-50',
            self::Paid => 'border-emerald-500 text-emerald-800 bg-emerald-50',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Ordered => 'bg-sky-100 text-sky-800',
            self::AwaitingPayment => 'bg-amber-100 text-amber-900',
            self::Paid => 'bg-emerald-100 text-emerald-800',
        };
    }

    /** Map legacy status query/values to the current three-stage flow. */
    public static function normalize(?string $status): string
    {
        return match ($status) {
            'delivered', 'payment_pending' => self::AwaitingPayment->value,
            default => (string) ($status ?? ''),
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
