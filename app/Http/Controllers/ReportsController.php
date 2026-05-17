<?php

namespace App\Http\Controllers;

use App\Http\Resources\DailySalesResource;
use App\Services\ReportService;
    use DomainException;
use Illuminate\Http\Request;
use Throwable;

class ReportsController extends Controller
{
    public function __construct(protected ReportService $service) {}

    public function dailySales(Request $request)
    {
        try {
            $date = $request->query('date');

            return new DailySalesResource($this->service->dailySales($date));
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } 
    }

    public function generateDailySales()
    {
        try {
            $this->service->generateDailySales();

            return response()->json([
                'status' => 'success',
                'message' => 'Report generation queued.',
            ], 202);
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to queue report.',
            ], 500);
        }
    }
}
