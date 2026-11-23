<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Season;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $seasonId = $request->query('season_id');

        $orders = Order::query()
            ->with(['customer', 'season.product'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($seasonId, fn ($q) => $q->where('season_id', $seasonId))
            ->latest('id')
            ->paginate(40)
            ->withQueryString();

        $seasons = Season::query()->with('product')->orderByDesc('year')->get();

        return view('orders.index', [
            'orders' => $orders,
            'seasons' => $seasons,
            'statuses' => OrderStatus::options(),
            'filters' => [
                'status' => $status,
                'season_id' => $seasonId,
            ],
        ]);
    }

    public function create(Request $request): View
    {
        return view('orders.create', [
            'customers' => Customer::query()->orderBy('name')->get(),
            'seasons' => Season::query()->with('product')->orderByDesc('year')->get(),
            'statuses' => OrderStatus::options(),
            'selectedCustomerId' => $request->query('customer_id'),
            'selectedSeasonId' => $request->query('season_id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        Order::query()->create($data);

        return redirect()->route('orders.index')->with('status', 'Sipariş oluşturuldu.');
    }

    public function edit(Order $order): View
    {
        return view('orders.edit', [
            'order' => $order,
            'customers' => Customer::query()->orderBy('name')->get(),
            'seasons' => Season::query()->with('product')->orderByDesc('year')->get(),
            'statuses' => OrderStatus::options(),
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $this->validated($request);
        $order->update($data);

        return redirect()->route('orders.index')->with('status', 'Sipariş güncellendi.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()->route('orders.index')->with('status', 'Sipariş silindi.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        return $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'season_id' => ['required', 'exists:seasons,id'],
            'quantity_kg' => ['required', 'numeric', 'min:0.01'],
            'status' => ['required', Rule::enum(OrderStatus::class)],
            'ordered_on' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
