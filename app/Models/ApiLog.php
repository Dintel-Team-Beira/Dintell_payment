<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ApiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'domain',
        'ip_address',
        'user_agent',
        'endpoint',
        'request_data',
        'response_data',
        'response_code'
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'created_at' => 'datetime'
    ];

    // Disable updated_at since we only have created_at
    public $timestamps = false;

    protected $dates = ['created_at'];

    /**
     * Relationship with subscription
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get status based on response code
     */
    public function getStatusAttribute()
    {
        if ($this->response_code >= 200 && $this->response_code < 300) {
            return 'success';
        } elseif ($this->response_code >= 400 && $this->response_code < 500) {
            return 'client_error';
        } elseif ($this->response_code >= 500) {
            return 'server_error';
        }
        return 'unknown';
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case 'success':
                return 'Sucesso';
            case 'client_error':
                return 'Erro do Cliente';
            case 'server_error':
                return 'Erro do Servidor';
            default:
                return 'Desconhecido';
        }
    }

    /**
     * Get method from endpoint
     */
    public function getMethodAttribute()
    {
        // Extract HTTP method from endpoint if stored
        if (preg_match('/^(GET|POST|PUT|DELETE|PATCH)\s/', $this->endpoint, $matches)) {
            return $matches[1];
        }
        return 'GET'; // Default
    }

    /**
     * Get clean endpoint without method
     */
    public function getCleanEndpointAttribute()
    {
        return preg_replace('/^(GET|POST|PUT|DELETE|PATCH)\s/', '', $this->endpoint);
    }

    /**
     * Scope for filtering by domain
     */
    public function scopeByDomain($query, $domain)
    {
        return $query->where('domain', $domain);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        switch ($status) {
            case 'success':
                return $query->whereBetween('response_code', [200, 299]);
            case 'client_error':
                return $query->whereBetween('response_code', [400, 499]);
            case 'server_error':
                return $query->where('response_code', '>=', 500);
            case 'error':
                return $query->where('response_code', '>=', 400);
            default:
                return $query;
        }
    }

    /**
     * Scope for filtering by endpoint
     */
    public function scopeByEndpoint($query, $endpoint)
    {
        return $query->where('endpoint', 'like', "%{$endpoint}%");
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate = null)
    {
        if ($endDate) {
            return $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        return $query->whereDate('created_at', '>=', $startDate);
    }

    /**
     * Scope for recent logs
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', Carbon::now()->subHours($hours));
    }

    /**
     * Get formatted request data
     */
    public function getFormattedRequestDataAttribute()
    {
        if (empty($this->request_data)) {
            return null;
        }
        return json_encode($this->request_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get formatted response data
     */
    public function getFormattedResponseDataAttribute()
    {
        if (empty($this->response_data)) {
            return null;
        }
        return json_encode($this->response_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get response code color class
     */
    public function getResponseCodeColorAttribute()
    {
        if ($this->response_code >= 200 && $this->response_code < 300) {
            return 'text-green-600 bg-green-100';
        } elseif ($this->response_code >= 400 && $this->response_code < 500) {
            return 'text-yellow-600 bg-yellow-100';
        } elseif ($this->response_code >= 500) {
            return 'text-red-600 bg-red-100';
        }
        return 'text-gray-600 bg-gray-100';
    }
}