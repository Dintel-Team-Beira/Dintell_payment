<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Client;
use App\Models\SubscriptionPlan;
use App\Models\ApiLog;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 30);
        $startDate = Carbon::now()->subDays($period);

        // Estatísticas gerais
        $stats = [
            'total_clients' => Client::count(),
            'active_clients' => Client::where('status', 'active')->count(),
            'total_subscriptions' => Subscription::count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'suspended_subscriptions' => Subscription::where('status', 'suspended')->count(),
            'expired_subscriptions' => Subscription::whereNotNull('ends_at')
                                                ->where('ends_at', '<', now())
                                                ->count(),
            'trial_subscriptions' => Subscription::where('status', 'trial')->count(),
            'monthly_revenue' => Subscription::where('last_payment_date', '>=', now()->startOfMonth())
                                           ->sum('amount_paid'),
            'total_revenue' => Subscription::sum('total_revenue'),
            'expiring_soon' => Subscription::where('ends_at', '<=', now()->addDays(7))
                                         ->where('ends_at', '>', now())
                                         ->where('status', 'active')
                                         ->count(),
            'failed_payments' => Subscription::where('payment_failures', '>', 0)->count()
        ];

        // Gráfico de receita (CORRIGIDO)
        $revenueChart = Subscription::select(
                DB::raw('DATE(last_payment_date) as date'),
                DB::raw('SUM(amount_paid) as revenue'),
                DB::raw('COUNT(*) as payments')
            )
            ->where('last_payment_date', '>=', $startDate)
            ->whereNotNull('last_payment_date')
            ->groupBy(DB::raw('DATE(last_payment_date)'))
            ->orderBy('date')
            ->get();

        // Gráfico de novas subscrições (CORRIGIDO)
        $subscriptionsChart = Subscription::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Uso da API (CORRIGIDO)
        $apiUsage = ApiLog::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as requests'),
                DB::raw('COUNT(DISTINCT domain) as unique_domains')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Top clientes por receita (CORRIGIDO)
        $topClients = Client::withSum('subscriptions', 'total_revenue')
                           ->withCount('subscriptions')
                           ->having('subscriptions_sum_total_revenue', '>', 0)
                           ->orderByDesc('subscriptions_sum_total_revenue')
                           ->limit(10)
                           ->get()
                           ->map(function($client) {
                               $client->revenue = $client->subscriptions_sum_total_revenue ?? 0;
                               return $client;
                           });

        // Subscrições expirando
        $expiring = Subscription::with(['client', 'plan'])
            ->where('ends_at', '<=', now()->addDays(7))
            ->where('ends_at', '>', now())
            ->where('status', 'active')
            ->orderBy('ends_at')
            ->limit(10)
            ->get();

        // Atividade recente
        $recentActivity = $this->getRecentActivity();

        // Distribuição por planos (CORRIGIDO)
        $planDistribution = SubscriptionPlan::withCount(['subscriptions' => function($query) {
                $query->where('status', 'active');
            }])
            ->get()
            ->filter(function($plan) {
                return $plan->subscriptions_count > 0;
            })
            ->map(function($plan) {
                return (object)[
                    'name' => $plan->name,
                    'price' => $plan->price,
                    'count' => $plan->subscriptions_count
                ];
            });

        return view('dashboard', compact(
            'stats', 'revenueChart', 'subscriptionsChart', 'apiUsage',
            'topClients', 'expiring', 'recentActivity', 'planDistribution', 'period'
        ));
    }

    private function getRecentActivity()
    {
        $activities = collect();

        // Novas subscrições
        $newSubscriptions = Subscription::with(['client', 'plan'])
            ->where('created_at', '>=', now()->subDays(7))
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($sub) {
                return [
                    'type' => 'new_subscription',
                    'message' => "Nova subscrição: {$sub->client->name} - {$sub->plan->name}",
                    'date' => $sub->created_at,
                    'client' => $sub->client->name,
                    'url' => route('subscriptions.show', $sub)
                ];
            });

        // Pagamentos recebidos
        $payments = Subscription::with(['client', 'plan'])
            ->whereNotNull('last_payment_date')
            ->where('last_payment_date', '>=', now()->subDays(7))
            ->latest('last_payment_date')
            ->limit(5)
            ->get()
            ->map(function ($sub) {
                return [
                    'type' => 'payment',
                    'message' => "Pagamento recebido: {$sub->client->name} - MT " . number_format($sub->amount_paid, 2),
                    'date' => $sub->last_payment_date,
                    'client' => $sub->client->name,
                    'url' => route('subscriptions.show', $sub)
                ];
            });

        // Suspensões
        $suspensions = Subscription::with(['client'])
            ->where('status', 'suspended')
            ->where('suspended_at', '>=', now()->subDays(7))
            ->latest('suspended_at')
            ->limit(5)
            ->get()
            ->map(function ($sub) {
                return [
                    'type' => 'suspension',
                    'message' => "Subscrição suspensa: {$sub->client->name} - {$sub->domain}",
                    'date' => $sub->suspended_at,
                    'client' => $sub->client->name,
                    'url' => route('subscriptions.show', $sub)
                ];
            });

        return $activities->merge($newSubscriptions)
                         ->merge($payments)
                         ->merge($suspensions)
                         ->sortByDesc('date')
                         ->take(15);
    }
}