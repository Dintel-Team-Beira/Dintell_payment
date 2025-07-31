<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AdminInvoice extends Model
{
    use HasFactory;

    protected $table = 'invoices'; // Reutiliza a tabela existente Invoice

    protected $fillable = [
        'company_id',
        'client_id',
        'number',
        'date',
        'due_date',
        'status',
        'subtotal',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'currency',
        'notes',
        'payment_method',
        'payment_date'
    ];

    protected $casts = [
        'date' => 'datetime',
        'due_date' => 'datetime',
        'payment_date' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2'
    ];

    const STATUSES = [
        'draft' => 'Rascunho',
        'sent' => 'Enviada',
        'paid' => 'Paga',
        'overdue' => 'Vencida',
        'cancelled' => 'Cancelada'
    ];

    const PAYMENT_METHODS = [
        'cash' => 'Dinheiro',
        'bank_transfer' => 'Transferência Bancária',
        'cheque' => 'Cheque',
        'credit_card' => 'Cartão',
        'mpesa' => 'M-Pesa',
        'emola' => 'E-Mola'
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes para relatórios administrativos
    public function scopeGlobalStats($query)
    {
        return $query->selectRaw('
            COUNT(*) as total_invoices,
            SUM(CASE WHEN status = "paid" THEN total_amount ELSE 0 END) as total_revenue,
            SUM(CASE WHEN status = "overdue" THEN total_amount ELSE 0 END) as overdue_amount,
            AVG(total_amount) as average_invoice_value,
            COUNT(CASE WHEN status = "paid" THEN 1 END) as paid_invoices,
            COUNT(CASE WHEN status = "overdue" THEN 1 END) as overdue_invoices
        ');
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereNotIn('status', ['paid', 'cancelled']);
    }

    public function scopeRecentActivity($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Accessors
    public function getStatusNameAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getPaymentMethodNameAttribute()
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date < now() && !in_array($this->status, ['paid', 'cancelled']);
    }

    public function getRemainingAmountAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_amount, 2) . ' ' . $this->currency;
    }

    // Static methods para dashboard administrativo
    public static function getGlobalStats($period = 'month')
    {
        $query = self::query();

        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
            case 'year':
                $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
                break;
        }

        return $query->globalStats()->first();
    }

    public static function getRevenueByMonth($year = null)
    {
        $year = $year ?? date('Y');

        return self::selectRaw('
            MONTH(date) as month,
            SUM(CASE WHEN status = "paid" THEN total_amount ELSE 0 END) as revenue,
            COUNT(*) as invoices_count
        ')
        ->whereYear('date', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->mapWithKeys(function ($item) {
            return [$item->month => [
                'revenue' => $item->revenue,
                'invoices' => $item->invoices_count
            ]];
        });
    }

    public static function getTopCompanies($limit = 10)
    {
        return self::select('company_id')
                   ->with('company:id,name')
                   ->selectRaw('
                       SUM(total_amount) as total_revenue,
                       COUNT(*) as total_invoices
                   ')
                   ->where('status', 'paid')
                   ->groupBy('company_id')
                   ->orderByDesc('total_revenue')
                   ->limit($limit)
                   ->get();
    }

    public static function getOverdueStats()
    {
        return [
            'count' => self::overdue()->count(),
            'total_amount' => self::overdue()->sum('total_amount'),
            'by_company' => self::overdue()
                               ->with('company:id,name')
                               ->selectRaw('company_id, COUNT(*) as count, SUM(total_amount) as amount')
                               ->groupBy('company_id')
                               ->get()
        ];
    }

    public static function getRecentActivity($days = 7)
    {
        return self::with(['company:id,name', 'client:id,name'])
                   ->recentActivity($days)
                   ->orderBy('created_at', 'desc')
                   ->limit(50)
                   ->get();
    }
}
