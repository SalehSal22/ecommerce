<?php

namespace App\Http\Controllers;

use App\Http\Resources\DailySalesResource;
use App\Services\ReportService;
use DomainException;
use Throwable;

class ReportsController extends Controller
{
    public function __construct(protected ReportService $service) {}

    public function dailySales()
    {
        try {
            return new DailySalesResource($this->service->dailySales());
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to fetch report.',
            ], 500);
        }
    }
}
