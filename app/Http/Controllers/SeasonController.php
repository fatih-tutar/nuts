<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Season;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SeasonController extends Controller
{
    public function index(): View
    {
        $seasons = Season::query()
            ->with('product')
            ->withSum('orders as orders_quantity_kg', 'quantity_kg')
            ->orderByDesc('year')
            ->orderBy('product_id')
            ->get();

        return view('seasons.index', compact('seasons'));
    }

    public function create(): View
    {
        $products = Product::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('seasons.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        Season::query()->create($data);

        return redirect()->route('seasons.index')->with('status', 'Sezon eklendi.');
    }

    public function edit(Season $season): View
    {
        $products = Product::query()->orderBy('sort_order')->orderBy('name')->get();

        return view('seasons.edit', compact('season', 'products'));
    }

    public function update(Request $request, Season $season): RedirectResponse
    {
        $data = $this->validated($request, $season);
        $season->update($data);

        return redirect()->route('seasons.index')->with('status', 'Sezon güncellendi.');
    }

    public function destroy(Season $season): RedirectResponse
    {
        $season->delete();

        return redirect()->route('seasons.index')->with('status', 'Sezon silindi.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Season $season = null): array
    {
        return $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'year' => [
                'required',
                'integer',
                'min:2000',
                'max:2100',
                Rule::unique('seasons', 'year')
                    ->where(fn ($q) => $q->where('product_id', $request->integer('product_id')))
                    ->ignore($season?->id),
            ],
            'label' => ['nullable', 'string', 'max:255'],
            'order_start_date' => ['nullable', 'date'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'gold_price' => ['nullable', 'numeric', 'min:0'],
            'usd_rate' => ['nullable', 'numeric', 'min:0'],
        ]);
    }
}
