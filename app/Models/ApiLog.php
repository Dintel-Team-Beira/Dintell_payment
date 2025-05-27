<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ApiLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subscription_id',
        'domain',
        'ip_address',
        'user_agent',
        'endpoint',
        'request_data',
        'response_data',
        'response_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the subscription that owns the log.
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Scope a query to filter by status type.
     */
    public function scopeByStatus($query, $status)
    {
        return match($status) {
            'success' => $query->where('response_code', '>=', 200)->where('response_code', '<', 300),
            'client_error' => $query->where('response_code', '>=', 400)->where('response_code', '<', 500),
            'server_error' => $query->where('response_code', '>=', 500),
            'error' => $query->where('response_code', '>=', 400),
            default => $query
        };
    }

    /**
     * Scope a query to filter by domain.
     */
    public function scopeByDomain($query, $domain)
    {
        return $query->where('domain', $domain);
    }

    /**
     * Scope a query to filter by endpoint.
     */
    public function scopeByEndpoint($query, $endpoint)
    {
        return $query->where('endpoint', 'like', "%{$endpoint}%");
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $from, $to = null)
    {
        if ($from) {
            $query->where('created_at', '>=', $from);
        }

        if ($to) {
            $query->where('created_at', '<=', $to . ' 23:59:59');
        }

        return $query;
    }

    /**
     * Scope a query to get recent logs.
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', Carbon::now()->subHours($hours));
    }

    /**
     * Get clean endpoint without query parameters.
     */
    public function getCleanEndpointAttribute()
    {
        $endpoint = $this->endpoint;

        // Remove query parameters
        if (strpos($endpoint, '?') !== false) {
            $endpoint = substr($endpoint, 0, strpos($endpoint, '?'));
        }

        return $endpoint;
    }

    /**
     * Get response code color class for UI.
     */
    public function getResponseCodeColorAttribute()
    {
        return match(true) {
            $this->response_code >= 200 && $this->response_code < 300 => 'bg-green-100 text-green-800',
            $this->response_code >= 300 && $this->response_code < 400 => 'bg-blue-100 text-blue-800',
            $this->response_code >= 400 && $this->response_code < 500 => 'bg-amber-100 text-amber-800',
            $this->response_code >= 500 => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get human readable status text.
     */
    public function getStatusTextAttribute()
    {
        return match(true) {
            $this->response_code >= 200 && $this->response_code < 300 => 'Sucesso',
            $this->response_code >= 300 && $this->response_code < 400 => 'Redirecionamento',
            $this->response_code >= 400 && $this->response_code < 500 => 'Erro do Cliente',
            $this->response_code >= 500 => 'Erro do Servidor',
            default => 'Desconhecido'
        };
    }

    /**
     * Get the HTTP method from endpoint.
     */
    public function getHttpMethodAttribute()
    {
        return explode(' ', $this->endpoint)[0] ?? 'GET';
    }

    /**
     * Get the path from endpoint (without HTTP method).
     */
    public function getPathAttribute()
    {
        $parts = explode(' ', $this->endpoint, 2);
        return $parts[1] ?? $this->endpoint;
    }

    /**
     * Check if the request was successful.
     */
    public function getIsSuccessAttribute()
    {
        return $this->response_code >= 200 && $this->response_code < 300;
    }

    /**
     * Check if the request had a client error.
     */
    public function getIsClientErrorAttribute()
    {
        return $this->response_code >= 400 && $this->response_code < 500;
    }

    /**
     * Check if the request had a server error.
     */
    public function getIsServerErrorAttribute()
    {
        return $this->response_code >= 500;
    }

    /**
     * Check if the request had any error.
     */
    public function getIsErrorAttribute()
    {
        return $this->response_code >= 400;
    }

    /**
     * Get formatted response time if available.
     */
    public function getFormattedResponseTimeAttribute()
    {
        if (isset($this->response_data['execution_time'])) {
            $time = $this->response_data['execution_time'];
            return $time < 1 ? round($time * 1000, 2) . 'ms' : round($time, 2) . 's';
        }

        return null;
    }

    /**
     * Get the country from IP address (requires GeoIP package).
     */
    public function getCountryAttribute()
    {
        try {
            // This requires a GeoIP package like torann/geoip
            if (class_exists(\Torann\GeoIP\Facades\GeoIP::class)) {
                return \Torann\GeoIP\Facades\GeoIP::getLocation($this->ip_address)['country'] ?? null;
            }
        } catch (\Exception $e) {
            // Silently fail
        }

        return null;
    }

    /**
     * Get browser info from user agent.
     */
    public function getBrowserInfoAttribute()
    {
        if (!$this->user_agent) {
            return null;
        }

        try {
            // This requires jenssegers/agent package
            if (class_exists(\Jenssegers\Agent\Agent::class)) {
                $agent = new \Jenssegers\Agent\Agent();
                $agent->setUserAgent($this->user_agent);

                return [
                    'browser' => $agent->browser(),
                    'version' => $agent->version($agent->browser()),
                    'platform' => $agent->platform(),
                    'device' => $agent->device(),
                    'is_mobile' => $agent->isMobile(),
                    'is_tablet' => $agent->isTablet(),
                    'is_desktop' => $agent->isDesktop(),
                    'is_robot' => $agent->isRobot(),
                ];
            }
        } catch (\Exception $e) {
            // Silently fail
        }

        return null;
    }

    /**
     * Check if request came from a mobile device.
     */
    public function getIsMobileAttribute()
    {
        $browserInfo = $this->browser_info;
        return $browserInfo ? $browserInfo['is_mobile'] : false;
    }

    /**
     * Get logs statistics for a given period.
     */
    public static function getStatistics($period = '7d')
    {
        $startDate = match($period) {
            '1d' => Carbon::now()->subDay(),
            '7d' => Carbon::now()->subWeek(),
            '30d' => Carbon::now()->subMonth(),
            default => Carbon::now()->subWeek()
        };

        return [
            'total_requests' => static::where('created_at', '>=', $startDate)->count(),
            'success_rate' => static::getSuccessRate($startDate),
            'error_rate' => static::getErrorRate($startDate),
            'avg_response_time' => static::getAverageResponseTime($startDate),
            'unique_ips' => static::where('created_at', '>=', $startDate)->distinct('ip_address')->count(),
            'top_endpoints' => static::getTopEndpoints($startDate),
            'status_distribution' => static::getStatusDistribution($startDate),
        ];
    }

    /**
     * Get success rate percentage.
     */
    public static function getSuccessRate($since = null)
    {
        $query = static::query();

        if ($since) {
            $query->where('created_at', '>=', $since);
        }

        $total = $query->count();
        if ($total === 0) return 0;

        $successful = $query->where('response_code', '>=', 200)
                           ->where('response_code', '<', 300)
                           ->count();

        return round(($successful / $total) * 100, 2);
    }

    /**
     * Get error rate percentage.
     */
    public static function getErrorRate($since = null)
    {
        $query = static::query();

        if ($since) {
            $query->where('created_at', '>=', $since);
        }

        $total = $query->count();
        if ($total === 0) return 0;

        $errors = $query->where('response_code', '>=', 400)->count();

        return round(($errors / $total) * 100, 2);
    }

    /**
     * Get average response time.
     */
    public static function getAverageResponseTime($since = null)
    {
        $query = static::query();

        if ($since) {
            $query->where('created_at', '>=', $since);
        }

        // This would require storing response time in the database
        // For now, return null
        return null;
    }

    /**
     * Get top endpoints by request count.
     */
    public static function getTopEndpoints($since = null, $limit = 10)
    {
        $query = static::query();

        if ($since) {
            $query->where('created_at', '>=', $since);
        }

        return $query->selectRaw('endpoint, COUNT(*) as count')
                    ->groupBy('endpoint')
                    ->orderBy('count', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Get status code distribution.
     */
    public static function getStatusDistribution($since = null)
    {
        $query = static::query();

        if ($since) {
            $query->where('created_at', '>=', $since);
        }

        return $query->selectRaw('response_code, COUNT(*) as count')
                    ->groupBy('response_code')
                    ->orderBy('count', 'desc')
                    ->get()
                    ->mapWithKeys(function ($item) {
                        return [$item->response_code => $item->count];
                    });
    }

    /**
     * Clean old logs.
     */
    public static function cleanOldLogs($daysToKeep = 30)
    {
        return static::where('created_at', '<', Carbon::now()->subDays($daysToKeep))->delete();
    }

    /**
     * Get logs for export.
     */
    public static function getForExport($filters = [], $limit = 1000)
    {
        $query = static::with('subscription')->orderBy('created_at', 'desc');

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('domain', 'like', "%{$search}%")
                  ->orWhere('endpoint', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (!empty($filters['domain'])) {
            $query->byDomain($filters['domain']);
        }

        if (!empty($filters['endpoint'])) {
            $query->byEndpoint($filters['endpoint']);
        }

        if (!empty($filters['date_from'])) {
            $query->dateRange($filters['date_from'], $filters['date_to'] ?? null);
        }

        return $query->limit($limit)->get();
    }
}