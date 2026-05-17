<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'quantity' => $this->resource->quantity,
            'price' => $this->resource->price,
            'subtotal' => $this->resource->subtotal,
            'product' => new ProductResource($this->resource->product),
        ];
    }
}
