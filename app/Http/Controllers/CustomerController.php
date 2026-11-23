<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $customers = Customer::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'ilike', "%{$q}%")
                        ->orWhere('phone', 'ilike', "%{$q}%")
                        ->orWhere('city', 'ilike', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        $editing = $this->resolveEditing($request);
        $showCreateModal = $request->boolean('create') || old('_modal') === 'create-customer';
        $showEditModal = $editing !== null || old('_modal') === 'edit-customer';

        return view('customers.index', compact(
            'customers',
            'q',
            'editing',
            'showCreateModal',
            'showEditModal',
        ));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('customers.index', ['create' => 1]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string'],
        ]);

        $customer = Customer::query()->create($data);

        return redirect()->route('customers.show', $customer)->with('status', 'Müşteri eklendi.');
    }

    public function show(Request $request, Customer $customer): View
    {
        $customer->load(['orders.season.product']);

        $orders = $customer->orders
            ->sortByDesc(fn ($order) => sprintf('%d-%d', $order->season?->year ?? 0, $order->id))
            ->values();

        $totalsByProduct = $orders
            ->groupBy(fn ($order) => $order->season?->product?->slug ?? 'diger')
            ->map(function ($group) {
                $first = $group->first();
                $kg = round((float) $group->sum('quantity_kg'), 2);
                $revenue = round($group->sum(fn ($order) => $order->revenueTry()), 2);

                return [
                    'name' => $first->season?->product?->name ?? 'Diğer',
                    'slug' => $first->season?->product?->slug,
                    'kg' => $kg,
                    'revenue' => $revenue,
                    'order_count' => $group->count(),
                ];
            });

        $cevizTotal = $totalsByProduct->get('ceviz', [
            'name' => 'Ceviz',
            'slug' => 'ceviz',
            'kg' => 0,
            'revenue' => 0,
            'order_count' => 0,
        ]);

        $bademTotal = $totalsByProduct->get('badem', [
            'name' => 'Badem',
            'slug' => 'badem',
            'kg' => 0,
            'revenue' => 0,
            'order_count' => 0,
        ]);

        $showEditModal = $request->boolean('edit') || old('_modal') === 'edit-customer';

        return view('customers.show', [
            'customer' => $customer,
            'orders' => $orders,
            'cevizTotal' => $cevizTotal,
            'bademTotal' => $bademTotal,
            'grandKg' => round((float) $orders->sum('quantity_kg'), 2),
            'grandRevenue' => round($orders->sum(fn ($order) => $order->revenueTry()), 2),
            'showEditModal' => $showEditModal,
        ]);
    }

    public function edit(Customer $customer): RedirectResponse
    {
        return redirect()->route('customers.show', ['customer' => $customer, 'edit' => 1]);
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string'],
        ]);

        $customer->update($data);

        return redirect()->route('customers.show', $customer)->with('status', 'Müşteri güncellendi.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('status', 'Müşteri silindi.');
    }

    private function resolveEditing(Request $request): ?Customer
    {
        $id = $request->query('edit') ?: (old('_modal') === 'edit-customer' ? old('_edit_id') : null);

        return $id ? Customer::query()->find($id) : null;
    }
}
