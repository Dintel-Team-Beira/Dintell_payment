<?php

namespace App\Http\Controllers;

use App\Models\ApiLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ApiLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ApiLog::with('subscription')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('domain', 'like', "%{$search}%")
                  ->orWhere('endpoint', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('domain')) {
            $query->byDomain($request->domain);
        }

        if ($request->filled('endpoint')) {
            $query->byEndpoint($request->endpoint);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $logs = $query->paginate(5)->withQueryString();

        // Get filter options
        $domains = ApiLog::select('domain')
            ->groupBy('domain')
            ->orderBy('domain')
            ->pluck('domain');

        $endpoints = ApiLog::select('endpoint')
            ->groupBy('endpoint')
            ->orderBy('endpoint')
            ->limit(20)
            ->pluck('endpoint');

        return view('api-logs.index', compact('logs', 'domains', 'endpoints'));
    }

    /**
     * Display the specified resource.
     */
    public function show(ApiLog $apiLog)
    {
        $apiLog->load('subscription');
        return view('api-logs.show', compact('apiLog'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApiLog $apiLog)
    {
        $apiLog->delete();

        return redirect()->route('api-logs.index')
            ->with('success', 'Log excluído com sucesso!');
    }

    /**
     * Bulk delete logs
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'log_ids' => 'required|array',
            'log_ids.*' => 'exists:api_logs,id'
        ]);

        ApiLog::whereIn('id', $request->log_ids)->delete();

        return redirect()->route('api-logs.index')
            ->with('success', count($request->log_ids) . ' logs excluídos com sucesso!');
    }

    /**
     * Clean up old logs
     */
    public function cleanup(Request $request)
    {
        $daysToKeep = $request->input('days', 30);

        $deleted = ApiLog::where('created_at', '<', Carbon::now()->subDays($daysToKeep))
            ->delete();

        return redirect()->route('api-logs.index')
            ->with('success', "{$deleted} logs antigos foram removidos!");
    }

    /**
     * Export logs to CSV
     */
    public function export(Request $request)
    {
        $query = ApiLog::with('subscription')
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('domain', 'like', "%{$search}%")
                  ->orWhere('endpoint', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('domain')) {
            $query->byDomain($request->domain);
        }

        $logs = $query->limit(1000)->get(); // Limit for performance

        $filename = 'api-logs-' . date('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID',
                'Domain',
                'IP Address',
                'Endpoint',
                'Response Code',
                'Status',
                'Subscription ID',
                'User Agent',
                'Created At'
            ]);

            // CSV data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->domain,
                    $log->ip_address,
                    $log->clean_endpoint,
                    $log->response_code,
                    $log->status_text,
                    $log->subscription_id,
                    $log->user_agent,
                    $log->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get API statistics
     */
    public function statistics(Request $request)
    {
        $period = $request->input('period', '7d'); // 1d, 7d, 30d

        $startDate = match($period) {
            '1d' => Carbon::now()->subDay(),
            '7d' => Carbon::now()->subWeek(),
            '30d' => Carbon::now()->subMonth(),
            default => Carbon::now()->subWeek()
        };

        $stats = [
            'total_requests' => ApiLog::where('created_at', '>=', $startDate)->count(),
            'success_requests' => ApiLog::byStatus('success')->where('created_at', '>=', $startDate)->count(),
            'error_requests' => ApiLog::byStatus('error')->where('created_at', '>=', $startDate)->count(),
            'unique_domains' => ApiLog::where('created_at', '>=', $startDate)->distinct('domain')->count(),
            'top_domains' => ApiLog::select('domain', DB::raw('count(*) as count'))
                ->where('created_at', '>=', $startDate)
                ->groupBy('domain')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'top_endpoints' => ApiLog::select('endpoint', DB::raw('count(*) as count'))
                ->where('created_at', '>=', $startDate)
                ->groupBy('endpoint')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'response_codes' => ApiLog::select('response_code', DB::raw('count(*) as count'))
                ->where('created_at', '>=', $startDate)
                ->groupBy('response_code')
                ->orderBy('count', 'desc')
                ->get(),
            'hourly_requests' => ApiLog::select(
                    DB::raw('HOUR(created_at) as hour'),
                    DB::raw('count(*) as count')
                )
                ->where('created_at', '>=', $startDate)
                ->groupBy('hour')
                ->orderBy('hour')
                ->get()
        ];

        return response()->json($stats);
    }

    /**
     * Log a new API request (for use in middleware or API routes)
     */
    public static function logRequest(Request $request, $response, $subscriptionId = null)
    {
        try {
            ApiLog::create([
                'subscription_id' => $subscriptionId,
                'domain' => $request->getHost(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'endpoint' => $request->method() . ' ' . $request->getPathInfo(),
                'request_data' => $request->except(['password', 'password_confirmation', '_token']),
                'response_data' => is_string($response) ? ['message' => $response] : $response,
                'response_code' => is_object($response) && method_exists($response, 'getStatusCode')
                    ? $response->getStatusCode()
                    : 200,
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            // Silently fail to avoid breaking the main request
            \Log::error('Failed to log API request: ' . $e->getMessage());
        }
    }
}