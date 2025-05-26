@extends('layouts.app')

@section('title', 'Cliente: ' . $client->name)

@section('header-actions')
<div class="flex items-center gap-x-3">
    <form method="POST" action="{{ route('clients.toggle-status', $client) }}" class="inline">
        @csrf
        <button type="submit"
                class="rounded-md bg-{{ $client->status === 'active' ? 'yellow' : 'green' }}-600 px-3 py-2 text-sm font-semibold text-white hover:bg-{{ $client->status === 'active' ? 'yellow' : 'green' }}-500">
            {{ $client->status === 'active' ? 'Desativar' : 'Ativar' }}
        </button>
    </form>

    <a href="{{ route('clients.edit', $client) }}"
       class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
        Editar Cliente
    </a>

    <a href="{{ route('subscriptions.create', ['client_id' => $client->id]) }}"
       class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
        Nova Subscrição
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Client Header Card -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-16 w-16 flex-shrink-0">
                        <div class="h-16 w-16 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                            <span class="text-2xl font-bold text-white">{{ substr($client->name, 0, 2) }}</span>
                        </div>
                    </div>
                    <div class="ml-6">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $client->name }}</h1>
                        @if($client->company)
                            <p class="text-lg text-gray-600">{{ $client->company }}</p>
                        @endif
                        <p class="text-sm text-gray-500">Cliente desde {{ $client->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex px-4 py-2 text-sm font-bold rounded-full
                        {{ $client->status === 'active' ? 'bg-green-100 text-green-800' :
                           ($client->status === 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ strtoupper($client->status) }}
                    </span>
                    @if($client->last_login)
                        <p class="text-sm text-gray-500 mt-1">Último login: {{ $client->last_login->diffForHumans() }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="px-6 py-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações de Contato</h2>
            <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <a href="mailto:{{ $client->email }}" class="text-blue-600 hover:text-blue-800 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            {{ $client->email }}
                        </a>
                    </dd>
                </div>

                @if($client->phone)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Telefone</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <a href="tel:{{ $client->phone }}" class="text-blue-600 hover:text-blue-800 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ $client->phone }}
                        </a>
                    </dd>
                </div>
                @endif

                @if($client->tax_number)
                <div>
                    <dt class="text-sm font-medium text-gray-500">NUIT</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $client->tax_number }}</dd>
                </div>
                @endif

                @if($client->address)
                <div class="sm:col-span-2 lg:col-span-3">
                    <dt class="text-sm font-medium text-gray-500">Endereço</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $client->address }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Subscrições</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['total_subscriptions'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Subscrições Ativas</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['active_subscriptions'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Receita Total</dt>
                            <dd class="text-2xl font-bold text-gray-900">MT {{ number_format($stats['total_revenue'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Último Pagamento</dt>
                            <dd class="text-sm font-medium text-gray-900">
                                {{ $stats['last_payment'] ? $stats['last_payment']->format('d/m/Y') : 'Nenhum' }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscriptions List -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold leading-6 text-gray-900">Subscrições do Cliente</h3>
                <a href="{{ route('subscriptions.create', ['client_id' => $client->id]) }}"
                   class="text-sm font-medium text-blue-600 hover:text-blue-500">
                    + Nova Subscrição
                </a>
            </div>
        </div>

        <div class="overflow-hidden">
            @if($client->subscriptions->count() > 0)
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Domínio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plano</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiração</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receita</th>
                        <th class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($client->subscriptions as $subscription)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-3 {{ $subscription->canAccess() ? 'bg-green-400' : 'bg-red-400' }}"></div>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' :
                                       ($subscription->status === 'suspended' ? 'bg-yellow-100 text-yellow-800' :
                                        ($subscription->status === 'trial' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $subscription->domain }}</div>
                            @if($subscription->subdomain)
                                <div class="text-sm text-gray-500">{{ $subscription->subdomain }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $subscription->plan->name }}</div>
                            <div class="text-sm text-gray-500">MT {{ number_format($subscription->plan->price, 2) }}/{{ $subscription->plan->billing_cycle }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($subscription->ends_at)
                                <div>{{ $subscription->ends_at->format('d/m/Y') }}</div>
                                @if($subscription->days_until_expiry !== null)
                                    <div class="text-xs {{ $subscription->days_until_expiry <= 7 ? 'text-red-600' : 'text-gray-400' }}">
                                        {{ $subscription->days_until_expiry > 0 ? $subscription->days_until_expiry . ' dias' : 'Expirado' }}
                                    </div>
                                @endif
                            @elseif($subscription->trial_ends_at)
                                <div class="text-blue-600">Trial até {{ $subscription->trial_ends_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-blue-500">{{ $subscription->trial_days_left }} dias restantes</div>
                            @else
                                <span class="text-gray-400">Sem expiração</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            MT {{ number_format($subscription->total_revenue, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('subscriptions.show', $subscription) }}"
                                   class="text-blue-600 hover:text-blue-900">Ver</a>
                                <a href="{{ route('subscriptions.edit', $subscription) }}"
                                   class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                @if($subscription->canAccess())
                                    <a href="https://{{ $subscription->domain }}" target="_blank"
                                       class="text-green-600 hover:text-green-900">Visitar</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma subscrição</h3>
                <p class="mt-1 text-sm text-gray-500">Este cliente ainda não possui subscrições ativas.</p>
                <div class="mt-6">
                    <a href="{{ route('subscriptions.create', ['client_id' => $client->id]) }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
                        </svg>
                        Criar Primeira Subscrição
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Payment History -->
    @if($paymentHistory->count() > 0)
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold leading-6 text-gray-900">Histórico de Pagamentos</h3>
        </div>

        <div class="overflow-hidden">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Método</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subscrição</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referência</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($paymentHistory as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $payment->last_payment_date->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            MT {{ number_format($payment->amount_paid, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($payment->payment_method)
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                    {{ ucfirst($payment->payment_method) }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <a href="{{ route('subscriptions.show', $payment) }}" class="text-blue-600 hover:text-blue-800">
                                {{ $payment->domain }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $payment->payment_reference ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Email Activity -->
    @if($client->emailLogs->count() > 0)
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold leading-6 text-gray-900">Atividade de Email Recente</h3>
                <a href="{{ route('email-logs.index', ['client_id' => $client->id]) }}"
                   class="text-sm font-medium text-blue-600 hover:text-blue-500">
                    Ver todos
                </a>
            </div>
        </div>

        <div class="overflow-hidden">
            <ul class="divide-y divide-gray-200">
                @foreach($client->emailLogs->take(5) as $email)
                <li class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-2 h-2 rounded-full {{ $email->status === 'sent' ? 'bg-green-400' : 'bg-red-400' }}"></div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">{{ $email->subject }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ ucfirst($email->type) }} • {{ $email->created_at->diffForHumans() }}
                                    @if($email->status === 'failed')
                                        • <span class="text-red-600">Falhou</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                {{ $email->status === 'sent' ? 'bg-green-100 text-green-800' :
                                   ($email->status === 'queued' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $email->status === 'sent' ? 'Enviado' :
                                   ($email->status === 'queued' ? 'Na Fila' : 'Falhou') }}
                            </span>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
</div>
@endsection