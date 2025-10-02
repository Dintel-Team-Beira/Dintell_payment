@extends('layouts.admin')

@section('title', 'Detalhes da Subscrição')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="mx-5 bg-white rounded-md shadow">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.subscriptions.index') }}"
                       class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Detalhes da Subscrição</h1>
                        <p class="mt-1 text-sm text-gray-600">ID: #{{ $subscription->id }}</p>
                    </div>
                </div>
                <div>
                    @php
                        $statusColors = [
                            'active' => 'bg-green-100 text-green-800',
                            'trialing' => 'bg-blue-100 text-blue-800',
                            'canceled' => 'bg-gray-100 text-gray-800',
                            'suspended' => 'bg-red-100 text-red-800',
                            'expired' => 'bg-orange-100 text-orange-800',
                            'past_due' => 'bg-yellow-100 text-yellow-800',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full {{ $statusColors[$subscription->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $subscription->getStatusLabel() }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-8">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Coluna Principal -->
            <div class="space-y-8 lg:col-span-2">
                
                <!-- Informações da Empresa -->
                <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Empresa</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                @if($subscription->company->logo)
                                    <img class="w-16 h-16 rounded-lg" src="{{ Storage::url($subscription->company->logo) }}" alt="">
                                @else
                                    <div class="flex items-center justify-center w-16 h-16 text-xl font-bold text-white bg-blue-600 rounded-lg">
                                        {{ strtoupper(substr($subscription->company->name, 0, 2)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900">{{ $subscription->company->name }}</h4>
                                <div class="mt-2 space-y-1 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $subscription->company->email }}
                                    </div>
                                    @if($subscription->company->phone)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                            {{ $subscription->company->phone }}
                                        </div>
                                    @endif
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        Status: <span class="font-medium">{{ ucfirst($subscription->company->status) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <a href="#" class="text-sm text-blue-600 hover:text-blue-700">
                                    Ver Empresa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detalhes do Plano -->
                <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Plano de Subscrição</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4">
                                <div class="flex items-center justify-center w-12 h-12 rounded-lg" style="background-color: {{ $subscription->plan->color }}20;">
                                    <svg class="w-7 h-7" style="color: {{ $subscription->plan->color }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-xl font-bold text-gray-900">{{ $subscription->plan->name }}</h4>
                                    <p class="mt-1 text-sm text-gray-600">{{ $subscription->plan->description }}</p>
                                    
                                    <!-- Limites do Plano -->
                                    <div class="grid grid-cols-2 gap-4 mt-4">
                                        <div class="p-3 border border-gray-200 rounded-lg bg-gray-50">
                                            <div class="text-xs text-gray-500">Usuários</div>
                                            <div class="mt-1 text-lg font-semibold text-gray-900">
                                                {{ $subscription->plan->max_users ?? 'Ilimitado' }}
                                            </div>
                                        </div>
                                        <div class="p-3 border border-gray-200 rounded-lg bg-gray-50">
                                            <div class="text-xs text-gray-500">Faturas/mês</div>
                                            <div class="mt-1 text-lg font-semibold text-gray-900">
                                                {{ $subscription->plan->max_invoices_per_month ?? 'Ilimitado' }}
                                            </div>
                                        </div>
                                        <div class="p-3 border border-gray-200 rounded-lg bg-gray-50">
                                            <div class="text-xs text-gray-500">Clientes</div>
                                            <div class="mt-1 text-lg font-semibold text-gray-900">
                                                {{ $subscription->plan->max_clients ?? 'Ilimitado' }}
                                            </div>
                                        </div>
                                        <div class="p-3 border border-gray-200 rounded-lg bg-gray-50">
                                            <div class="text-xs text-gray-500">Armazenamento</div>
                                            <div class="mt-1 text-lg font-semibold text-gray-900">
                                                {{ $subscription->plan->max_storage_mb ? $subscription->plan->max_storage_mb . ' MB' : 'Ilimitado' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold text-gray-900">
                                    {{ number_format($subscription->amount, 2) }} MT
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ ucfirst($subscription->billing_cycle) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Datas e Período -->
                <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Período e Datas</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Data de Início -->
                            <div>
                                <div class="flex items-center text-sm font-medium text-gray-500">
                                    <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Iniciou em
                                </div>
                                <div class="mt-2 text-lg font-semibold text-gray-900">
                                    {{ $subscription->starts_at->format('d/m/Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $subscription->starts_at->diffForHumans() }}
                                </div>
                            </div>

                            <!-- Data de Término -->
                            <div>
                                <div class="flex items-center text-sm font-medium text-gray-500">
                                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    Termina em
                                </div>
                                <div class="mt-2 text-lg font-semibold text-gray-900">
                                    {{ $subscription->ends_at->format('d/m/Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    @if($subscription->ends_at->isFuture())
                                        Faltam {{ abs($subscription->daysUntilExpiration()) }} dias
                                    @else
                                        Expirou há {{ abs($subscription->daysUntilExpiration()) }} dias
                                    @endif
                                </div>
                            </div>

                            @if($subscription->trial_ends_at)
                            <!-- Trial -->
                            <div>
                                <div class="flex items-center text-sm font-medium text-gray-500">
                                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Trial termina em
                                </div>
                                <div class="mt-2 text-lg font-semibold text-gray-900">
                                    {{ $subscription->trial_ends_at->format('d/m/Y') }}
                                </div>
                            </div>
                            @endif

                            @if($subscription->next_payment_due)
                            <!-- Próximo Pagamento -->
                            <div>
                                <div class="flex items-center text-sm font-medium text-gray-500">
                                    <svg class="w-5 h-5 mr-2 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                    </svg>
                                    Próximo Pagamento
                                </div>
                                <div class="mt-2 text-lg font-semibold text-gray-900">
                                    {{ $subscription->next_payment_due->format('d/m/Y') }}
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Barra de Progresso -->
                        @if($subscription->ends_at->isFuture())
                        <div class="mt-6">
                            @php
                                $totalDays = $subscription->starts_at->diffInDays($subscription->ends_at);
                                $daysElapsed = $subscription->starts_at->diffInDays(now());
                                $progress = $totalDays > 0 ? min(100, ($daysElapsed / $totalDays) * 100) : 0;
                            @endphp
                            <div class="flex justify-between mb-2 text-sm text-gray-600">
                                <span>Progresso do período</span>
                                <span>{{ number_format($progress, 0) }}%</span>
                            </div>
                            <div class="w-full h-2 overflow-hidden bg-gray-200 rounded-full">
                                <div class="h-2 transition-all duration-500 bg-blue-600 rounded-full" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Timeline / Histórico -->
                <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Histórico e Atividades</h3>
                    </div>
                    <div class="p-6">
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <!-- Criação -->
                                <li>
                                    <div class="relative pb-8">
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="flex items-center justify-center w-8 h-8 bg-blue-500 rounded-full ring-8 ring-white">
                                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="flex justify-between flex-1 min-w-0 space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-900">
                                                        Subscrição <span class="font-medium">criada</span>
                                                    </p>
                                                    @if($subscription->createdByUser)
                                                    <p class="text-sm text-gray-500">
                                                        por {{ $subscription->createdByUser->name }}
                                                    </p>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-right text-gray-500 whitespace-nowrap">
                                                    {{ $subscription->created_at->format('d/m/Y H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                @if($subscription->last_payment_at)
                                <!-- Último Pagamento -->
                                <li>
                                    <div class="relative pb-8">
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="flex items-center justify-center w-8 h-8 bg-green-500 rounded-full ring-8 ring-white">
                                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="flex justify-between flex-1 min-w-0 space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-900">
                                                        <span class="font-medium">Pagamento recebido</span>
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        {{ number_format($subscription->amount, 2) }} MT
                                                        @if($subscription->payment_method)
                                                            via {{ ucfirst($subscription->payment_method) }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="text-sm text-right text-gray-500 whitespace-nowrap">
                                                    {{ $subscription->last_payment_at->format('d/m/Y H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif

                                @if($subscription->renewal_count > 0)
                                <!-- Renovações -->
                                <li>
                                    <div class="relative pb-8">
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-500 ring-8 ring-white">
                                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="flex justify-between flex-1 min-w-0 space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-900">
                                                        Renovada <span class="font-medium">{{ $subscription->renewal_count }}x</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif

                                @if($subscription->suspended_at)
                                <!-- Suspensão -->
                                <li>
                                    <div class="relative pb-8">
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="flex items-center justify-center w-8 h-8 bg-red-500 rounded-full ring-8 ring-white">
                                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="flex justify-between flex-1 min-w-0 space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-900">
                                                        <span class="font-medium">Suspensa</span>
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        Motivo: {{ ucfirst(str_replace('_', ' ', $subscription->suspension_reason)) }}
                                                    </p>
                                                    @if($subscription->suspendedByUser)
                                                    <p class="text-sm text-gray-500">
                                                        por {{ $subscription->suspendedByUser->name }}
                                                    </p>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-right text-gray-500 whitespace-nowrap">
                                                    {{ $subscription->suspended_at->format('d/m/Y H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif

                                @if($subscription->reactivated_at)
                                <!-- Reativação -->
                                <li>
                                    <div class="relative pb-8">
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="flex items-center justify-center w-8 h-8 bg-green-500 rounded-full ring-8 ring-white">
                                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="flex justify-between flex-1 min-w-0 space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-900">
                                                        <span class="font-medium">Reativada</span>
                                                    </p>
                                                    @if($subscription->reactivatedByUser)
                                                    <p class="text-sm text-gray-500">
                                                        por {{ $subscription->reactivatedByUser->name }}
                                                    </p>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-right text-gray-500 whitespace-nowrap">
                                                    {{ $subscription->reactivated_at->format('d/m/Y H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif

                                @if($subscription->canceled_at)
                                <!-- Cancelamento -->
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="flex items-center justify-center w-8 h-8 bg-gray-500 rounded-full ring-8 ring-white">
                                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="flex justify-between flex-1 min-w-0 space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-900">
                                                        <span class="font-medium">Cancelada</span>
                                                    </p>
                                                    @if($subscription->canceled_reason)
                                                    <p class="text-sm text-gray-500">
                                                        Motivo: {{ $subscription->canceled_reason }}
                                                    </p>
                                                    @endif
                                                    @if($subscription->canceledByUser)
                                                    <p class="text-sm text-gray-500">
                                                        por {{ $subscription->canceledByUser->name }}
                                                    </p>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-right text-gray-500 whitespace-nowrap">
                                                    {{ $subscription->canceled_at->format('d/m/Y H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif
                            </ul>
                        </div>

                        <!-- Histórico de Subscrições Anteriores -->
                        @if($history->count() > 0)
                        <div class="pt-6 mt-6 border-t border-gray-200">
                            <h4 class="mb-4 text-sm font-semibold text-gray-900">Subscrições Anteriores</h4>
                            <div class="space-y-3">
                                @foreach($history as $oldSub)
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg bg-gray-50">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex items-center justify-center w-8 h-8 rounded" style="background-color: {{ $oldSub->plan->color }}20;">
                                            <svg class="w-5 h-5" style="color: {{ $oldSub->plan->color }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $oldSub->plan->name }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $oldSub->starts_at->format('d/m/Y') }} - {{ $oldSub->ends_at->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$oldSub->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $oldSub->getStatusLabel() }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Notas e Observações -->
                @if($subscription->notes || $subscription->suspension_details)
                <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Notas e Observações</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @if($subscription->notes)
                        <div>
                            <h4 class="mb-2 text-sm font-semibold text-gray-700">Notas Gerais</h4>
                            <div class="p-4 text-sm text-gray-700 border border-gray-200 rounded-lg bg-gray-50">
                                {!! nl2br(e($subscription->notes)) !!}
                            </div>
                        </div>
                        @endif

                        @if($subscription->suspension_details)
                        <div>
                            <h4 class="mb-2 text-sm font-semibold text-gray-700">Detalhes da Suspensão</h4>
                            <div class="p-4 text-sm text-gray-700 border border-red-200 rounded-lg bg-red-50">
                                {!! nl2br(e($subscription->suspension_details)) !!}
                            </div>
                        </div>
                        @endif

                        @if($subscription->suspension_message)
                        <div>
                            <h4 class="mb-2 text-sm font-semibold text-gray-700">Mensagem ao Cliente</h4>
                            <div class="p-4 text-sm text-gray-700 border border-yellow-200 rounded-lg bg-yellow-50">
                                {!! nl2br(e($subscription->suspension_message)) !!}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

            </div>

            <!-- Sidebar - Ações e Info Rápida -->
            <div class="space-y-6">
                
                <!-- Card de Resumo -->
                <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Resumo Rápido</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <div class="text-sm text-gray-500">Valor Total</div>
                            <div class="text-2xl font-bold text-gray-900">
                                {{ number_format($subscription->getFinalAmount(), 2) }} MT
                            </div>
                            @if($subscription->discount_amount || $subscription->discount_percentage)
                            <div class="flex items-center mt-1 text-sm text-green-600">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                                </svg>
                                Desconto aplicado
                            </div>
                            @endif
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between mb-2 text-sm">
                                <span class="text-gray-500">Auto-renovação</span>
                                <span class="font-medium {{ $subscription->auto_renew ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $subscription->auto_renew ? 'Ativa' : 'Inativa' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between mb-2 text-sm">
                                <span class="text-gray-500">Renovações</span>
                                <span class="font-medium text-gray-900">{{ $subscription->renewal_count }}x</span>
                            </div>
                            @if($subscription->suspension_count > 0)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Suspensões</span>
                                <span class="font-medium text-red-600">{{ $subscription->suspension_count }}x</span>
                            </div>
                            @endif
                        </div>

                        @if($subscription->coupon_code)
                        <div class="p-3 border border-green-200 rounded-lg bg-green-50">
                            <div class="text-xs text-green-600">Cupom Aplicado</div>
                            <div class="mt-1 font-mono text-sm font-semibold text-green-800">
                                {{ $subscription->coupon_code }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Ações Rápidas -->
                <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Ações</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @if($subscription->isActive() || $subscription->isTrialing())
                            <!-- Renovar -->
                            <form action="{{ route('admin.subscriptions.renew', $subscription) }}" method="POST" onsubmit="return confirm('Deseja renovar esta subscrição?')">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                                    </svg>
                                    Renovar Agora
                                </button>
                            </form>

                            <!-- Toggle Auto-Renew -->
                            <button onclick="toggleAutoRenew()" class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                                </svg>
                                {{ $subscription->auto_renew ? 'Desativar' : 'Ativar' }} Auto-renovação
                            </button>

                            <!-- Suspender -->
                            <button onclick="openSuspendModal()" class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-orange-700 bg-orange-100 border border-orange-200 rounded-md hover:bg-orange-200">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                                </svg>
                                Suspender
                            </button>

                            <!-- Cancelar -->
                            <button onclick="openCancelModal()" class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-red-700 bg-red-100 border border-red-200 rounded-md hover:bg-red-200">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                                Cancelar
                            </button>
                        @endif

                        @if($subscription->isSuspended())
                            <!-- Reativar -->
                            <button onclick="reactivateSubscription()" class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Reativar Subscrição
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Alerta de Status -->
                @if($subscription->isSuspended())
                <div class="p-4 border-l-4 border-red-500 rounded-lg bg-red-50">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold text-red-800">Subscrição Suspensa</h3>
                            <p class="mt-1 text-sm text-red-700">
                                Esta empresa não pode acessar o sistema enquanto a subscrição estiver suspensa.
                            </p>
                        </div>
                    </div>
                </div>
                @elseif($subscription->isExpiringIn(7))
                <div class="p-4 border-l-4 border-yellow-500 rounded-lg bg-yellow-50">
                    <div class="flex">
                        <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold text-yellow-800">Expirando em Breve</h3>
                            <p class="mt-1 text-sm text-yellow-700">
                                Faltam apenas {{ abs($subscription->daysUntilExpiration()) }} dias para expirar.
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modais (usar os mesmos da index.blade.php) -->
<!-- ... incluir modais de cancel e suspend aqui ... -->

@push('scripts')
<script>
function toggleAutoRenew() {
    fetch(`/admin/subscriptions/{{ $subscription->id }}/toggle-auto-renew`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        }
    })
    .catch(error => showNotification('Erro ao alterar renovação automática', 'error'));
}

function openSuspendModal() {
    window.location.href = '{{ route("admin.subscriptions.index") }}?suspend={{ $subscription->id }}';
}

function openCancelModal() {
    window.location.href = '{{ route("admin.subscriptions.index") }}?cancel={{ $subscription->id }}';
}

function reactivateSubscription() {
    if (!confirm('Deseja reativar esta subscrição?')) return;
    
    fetch(`/admin/subscriptions/{{ $subscription->id }}/reactivate`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Subscrição reativada com sucesso!', 'success');
            setTimeout(() => window.location.reload(), 1000);
        }
    })
    .catch(error => showNotification('Erro ao reativar subscrição', 'error'));
}

function showNotification(message, type = 'info') {
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(n => n.remove());

    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg`;

    const colors = {
        success: 'bg-green-50 border border-green-200 text-green-800',
        error: 'bg-red-50 border border-red-200 text-red-800',
        info: 'bg-blue-50 border border-blue-200 text-blue-800',
    };

    notification.className += ` ${colors[type]}`;
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-1"><p class="text-sm font-medium">${message}</p></div>
            <button class="ml-3 text-gray-400 hover:text-gray-600" onclick="this.closest('.notification').remove()">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    `;

    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 5000);
}
</script>
@endpush
@endsection