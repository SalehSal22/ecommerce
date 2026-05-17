<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inventory\UpdateInventoryRequest;
use App\Http\Resources\InventoryProductResource;
use App\Services\InventoryService;

class InventoryController extends Controller
{
    public function __construct(protected InventoryService $service) {}

    public function index()
    {
        return InventoryProductResource::collection($this->service->list());
    }

    public function update(UpdateInventoryRequest $request, int $id)
    {
        $product = $this->service->updateStock($id, $request);

        return new InventoryProductResource($product);
    }
}
