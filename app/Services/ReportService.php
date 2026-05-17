<?php

namespace App\Services;

use App\Models\Order;

class ReportService
{
    public function dailySales(): array
    {
        $today = now()->toDateString();

        $orders = Order::whereDate('created_at', $today)->get();

        return [
            'date' => $today,
            'orders_count' => $orders->count(),
            'total_sales' => $orders->sum('total'),
        ];
    }
}
