<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Report;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class MakeReports implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $today = now()->toDateString();
        $totalOrders = 0;
        $finalTotalSales = 0;
        Order::whereDate('created_at', $today)->chunkById(100, function ($orders) use (&$totalOrders, &$finalTotalSales) {
            $ordersCount =  $orders->count();
            $totalSales =  $orders->sum('total');
            $totalOrders += $ordersCount;
            $finalTotalSales += $totalSales;
        });


        Report::updateOrCreate(
            ['date' => $today],
            [
                'orders_count' => $totalOrders,
                'total_sales' => $finalTotalSales
            ]
        );
    }
}
