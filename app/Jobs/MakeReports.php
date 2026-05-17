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

        $ordersQuery = Order::whereDate('created_at', $today);

        $ordersCount =  $ordersQuery->count();
        $totalSales =  $ordersQuery->sum('total');

        Report::updateOrCreate(
            ['date' => $today],
            ['orders_count' => $ordersCount, 'total_sales' => $totalSales]
        );
    }
}
