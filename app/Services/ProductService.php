<?php

namespace App\Services;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function list(): Collection
    {
        return Product::all();
    }

    public function get(int $id): Product
    {
        return Product::findOrFail($id);
    }

    public function create(StoreProductRequest $request): Product
    {
        $validated = $request->validated();

        return Product::create($validated);
    }

    public function update(int $id, UpdateProductRequest $request): Product
    {
        $validated = $request->validated();
        $product = Product::findOrFail($id);
        $product->update($validated);

        return $product;
    }

    public function delete(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->delete();
    }
}
