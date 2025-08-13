<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait LogsActivity
{
    /**
     * Log activity to Laravel logs
     */
    protected function logActivity(string $action, array $data = []): void
    {
        $user = auth()->user();

        $logData = [
            'action' => $action,
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : 'Guest',
            'user_email' => $user ? $user->email : null,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'timestamp' => now()->toISOString(),
            'data' => $data
        ];

        Log::channel('activity')->info($action, $logData);
    }

    /**
     * Log admin activity
     */
    protected function logAdminActivity(string $action, array $data = []): void
    {
        $user = auth()->user();

        $logData = [
            'action' => $action,
            'admin_id' => $user ? $user->id : null,
            'admin_name' => $user ? $user->name : 'Unknown',
            'admin_email' => $user ? $user->email : null,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'timestamp' => now()->toISOString(),
            'data' => $data
        ];

        Log::channel('admin')->info($action, $logData);
    }

    /**
     * Log company activity
     */
    protected function logCompanyActivity(string $action, array $data = []): void
    {
        $user = auth()->user();
        $company = session('current_company');

        $logData = [
            'action' => $action,
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : 'Unknown',
            'company_id' => $company ? $company->id : null,
            'company_name' => $company ? $company->name : null,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'timestamp' => now()->toISOString(),
            'data' => $data
        ];

        Log::channel('company')->info($action, $logData);
    }

    
}
