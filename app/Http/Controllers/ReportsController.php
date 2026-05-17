<?php

namespace App\Http\Controllers;

use App\Http\Resources\DailySalesResource;
use App\Services\ReportService;

class ReportsController extends Controller
{
    public function __construct(protected ReportService $service) {}

    public function dailySales()
    {
        return new DailySalesResource($this->service->dailySales());
    }
}
