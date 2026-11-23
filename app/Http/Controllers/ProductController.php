<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()->orderBy('sort_order')->orderBy('name')->get();

        $editing = $this->resolveEditing($request);
        $showCreateModal = $request->boolean('create') || old('_modal') === 'create-product';
        $showEditModal = $editing !== null || old('_modal') === 'edit-product';

        return view('products.index', compact('products', 'editing', 'showCreateModal', 'showEditModal'));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('products.index', ['create' => 1]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:16'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        Product::query()->create($data);

        return redirect()->route('products.index')->with('status', 'Ürün eklendi.');
    }

    public function edit(Product $product): RedirectResponse
    {
        return redirect()->route('products.index', ['edit' => $product->id]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:16'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $product->update($data);

        return redirect()->route('products.index')->with('status', 'Ürün güncellendi.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')->with('status', 'Ürün silindi.');
    }

    private function resolveEditing(Request $request): ?Product
    {
        $id = $request->query('edit') ?: (old('_modal') === 'edit-product' ? old('_edit_id') : null);

        return $id ? Product::query()->find($id) : null;
    }
}
