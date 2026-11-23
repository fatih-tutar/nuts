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
        $seasons = Season::query()
            ->with('product')
            ->orderByDesc('year')
            ->orderBy('product_id')
            ->get();

        $status = $request->query('status', '');
        // First visit: default to latest season. Explicit empty season_id = all seasons.
        $seasonId = $request->has('season_id')
            ? $request->query('season_id')
            : (string) ($seasons->first()?->id ?? '');

        $orders = Order::query()
            ->with(['customer', 'season.product'])
            ->when($status !== '' && $status !== null, fn ($q) => $q->where('status', $status))
            ->when($seasonId !== '' && $seasonId !== null, fn ($q) => $q->where('season_id', $seasonId))
            ->latest('id')
            ->paginate(40)
            ->withQueryString();

        $editing = $this->resolveEditing($request);
        $showCreateModal = $request->boolean('create') || old('_modal') === 'create-order';
        $showEditModal = $editing !== null || old('_modal') === 'edit-order';

        return view('orders.index', [
            'orders' => $orders,
            'seasons' => $seasons,
            'customers' => Customer::query()->orderBy('name')->get(),
            'statuses' => OrderStatus::options(),
            'filters' => [
                'status' => $status,
                'season_id' => $seasonId,
            ],
            'editing' => $editing,
            'showCreateModal' => $showCreateModal,
            'showEditModal' => $showEditModal,
            'selectedCustomerId' => $request->query('customer_id'),
            'selectedSeasonId' => $request->query('season_id', $seasonId),
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        return redirect()->route('orders.index', array_filter([
            'create' => 1,
            'customer_id' => $request->query('customer_id'),
            'season_id' => $request->query('season_id'),
        ], fn ($v) => $v !== null && $v !== ''));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $order = Order::query()->create($data);

        return redirect()
            ->route('orders.index', $this->indexQuery($request, $order->season_id))
            ->with('status', 'Sipariş oluşturuldu.');
    }

    public function edit(Request $request, Order $order): RedirectResponse
    {
        return redirect()->route('orders.index', array_merge(
            $this->indexQuery($request, $order->season_id),
            ['edit' => $order->id],
        ));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $this->validated($request);
        $order->update($data);

        return redirect()
            ->route('orders.index', $this->indexQuery($request, $order->season_id))
            ->with('status', 'Sipariş güncellendi.');
    }

    public function destroy(Request $request, Order $order): RedirectResponse
    {
        $seasonId = $order->season_id;
        $order->delete();

        return redirect()
            ->route('orders.index', $this->indexQuery($request, $seasonId))
            ->with('status', 'Sipariş silindi.');
    }

    /**
     * Preserve list filters (status tab + season) after create/update/delete.
     *
     * @return array<string, mixed>
     */
    private function indexQuery(Request $request, int|string|null $fallbackSeasonId = null): array
    {
        $status = $request->input('_return_status', $request->query('status'));
        $hasReturnSeason = $request->has('_return_season_id');
        $seasonId = $hasReturnSeason
            ? $request->input('_return_season_id')
            : ($request->has('season_id') ? $request->query('season_id') : $fallbackSeasonId);

        return array_filter([
            'status' => $status !== null && $status !== '' ? $status : null,
            'season_id' => $seasonId,
        ], fn ($value) => $value !== null);
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

    private function resolveEditing(Request $request): ?Order
    {
        $id = $request->query('edit') ?: (old('_modal') === 'edit-order' ? old('_edit_id') : null);

        return $id ? Order::query()->with(['customer', 'season'])->find($id) : null;
    }
}
