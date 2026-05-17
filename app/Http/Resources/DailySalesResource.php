<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailySalesResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'date' => data_get($this->resource, 'date'),
            'orders_count' => data_get($this->resource, 'orders_count'),
            'total_sales' => data_get($this->resource, 'total_sales'),
        ];
    }
}
