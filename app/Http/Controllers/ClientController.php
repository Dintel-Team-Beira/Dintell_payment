<?php
// app/Http/Controllers/ClientController.php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with(['subscriptions' => function($q) {
            $q->with('plan');
        }]);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $clients = $query->withCount(['subscriptions', 'activeSubscriptions'])
                        ->withSum('subscriptions', 'total_revenue')
                        ->latest()
                        ->paginate(15);

        return view('clients.index', compact('clients'));
    }

    public function show(string $tenant,Client $client)
    {
        $client->load(['subscriptions.plan', 'emailLogs']);

        // Estatísticas do cliente
        $stats = [
            'total_subscriptions' => $client->subscriptions()->count(),
            'active_subscriptions' => $client->activeSubscriptions()->count(),
            'total_revenue' => $client->totalRevenue(),
            'last_payment' => $client->subscriptions()->latest('last_payment_date')->first()?->last_payment_date,
            'avg_monthly_revenue' => $client->subscriptions()->where('status', 'active')->avg('amount_paid')
        ];

        // Histórico de pagamentos
        $paymentHistory = $client->subscriptions()
                                ->whereNotNull('last_payment_date')
                                ->orderBy('last_payment_date', 'desc')
                                ->limit(10)
                                ->get();

        return view('clients.show', compact('client', 'stats', 'paymentHistory'));
    }

    public function create()
    {
        $user = auth()->user();
        $company = $user->company;
        $excededUsage = false;
        if ($company->plan_id && $company->plan) {
            $clientUsage = $company->getClientUsage(); 
            if ($clientUsage['exceeded']) {
                $excededUsage = true;
            }
        }
        return view('clients.create',compact('excededUsage', 'company'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'tax_number' => 'nullable|string|max:50',
            'contact_preferences' => 'nullable|array'
        ]);
        $validated['company_id'] = auth()->user()->company->id;
        $client = Client::create($validated);

        return redirect()->route('clients.show', $client)
                        ->with('success', 'Cliente criado com sucesso!');
    }

    public function edit(string $tenant, Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, string $tenant, Client $client)
    {
        // dd(auth()->user()->company->id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'tax_number' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,blocked',
            'contact_preferences' => 'nullable|array'
            
        ]);
        $validated['company_id'] = auth()->user()->company->id;

        $client->update($validated);

        return redirect()->route('clients.show', $client)
                        ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(string $tenant, Client $client)
    {
        // Verificar se tem subscrições ativas
        if ($client->activeSubscriptions()->count() > 0) {
            return back()->with('error', 'Não é possível excluir cliente com subscrições ativas.');
        }

        $client->delete();

        return redirect()->route('clients.index')
                        ->with('success', 'Cliente excluído com sucesso!');
    }

    public function toggleStatus(string $tenant, Client $client)
    {
        $newStatus = $client->status === 'active' ? 'inactive' : 'active';
        $client->update(['status' => $newStatus]);

        // Se desativar cliente, suspender todas as subscrições
        if ($newStatus === 'inactive') {
            $client->subscriptions()->where('status', 'active')->each(function($subscription) {
                $subscription->suspend('Cliente desativado');
            });
        }

        return back()->with('success', 'Status do cliente alterado com sucesso!');
    }
}
