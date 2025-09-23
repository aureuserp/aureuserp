<?php

namespace Webkul\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Webkul\Inventory\Models\Inventory;

class InventoryController extends Controller
{
    public function index()
    {
        $inventory = Inventory::where('is_active', true)
            ->orderBy('sort_order')
            ->paginate(10);

        return view('inventory::index', compact('inventory'));
    }

    public function show($id)
    {
        $item = Inventory::where('is_active', true)->findOrFail($id);

        return view('inventory::show', compact('item'));
    }
}