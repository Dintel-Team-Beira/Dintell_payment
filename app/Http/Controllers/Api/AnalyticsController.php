<?php
// app/Http/Controllers/Api/AnalyticsController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\ApiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function dashboard(Request $request)
    {
        $period = $request->get('period', 30); // dias
        $startDate = Carbon::now()->subDays($period);

        return response()->json([
            'revenue' => $this->getRevenueData($startDate),
            'subscriptions' => $this->getSubscriptionData($startDate),
            'api_usage' => $this->getApiUsageData($startDate),
            'top_domains' => $this->getTopDomains($startDate),
            'status_distribution' => $this->getStatusDistribution()
        ]);
    }

    private function getRevenueData($startDate)
    {
        return Subscription::select(
                DB::raw('DATE(last_payment_date) as date'),
                DB::raw('SUM(amount_paid) as revenue'),
                DB::raw('COUNT(*) as payments')
            )
            ->where('last_payment_date', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getSubscriptionData($startDate)
    {
        return [
            'new_subscriptions' => Subscription::where('created_at', '>=', $startDate)->count(),
            'cancelled_subscriptions' => Subscription::where('status', 'cancelled')
                                                  ->where('updated_at', '>=', $startDate)
                                                  ->count(),
            'suspended_subscriptions' => Subscription::where('status', 'suspended')
                                                   ->where('suspended_at', '>=', $startDate)
                                                   ->count()
        ];
    }

    private function getApiUsageData($startDate)
    {
        return ApiLog::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as requests'),
                DB::raw('COUNT(DISTINCT domain) as unique_domains'),
                DB::raw('AVG(response_code) as avg_response_code')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getTopDomains($startDate)
    {
        return ApiLog::select('domain', DB::raw('COUNT(*) as requests'))
                    ->where('created_at', '>=', $startDate)
                    ->groupBy('domain')
                    ->orderByDesc('requests')
                    ->limit(10)
                    ->get();
    }

    private function getStatusDistribution()
    {
        return Subscription::select('status', DB::raw('COUNT(*) as count'))
                          ->groupBy('status')
                          ->get();
    }
}