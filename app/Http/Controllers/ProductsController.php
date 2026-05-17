<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use DomainException;
use Throwable;

class ProductsController extends Controller
{
    public function __construct(protected ProductService $service) {}

    public function index()
    {
        try {
            return ProductResource::collection($this->service->list());
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to list products.',
            ], 500);
        }
    }

    public function show(int $id)
    {
        try {
            return new ProductResource($this->service->get($id));
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to fetch product.',
            ], 500);
        }
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $product = $this->service->create($request);

            return (new ProductResource($product))->response()->setStatusCode(201);
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to create product.',
            ], 500);
        }
    }

    public function update(UpdateProductRequest $request, int $id)
    {
        try {
            return new ProductResource($this->service->update($id, $request));
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to update product.',
            ], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->service->delete($id);

            return response()->json([
                'message' => 'Product deleted.',
            ]);
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to delete product.',
            ], 500);
        }
    }
}
