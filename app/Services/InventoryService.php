<?php

namespace App\Services;

use App\Http\Requests\Inventory\UpdateInventoryRequest;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class InventoryService
{
    public function list(): Collection
    {
        return Product::select('id', 'name', 'stock', 'price')->get();
    }

    public function updateStock(int $id, UpdateInventoryRequest $request): Product
    {
        $validated = $request->validated();
        $product = Product::findOrFail($id);
        $product->update([
            'stock' => $validated['stock'],
        ]);

        return $product;
    }
}
