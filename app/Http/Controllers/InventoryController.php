<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inventory\UpdateInventoryRequest;
use App\Http\Resources\InventoryProductResource;
use App\Services\InventoryService;
use DomainException;
use Throwable;

class InventoryController extends Controller
{
    public function __construct(protected InventoryService $service) {}

    public function index()
    {
        try {
            return InventoryProductResource::collection($this->service->list());
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to list inventory.',
            ], 500);
        }
    }

    public function update(UpdateInventoryRequest $request, int $id)
    {
        try {
            $product = $this->service->updateStock($id, $request);

            return new InventoryProductResource($product);
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to update inventory.',
            ], 500);
        }
    }
}
