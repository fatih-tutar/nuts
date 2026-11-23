<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
    protected $fillable = [
        'product_id',
        'year',
        'label',
        'order_start_date',
        'unit_price',
        'gold_price',
        'usd_rate',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'order_start_date' => 'date',
            'unit_price' => 'decimal:2',
            'gold_price' => 'decimal:2',
            'usd_rate' => 'decimal:4',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function displayName(): string
    {
        $product = $this->product?->name ?? 'Ürün';
        $label = $this->label ? " ({$this->label})" : '';

        return "{$this->year} {$product}{$label}";
    }

    public function totalQuantityKg(): float
    {
        return (float) $this->orders()->sum('quantity_kg');
    }

    public function totalRevenueTry(): float
    {
        return round($this->totalQuantityKg() * (float) $this->unit_price, 2);
    }

    public function totalRevenueUsd(): ?float
    {
        if (! $this->usd_rate || (float) $this->usd_rate <= 0) {
            return null;
        }

        return round($this->totalRevenueTry() / (float) $this->usd_rate, 2);
    }

    public function totalRevenueGold(): ?float
    {
        if (! $this->gold_price || (float) $this->gold_price <= 0) {
            return null;
        }

        return round($this->totalRevenueTry() / (float) $this->gold_price, 2);
    }
}
