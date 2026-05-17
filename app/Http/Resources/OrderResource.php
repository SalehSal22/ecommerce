<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'status' => $this->resource->status,
            'total' => $this->resource->total,
            'created_at' => $this->resource->created_at,
            'items' => OrderItemResource::collection($this->resource->items),
        ];
    }
}
