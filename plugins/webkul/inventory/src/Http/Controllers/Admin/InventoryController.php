<?php

namespace Webkul\Inventory\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Webkul\Inventory\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $inventory = Inventory::orderBy('sort_order')->paginate(10);

        return view('inventory::admin.index', compact('inventory'));
    }

    public function create()
    {
        return view('inventory::admin.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:inventory,slug',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        Inventory::create($validated);

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory created successfully.');
    }

    public function show(Inventory $inventory)
    {
        return view('inventory::admin.show', compact('inventory'));
    }

    public function edit(Inventory $inventory)
    {
        return view('inventory::admin.edit', compact('inventory'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:inventory,slug,' . $inventory->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $inventory->update($validated);

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory updated successfully.');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory deleted successfully.');
    }
}