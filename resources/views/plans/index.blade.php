@extends('layouts.app')

@section('title', 'Planos de Subscrição')

@section('header-actions')
<a href="{{ route('plans.create') }}"
   class="px-3 py-2 text-sm font-semibold text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-500">
    Novo Plano
</a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Plans Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($plans as $plan)
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg overflow-hidden {{ $plan->is_featured ? 'ring-2 ring-blue-500' : '' }}">
            @if($plan->is_featured)
            <div class="py-1 text-xs font-medium text-center text-white bg-blue-500">
                MAIS POPULAR
            </div>
            @endif

            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $plan->name }}</h3>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                        {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $plan->is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                </div>

                <div class="mb-4">
                    <div class="flex items-baseline">
                        <span class="text-3xl font-bold text-gray-900">MT {{ number_format($plan->price, 0) }}</span>
                        <span class="ml-1 text-sm text-gray-500">/{{ $plan->billing_cycle === 'monthly' ? 'mês' : $plan->billing_cycle }}</span>
                    </div>
                    @if($plan->setup_fee > 0)
                        <p class="text-sm text-gray-500">+ MT {{ number_format($plan->setup_fee, 2) }} taxa de instalação</p>
                    @endif
                </div>

                <p class="mb-4 text-sm text-gray-600">{{ $plan->description }}</p>

                <ul class="mb-6 space-y-2">
                    @foreach($plan->features as $feature)
                    <li class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $feature }}
                    </li>
                    @endforeach
                </ul>

            <!-- Versão melhorada da seção -->
<div class="p-4 mb-4 rounded-lg bg-gradient-to-r from-gray-50 to-gray-100">
    <div class="grid grid-cols-2 gap-6 mb-4 text-sm">
        <div class="flex items-center space-x-2">
            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
            <span class="text-gray-600">Domínios:</span>
            <span class="font-semibold text-gray-900">{{ $plan->max_domains }}</span>
        </div>
        <div class="flex items-center space-x-2">
            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
            <span class="text-gray-600">Storage:</span>
            <span class="font-semibold text-gray-900">{{ $plan->max_storage_gb }}GB</span>
        </div>
        <div class="flex items-center space-x-2">
            <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
            <span class="text-gray-600">Bandwidth:</span>
            <span class="font-semibold text-gray-900">{{ $plan->max_bandwidth_gb }}GB</span>
        </div>
        <div class="flex items-center space-x-2">
            <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
            <span class="text-gray-600">Trial:</span>
            <span class="font-semibold text-gray-900">{{ $plan->trial_days }}d</span>
        </div>
    </div>
</div>

<div class="flex items-center justify-between pt-2 border-t border-gray-200">
    <div class="flex items-center space-x-2">
        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-sm font-medium text-gray-600">{{ $plan->subscriptions_count ?? 0 }} subscrições</span>
    </div>

    <div class="flex items-center space-x-3">
        <a href="{{ route('plans.edit', $plan) }}"
           class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-100 transition-colors duration-200">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Editar
        </a>

        <form method="POST" action="{{ route('plans.toggle', $plan) }}" class="inline">
            @csrf
            <button type="submit"
                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg transition-colors duration-200
                           {{ $plan->is_active
                              ? 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100'
                              : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                @if($plan->is_active)
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Desativar
                @else
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Ativar
                @endif
            </button>
        </form>
    </div>
</div>
            </div>
        </div>
        @endforeach
    </div>

    @if($plans->isEmpty())
    <div class="py-12 text-center">
        <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum plano encontrado</h3>
        <p class="mt-1 text-sm text-gray-500">Comece criando seu primeiro plano de subscrição.</p>
        <div class="mt-6">
            <a href="{{ route('plans.create') }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700">
                Novo Plano
            </a>
        </div>
    </div>
    @endif
</div>
@endsection