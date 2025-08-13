@extends('layouts.admin')

@section('title', 'Detalhes do Usuário')

@section('content')
<div class="container px-6 py-8 mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
                <p class="mt-2 text-gray-600">{{ $user->email }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.users.edit', $user) }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Voltar
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Profile Information -->
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informações do Perfil</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="flex flex-col items-center">
                        <!-- Avatar -->
                        <div class="w-24 h-24 mb-4">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}"
                                     alt="{{ $user->name }}"
                                     class="object-cover w-24 h-24 rounded-full">
                            @else
                                <div class="flex items-center justify-center w-24 h-24 bg-gray-200 rounded-full">
                                    <span class="text-2xl font-semibold text-gray-600">
                                        {{ substr($user->name, 0, 2) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Status Badge -->
                        <div class="mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <svg class="w-2 h-2 mr-1.5" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3"/>
                                </svg>
                                {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>

                        <!-- User Details -->
                        <div class="w-full space-y-4">
                            <div class="text-center">
                                <h4 class="text-lg font-semibold text-gray-900">{{ $user->name }}</h4>
                                <p class="text-sm text-gray-500">{{ $user->role ?? 'Usuário' }}</p>
                            </div>

                            <div class="pt-4 space-y-3 border-t border-gray-200">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Email:</span>
                                    <span class="text-gray-900">{{ $user->email }}</span>
                                </div>

                                @if($user->phone)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Telefone:</span>
                                        <span class="text-gray-900">{{ $user->phone }}</span>
                                    </div>
                                @endif

                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Empresa:</span>
                                    <span class="text-gray-900">{{ $user->company->name ?? 'N/A' }}</span>
                                </div>

                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Criado em:</span>
                                    <span class="text-gray-900">{{ $user->created_at->format('d/m/Y') }}</span>
                                </div>

                                @if($userStats['last_login'])
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Último login:</span>
                                        <span class="text-gray-900">{{ \Carbon\Carbon::parse($userStats['last_login'])->format('d/m/Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Ações Rápidas</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    @if($user->is_active)
                        <button onclick="suspendUser({{ $user->id }})"
                                class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-red-700 border border-red-300 rounded-md shadow-sm bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                            </svg>
                            Suspender Usuário
                        </button>
                    @else
                        <button onclick="activateUser({{ $user->id }})"
                                class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-green-700 border border-green-300 rounded-md shadow-sm bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Ativar Usuário
                        </button>
                    @endif

                    <button onclick="resetPassword({{ $user->id }})"
                            class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-blue-700 border border-blue-300 rounded-md shadow-sm bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2V5a2 2 0 00-2-2m-2 2h.01M5 15a2 2 0 002 2m0 0a2 2 0 002 2m-2-2a2 2 0 00-2 2m2-2v-.01"/>
                        </svg>
                        Redefinir Senha
                    </button>

                    <button onclick="sendWelcomeEmail({{ $user->id }})"
                            class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Enviar Email de Boas-vindas
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-md">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Faturas</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($userStats['invoices_count']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-md">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Cotações</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($userStats['quotes_count']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-8 h-8 bg-yellow-100 rounded-md">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Receita Total</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($userStats['total_revenue'], 2) }} MT</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-8 h-8 bg-purple-100 rounded-md">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Média/Fatura</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $userStats['invoices_count'] > 0 ? number_format($userStats['total_revenue'] / $userStats['invoices_count'], 2) : '0.00' }} MT
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
        {{-- Substituir o conteúdo da seção de Atividade Recente na view --}}
<div class="bg-white border border-gray-200 shadow-sm rounded-xl">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Atividade Recente</h3>
            <span class="text-sm text-gray-500">Últimos 30 dias</span>
        </div>
    </div>
    <div class="px-6 py-4">
        @if(isset($recentActivity) && $recentActivity->count() > 0)
            {{-- Se existe AdminActivity --}}
            <div class="flow-root">
                <ul class="-mb-8">
                    @foreach($recentActivity as $activity)
                    <li>
                        <div class="relative pb-8">
                            @if(!$loop->last)
                            <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                            @endif
                            <div class="relative flex items-start space-x-3">
                                <div class="relative">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full
                                        {{ $activity->severity === 'critical' ? 'bg-red-100' :
                                           ($activity->severity === 'high' ? 'bg-yellow-100' : 'bg-blue-100') }}">
                                        <svg class="w-5 h-5 {{ $activity->severity === 'critical' ? 'text-red-600' :
                                                                 ($activity->severity === 'high' ? 'text-yellow-600' : 'text-blue-600') }}"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($activity->category === 'user_management')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            @elseif($activity->category === 'company_management')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            @elseif($activity->category === 'security')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            @endif
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div>
                                        <div class="text-sm text-gray-500">
                                            <span class="font-medium text-gray-900">{{ $activity->description }}</span>
                                        </div>
                                        <p class="mt-0.5 text-xs text-gray-500">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </p>
                                        @if($activity->category)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 mt-1">
                                            {{ $activity->category_label }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        @elseif($user->company_id)
            {{-- Se não tem AdminActivity, mas tem empresa, mostrar atividades da empresa --}}
            @php
                $activities = collect();

                // Buscar faturas da empresa do usuário
                $companyInvoices = \App\Models\Invoice::where('company_id', $user->company_id)
                    ->latest()
                    ->limit(5)
                    ->get();

                foreach($companyInvoices as $invoice) {
                    $activities->push([
                        'type' => 'invoice',
                        'title' => 'Fatura criada',
                        'description' => 'Fatura #' . $invoice->invoice_number,
                        'value' => number_format($invoice->total, 2) . ' MT',
                        'date' => $invoice->created_at,
                        'status' => $invoice->status
                    ]);
                }

                // Buscar quotes da empresa do usuário
                $companyQuotes = \App\Models\Quote::where('company_id', $user->company_id)
                    ->latest()
                    ->limit(3)
                    ->get();

                foreach($companyQuotes as $quote) {
                    $activities->push([
                        'type' => 'quote',
                        'title' => 'Orçamento criado',
                        'description' => 'Orçamento #' . $quote->quote_number,
                        'value' => number_format($quote->total, 2) . ' MT',
                        'date' => $quote->created_at,
                        'status' => $quote->status
                    ]);
                }

                $activities = $activities->sortByDesc('date')->take(8);
            @endphp

            @if($activities->count() > 0)
            <div class="flow-root">
                <ul class="-mb-8">
                    @foreach($activities as $activity)
                    <li>
                        <div class="relative pb-8">
                            @if(!$loop->last)
                            <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                            @endif
                            <div class="relative flex items-start space-x-3">
                                <div class="relative">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full
                                        {{ $activity['type'] === 'invoice' ? 'bg-green-100' : 'bg-blue-100' }}">
                                        <svg class="w-5 h-5 {{ $activity['type'] === 'invoice' ? 'text-green-600' : 'text-blue-600' }}"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($activity['type'] === 'invoice')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            @endif
                                        </svg>
                                    </div>

                                    @if(isset($activity['status']))
                                    <div class="absolute -bottom-0.5 -right-1 rounded-tl bg-white">
                                        <div class="w-3 h-3 rounded-full
                                            {{ $activity['status'] === 'paid' ? 'bg-green-400' :
                                               ($activity['status'] === 'sent' ? 'bg-blue-400' :
                                               ($activity['status'] === 'draft' ? 'bg-gray-400' : 'bg-yellow-400')) }}">
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div>
                                        <div class="text-sm text-gray-500">
                                            <span class="font-medium text-gray-900">{{ $activity['title'] }}</span>
                                        </div>
                                        <p class="mt-0.5 text-sm text-gray-500">
                                            {{ $activity['description'] }}
                                            @if(isset($activity['value']))
                                            <span class="font-medium text-gray-900"> - {{ $activity['value'] }}</span>
                                            @endif
                                        </p>
                                        <p class="mt-0.5 text-xs text-gray-500">
                                            {{ $activity['date']->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
            @else
            <div class="py-6 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma atividade</h3>
                <p class="mt-1 text-sm text-gray-500">A empresa ainda não possui atividades registradas.</p>
            </div>
            @endif
        @else
            {{-- Se não tem empresa nem AdminActivity --}}
            <div class="py-6 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Super Administrador</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($user->is_super_admin)
                        Super administrador sem empresa associada.
                    @else
                        Usuário sem atividades recentes.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>

            <!-- Recent Invoices -->
 {{-- @if($user->invoices->count() > 0 || $user->quotes->count() > 0)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Faturas Recentes</h3>
                            <a href="{{ route('invoices.index', ['user' => $user->id]) }}"
                               class="text-sm text-blue-600 hover:text-blue-500">Ver todas</a>
                        </div>
                    </div>
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Número</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Cliente</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Data</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Valor</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($user->invoices->take(5) as $invoice)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                            <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-600 hover:text-blue-500">
                                                {{ $invoice->number }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                            {{ $invoice->client->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                            {{ $invoice->date->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                            {{ number_format($invoice->total_amount, 2) }} MT
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($invoice->status === 'paid') bg-green-100 text-green-800
                                                @elseif($invoice->status === 'sent') bg-blue-100 text-blue-800
                                                @elseif($invoice->status === 'draft') bg-gray-100 text-gray-800
                                                @elseif($invoice->status === 'overdue') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800
                                                @endif">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div> --}}

@push('scripts')
<script>
    function suspendUser(userId) {
        if (confirm('Tem certeza que deseja suspender este usuário?')) {
            fetch(`/admin/users/${userId}/suspend`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Usuário suspenso com sucesso!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message || 'Erro ao suspender usuário', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Erro ao suspender usuário', 'error');
            });
        }
    }

    function activateUser(userId) {
        if (confirm('Tem certeza que deseja ativar este usuário?')) {
            fetch(`/admin/users/${userId}/activate`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Usuário ativado com sucesso!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message || 'Erro ao ativar usuário', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Erro ao ativar usuário', 'error');
            });
        }
    }

    function resetPassword(userId) {
        if (confirm('Tem certeza que deseja redefinir a senha deste usuário? Uma nova senha será enviada por email.')) {
            fetch(`/admin/users/${userId}/reset-password`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Nova senha enviada por email!', 'success');
                } else {
                    showNotification(data.message || 'Erro ao redefinir senha', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Erro ao redefinir senha', 'error');
            });
        }
    }

    function sendWelcomeEmail(userId) {
        if (confirm('Deseja enviar um email de boas-vindas para este usuário?')) {
            fetch(`/admin/users/${userId}/send-welcome`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Email de boas-vindas enviado!', 'success');
                } else {
                    showNotification(data.message || 'Erro ao enviar email', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Erro ao enviar email', 'error');
            });
        }
    }

    function showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => notification.remove());

        const notification = document.createElement('div');
        notification.className = `notification fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;

        const colors = {
            success: 'bg-green-50 border border-green-200 text-green-800',
            error: 'bg-red-50 border border-red-200 text-red-800',
            info: 'bg-blue-50 border border-blue-200 text-blue-800',
            warning: 'bg-yellow-50 border border-yellow-200 text-yellow-800'
        };

        notification.className += ` ${colors[type]}`;
        notification.innerHTML = `
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-3">
                    <button class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none" onclick="this.closest('.notification').remove()">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.remove('translate-x-full');
            notification.classList.add('translate-x-0');
        }, 100);

        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }, 5000);
    }
</script>
@endpush
@endsection
