<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;

class AdminMonitoringController extends Controller
{
    /**
     * Performance monitoring dashboard.
     */
    public function performance()
    {
        $metrics = $this->getPerformanceMetrics();
        $systemInfo = $this->getSystemInfo();
        $databaseStats = $this->getDatabaseStats();




        return view('admin.monitoring.performance', compact('metrics', 'systemInfo', 'databaseStats'));
    }

    /**
     * System health check.
     */
    public function health()
    {
        $healthChecks = $this->runHealthChecks();
        $services = $this->checkServices();
        $alerts = $this->getSystemAlerts();

        return view('admin.monitoring.health', compact('healthChecks', 'services', 'alerts'));
    }

    /**
     * System metrics API endpoint.
     */
    public function metrics(Request $request)
    {
        $timeframe = $request->get('timeframe', '24h');

        $metrics = [
            'cpu_usage' => $this->getCpuUsage($timeframe),
            'memory_usage' => $this->getMemoryUsage($timeframe),
            'disk_usage' => $this->getDiskUsage(),
            'response_times' => $this->getResponseTimes($timeframe),
            'active_users' => $this->getActiveUsers($timeframe),
            'error_rates' => $this->getErrorRates($timeframe)
        ];

        return response()->json($metrics);
    }

    /**
     * Get performance metrics.
     */
    private function getPerformanceMetrics()
    {
        return [
            'response_time' => [
                'current' => $this->getCurrentResponseTime(),
                'average_24h' => $this->getAverageResponseTime(24),
                'status' => 'good' // good, warning, critical
            ],
            'memory_usage' => [
                'current' => $this->getCurrentMemoryUsage(),
                'peak_24h' => $this->getPeakMemoryUsage(24),
                'limit' => ini_get('memory_limit')
            ],
            'cpu_usage' => [
                'current' => $this->getCurrentCpuUsage(),
                'average_24h' => $this->getAverageCpuUsage(24)
            ],
            'disk_usage' => [
                'used' => $this->getDiskUsed(),
                'free' => $this->getDiskFree(),
                'total' => $this->getDiskTotal(),
                'percentage' => $this->getDiskUsagePercentage()
            ],
            'database' => [
                'connections' => $this->getDatabaseConnections(),
                'slow_queries' => $this->getSlowQueries(),
                'size' => $this->getDatabaseSize()
            ],
            'cache' => [
                'hit_rate' => $this->getCacheHitRate(),
                'size' => $this->getCacheSize(),
                'keys' => $this->getCacheKeys()
            ]
        ];
    }

    /**
     * Get system information.
     */
    private function getSystemInfo()
    {
        return [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'os' => php_uname('s') . ' ' . php_uname('r'),
            'uptime' => $this->getSystemUptime(),
            'load_average' => $this->getLoadAverage(),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'environment' => config('app.env'),
            'debug_mode' => config('app.debug'),
            'maintenance_mode' => app()->isDownForMaintenance()
        ];
    }

    /**
     * Get database statistics.
     */
    private function getDatabaseStats()
    {
        try {
            $connection = DB::connection();
            $database = $connection->getDatabaseName();

            // Estatísticas específicas para MySQL/MariaDB
            if ($connection->getDriverName() === 'mysql') {
                $tables = DB::select("SELECT
                    COUNT(*) as table_count,
                    SUM(data_length + index_length) as total_size,
                    SUM(data_length) as data_size,
                    SUM(index_length) as index_size
                    FROM information_schema.TABLES
                    WHERE table_schema = ?", [$database]);

                $status = DB::select("SHOW STATUS LIKE 'Threads_connected'");
                $maxConnections = DB::select("SHOW VARIABLES LIKE 'max_connections'");

                return [
                    'database_name' => $database,
                    'driver' => $connection->getDriverName(),
                    'table_count' => $tables[0]->table_count ?? 0,
                    'total_size' => $this->formatBytes($tables[0]->total_size ?? 0),
                    'data_size' => $this->formatBytes($tables[0]->data_size ?? 0),
                    'index_size' => $this->formatBytes($tables[0]->index_size ?? 0),
                    'active_connections' => $status[0]->Value ?? 0,
                    'max_connections' => $maxConnections[0]->Value ?? 0,
                    'status' => 'connected'
                ];
            }

            // Fallback para outros drivers
            return [
                'database_name' => $database,
                'driver' => $connection->getDriverName(),
                'status' => 'connected'
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Run system health checks.
     */
    private function runHealthChecks()
    {
        $checks = [];

        // Database connectivity
        $checks[] = [
            'name' => 'Database Connection',
            'status' => $this->checkDatabaseConnection(),
            'message' => $this->getDatabaseConnectionMessage(),
            'critical' => true
        ];

        // Cache system
        $checks[] = [
            'name' => 'Cache System',
            'status' => $this->checkCacheSystem(),
            'message' => $this->getCacheSystemMessage(),
            'critical' => false
        ];

        // Storage permissions
        $checks[] = [
            'name' => 'Storage Permissions',
            'status' => $this->checkStoragePermissions(),
            'message' => $this->getStoragePermissionsMessage(),
            'critical' => true
        ];

        // Queue system
        $checks[] = [
            'name' => 'Queue System',
            'status' => $this->checkQueueSystem(),
            'message' => $this->getQueueSystemMessage(),
            'critical' => false
        ];

        // Disk space
        $checks[] = [
            'name' => 'Disk Space',
            'status' => $this->checkDiskSpace(),
            'message' => $this->getDiskSpaceMessage(),
            'critical' => true
        ];

        // Memory usage
        $checks[] = [
            'name' => 'Memory Usage',
            'status' => $this->checkMemoryUsage(),
            'message' => $this->getMemoryUsageMessage(),
            'critical' => false
        ];

        return $checks;
    }

    /**
     * Check external services.
     */
    private function checkServices()
    {
        return [
            'database' => $this->checkDatabaseConnection(),
            'cache' => $this->checkCacheSystem(),
            'mail' => $this->checkMailService(),
            'queue' => $this->checkQueueSystem(),
            'storage' => $this->checkStorageService()
        ];
    }

    /**
     * Get system alerts.
     */
    private function getSystemAlerts()
    {
        $alerts = [];

        // High disk usage
        if ($this->getDiskUsagePercentage() > 85) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'High Disk Usage',
                'message' => 'Disk usage is above 85%. Consider cleaning up old files.',
                'timestamp' => now()
            ];
        }

        // High memory usage
        if ($this->getMemoryUsagePercentage() > 80) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'High Memory Usage',
                'message' => 'Memory usage is above 80%. Monitor application performance.',
                'timestamp' => now()
            ];
        }

        // Failed jobs in queue
        $failedJobs = $this->getFailedJobsCount();
        if ($failedJobs > 0) {
            $alerts[] = [
                'type' => 'error',
                'title' => 'Failed Jobs',
                'message' => "There are {$failedJobs} failed jobs in the queue.",
                'timestamp' => now()
            ];
        }

        return $alerts;
    }

    // Helper methods for metrics calculation
    private function getCurrentResponseTime() { return round(microtime(true) * 1000 - (LARAVEL_START * 1000), 2); }
    private function getAverageResponseTime($hours) { return 125; } // Mock data
    private function getCurrentMemoryUsage() { return $this->formatBytes(memory_get_usage(true)); }
    private function getPeakMemoryUsage($hours) { return $this->formatBytes(memory_get_peak_usage(true)); }
    private function getCurrentCpuUsage() { return round(sys_getloadavg()[0] * 100 / 4, 2); } // Approximate
    private function getAverageCpuUsage($hours) { return 15.5; } // Mock data

    private function getDiskUsed() { return $this->formatBytes(disk_total_space('/') - disk_free_space('/')); }
    private function getDiskFree() { return $this->formatBytes(disk_free_space('/')); }
    private function getDiskTotal() { return $this->formatBytes(disk_total_space('/')); }
    private function getDiskUsagePercentage() {
        $total = disk_total_space('/');
        $free = disk_free_space('/');
        return round((($total - $free) / $total) * 100, 2);
    }

    private function getDatabaseConnections() {
        try {
            $result = DB::select("SHOW STATUS LIKE 'Threads_connected'");
            return $result[0]->Value ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getSlowQueries() { return 0; } // Implement based on your needs
    private function getDatabaseSize() { return '50 MB'; } // Mock data
    private function getCacheHitRate() { return 95.5; } // Mock data
    private function getCacheSize() { return '15 MB'; } // Mock data
    private function getCacheKeys() { return 1250; } // Mock data

    private function getSystemUptime() {
        if (function_exists('sys_getloadavg')) {
            $uptime = shell_exec('uptime');
            return $uptime ?: 'Unknown';
        }
        return 'Unknown';
    }

    private function getLoadAverage() {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return sprintf("%.2f, %.2f, %.2f", $load[0], $load[1], $load[2]);
        }
        return 'Unknown';
    }

    // Health check methods
    private function checkDatabaseConnection() {
        try {
            DB::connection()->getPdo();
            return 'healthy';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    private function getDatabaseConnectionMessage() {
        return $this->checkDatabaseConnection() === 'healthy'
            ? 'Database connection is working properly'
            : 'Unable to connect to database';
    }

    private function checkCacheSystem() {
        try {
            Cache::put('health_check', 'test', 10);
            $value = Cache::get('health_check');
            Cache::forget('health_check');
            return $value === 'test' ? 'healthy' : 'warning';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    private function getCacheSystemMessage() {
        $status = $this->checkCacheSystem();
        return match($status) {
            'healthy' => 'Cache system is working properly',
            'warning' => 'Cache system has issues',
            'error' => 'Cache system is not working'
        };
    }

    private function checkStoragePermissions() {
        $paths = [
            storage_path('app'),
            storage_path('logs'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views')
        ];

        foreach ($paths as $path) {
            if (!is_writable($path)) {
                return 'error';
            }
        }

        return 'healthy';
    }

    private function getStoragePermissionsMessage() {
        return $this->checkStoragePermissions() === 'healthy'
            ? 'All storage directories are writable'
            : 'Some storage directories are not writable';
    }

    private function checkQueueSystem() {
        try {
            // Check if queue driver is properly configured
            $driver = config('queue.default');
            if ($driver === 'sync') {
                return 'warning'; // Sync driver is not recommended for production
            }
            return 'healthy';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    private function getQueueSystemMessage() {
        $status = $this->checkQueueSystem();
        return match($status) {
            'healthy' => 'Queue system is properly configured',
            'warning' => 'Queue is using sync driver (not recommended for production)',
            'error' => 'Queue system has configuration issues'
        };
    }

    private function checkDiskSpace() {
        return $this->getDiskUsagePercentage() < 90 ? 'healthy' : 'warning';
    }

    private function getDiskSpaceMessage() {
        $percentage = $this->getDiskUsagePercentage();
        return "Disk usage: {$percentage}%";
    }

    private function checkMemoryUsage() {
        return $this->getMemoryUsagePercentage() < 85 ? 'healthy' : 'warning';
    }

    private function getMemoryUsageMessage() {
        $percentage = $this->getMemoryUsagePercentage();
        return "Memory usage: {$percentage}%";
    }

    private function getMemoryUsagePercentage() {
        $limit = $this->parseMemoryLimit(ini_get('memory_limit'));
        $used = memory_get_usage(true);
        return $limit > 0 ? round(($used / $limit) * 100, 2) : 0;
    }

    private function parseMemoryLimit($limit) {
        if ($limit == -1) return 0;

        $limit = strtolower($limit);
        $bytes = (int) $limit;

        if (str_contains($limit, 'k')) $bytes *= 1024;
        if (str_contains($limit, 'm')) $bytes *= 1024 * 1024;
        if (str_contains($limit, 'g')) $bytes *= 1024 * 1024 * 1024;

        return $bytes;
    }

    private function checkMailService() {
        try {
            // Basic mail configuration check
            $driver = config('mail.default');
            return !empty($driver) ? 'healthy' : 'warning';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    private function checkStorageService() {
        return is_writable(storage_path()) ? 'healthy' : 'error';
    }

    private function getFailedJobsCount() {
        try {
            return DB::table('failed_jobs')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    // Time-series data methods (implement based on your logging system)
    private function getCpuUsage($timeframe) { return []; }
    private function getMemoryUsage($timeframe) { return []; }
    private function getResponseTimes($timeframe) { return []; }
    private function getActiveUsers($timeframe) { return []; }
    private function getErrorRates($timeframe) { return []; }

    private function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
