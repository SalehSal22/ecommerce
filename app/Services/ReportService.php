<?php

namespace App\Services;

use App\Jobs\MakeReports;
use App\Models\Report;

class ReportService
{
    public function generateDailySales(): void
    {
        MakeReports::dispatch();
    }

    public function dailySales(?string $date = null): Report
    {
        if ($date) {
            return Report::whereDate('date', $date)->firstOrFail();
        }

        return Report::orderByDesc('date')->firstOrFail();
    }
}
