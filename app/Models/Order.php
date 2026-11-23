<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'season_id',
        'quantity_kg',
        'status',
        'ordered_on',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity_kg' => 'decimal:2',
            'status' => OrderStatus::class,
            'ordered_on' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function revenueTry(): float
    {
        $unitPrice = (float) ($this->season?->unit_price ?? 0);

        return round((float) $this->quantity_kg * $unitPrice, 2);
    }

    public function revenueUsd(): ?float
    {
        $rate = (float) ($this->season?->usd_rate ?? 0);
        if ($rate <= 0) {
            return null;
        }

        return round($this->revenueTry() / $rate, 2);
    }

    public function revenueGold(): ?float
    {
        $gold = (float) ($this->season?->gold_price ?? 0);
        if ($gold <= 0) {
            return null;
        }

        return round($this->revenueTry() / $gold, 2);
    }
}
