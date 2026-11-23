<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $rows = Order::query()
            ->join('seasons', 'orders.season_id', '=', 'seasons.id')
            ->join('products', 'seasons.product_id', '=', 'products.id')
            ->whereIn('products.slug', ['ceviz', 'badem'])
            ->select([
                'seasons.year',
                'products.slug',
                DB::raw('COALESCE(SUM(orders.quantity_kg), 0) as total_kg'),
            ])
            ->groupBy('seasons.year', 'products.slug')
            ->orderBy('seasons.year')
            ->get();

        $years = $rows->pluck('year')->unique()->sort()->values()->map(fn ($y) => (int) $y)->all();

        $bySlug = $rows->groupBy('slug');

        $seriesFor = function (string $slug) use ($years, $bySlug): array {
            $byYear = ($bySlug->get($slug) ?? collect())->keyBy(fn ($row) => (int) $row->year);

            return collect($years)->map(function (int $year) use ($byYear) {
                $row = $byYear->get($year);

                return $row ? round((float) $row->total_kg, 2) : 0;
            })->all();
        };

        return view('dashboard', [
            'chart' => [
                'years' => $years,
                'ceviz' => $seriesFor('ceviz'),
                'badem' => $seriesFor('badem'),
            ],
        ]);
    }
}
