@extends('layouts.app')

@section('title', 'Planos de Subscrição')

@section('header-actions')
<a href="{{ route('plans.create') }}"
   class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
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
            <div class="bg-blue-500 text-white text-center py-1 text-xs font-medium">
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

                <p class="text-gray-600 text-sm mb-4">{{ $plan->description }}</p>

                <ul class="space-y-2 mb-6">
                    @foreach($plan->features as $feature)
                    <li class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $feature }}
                    </li>
                    @endforeach
                </ul>

                <div class="grid grid-cols-2 gap-4 text-sm text-gray-500 mb-4">
                    <div>
                        <span class="font-medium">Domínios:</span> {{ $plan->max_domains }}
                    </div>
                    <div>
                        <span class="font-medium">Storage:</span> {{ $plan->max_storage_gb }}GB
                    </div>
                    <div>
                        <span class="font-medium">Bandwidth:</span> {{ $plan->max_bandwidth_gb }}GB
                    </div>
                    <div>
                        <span class="font-medium">Trial:</span> {{ $plan->trial_days }}d
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">{{ $plan->subscriptions_count ?? 0 }} subscrições</span>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('plans.edit', $plan) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            Editar
                        </a>
                        <form method="POST" action="{{ route('plans.toggle', $plan) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-{{ $plan->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $plan->is_active ? 'yellow' : 'green' }}-800 text-sm">
                                {{ $plan->is_active ? 'Desativar' : 'Ativar' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($plans->isEmpty())
    <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum plano encontrado</h3>
        <p class="mt-1 text-sm text-gray-500">Comece criando seu primeiro plano de subscrição.</p>
        <div class="mt-6">
            <a href="{{ route('plans.create') }}"
               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                Novo Plano
            </a>
        </div>
    </div>
    @endif
</div>
@endsection