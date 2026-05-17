<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;

class ProductsController extends Controller
{
    public function __construct(protected ProductService $service) {}

    public function index()
    {
        return ProductResource::collection($this->service->list());
    }

    public function show(int $id)
    {
        return new ProductResource($this->service->get($id));
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->service->create($request);

        return (new ProductResource($product))->response()->setStatusCode(201);
    }

    public function update(UpdateProductRequest $request, int $id)
    {
        return new ProductResource($this->service->update($id, $request));
    }

    public function destroy(int $id)
    {
        $this->service->delete($id);

        return response()->json([
            'message' => 'Product deleted.',
        ]);
    }
}
