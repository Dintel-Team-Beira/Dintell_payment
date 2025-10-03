@extends('layouts.app')

@section('title', 'Novo Cliente')

@section('content')
<div class="mx-auto max-w-8xl">

    @if ($excededUsage)
                    <div class="container mx-auto">
                <!-- Aviso de Limite Atingido -->
                <div class="max-w-7xl mx-auto my-8">
                    <div class="overflow-hidden bg-white border border-orange-200 shadow-lg rounded-xl">

                        <!-- Header com ícone -->
                        <div class="px-6 pt-6 pb-4 bg-gradient-to-r from-orange-50 to-red-50">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-16 h-16 mr-4 bg-orange-100 rounded-full">
                                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h2 class="text-2xl font-bold text-orange-900">
                                        Limite de Clientes Atingido
                                    </h2>
                                    <p class="mt-1 text-sm text-orange-700">
                                        Você atingiu o limite do seu plano atual
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Conteúdo -->
                        <div class="px-6 py-6 space-y-6">

                            <!-- Mensagem principal -->
                            <div class="flex items-start p-4 border-l-4 border-orange-500 rounded-r-lg bg-orange-50">
                                <svg class="w-6 h-6 mr-3 text-orange-600 flex-shrink-0 mt-0.5" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-orange-900">
                                        Não é possível criar novos clientes no momento
                                    </p>
                                    <p class="mt-1 text-sm text-orange-800">
                                        Você alcançou o número máximo de clientes permitido no seu plano atual.
                                        Para continuar criando clientes, faça upgrade para um plano superior.
                                    </p>
                                </div>
                            </div>

                            <!-- Informações do uso atual -->
                            @if (isset($company) && $company->plan)
                                @php
                                    $clientUsage = $company->getClientUsage();
                                @endphp
                                <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                                    <h4 class="mb-3 text-sm font-semibold text-gray-900">Uso Atual:</h4>

                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-700">Clientes atingidos</span>
                                            <span class="font-bold text-orange-600">
                                                {{ $clientUsage['current'] }}/{{ $clientUsage['max'] }}
                                            </span>
                                        </div>

                                        <!-- Barra de progresso -->
                                        <div class="w-full h-3 overflow-hidden bg-gray-200 rounded-full">
                                            <div class="h-3 transition-all duration-500 bg-gradient-to-r from-orange-500 to-red-500"
                                                style="width: 100%"></div>
                                        </div>

                                        <p class="text-xs text-orange-600">
                                            ⚠️ Limite máximo atingido
                                        </p>
                                    </div>

                                    <!-- Informação do plano -->
                                    <div class="flex items-center justify-between pt-3 mt-3 border-t border-gray-300">
                                        <div>
                                            <p class="text-xs text-gray-600">Plano Atual</p>
                                            <p class="font-semibold text-gray-900">{{ $company->subscriptions()->latest()->first()->plan->name }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-600">Renovação</p>
                                            <p class="font-semibold text-gray-900">
                                                {{ \Carbon\Carbon::parse($company->subscriptions()->latest()->first()->next_payment_due)->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Benefícios do upgrade -->
                            <div class="p-4 border border-blue-200 rounded-lg bg-blue-50">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                        <path fill-rule="evenodd"
                                            d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-blue-900">
                                            Benefícios do Upgrade:
                                        </p>
                                        <ul class="mt-2 space-y-1 text-sm text-blue-800">
                                            <li class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Mais clientes por mês
                                            </li>
                                            <li class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Recursos avançados
                                            </li>
                                            <li class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Suporte prioritário
                                            </li>
                                            <li class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Sem interrupções
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Footer com botões de ação -->
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                            <div
                                class="flex flex-col items-center justify-between space-y-3 sm:flex-row sm:space-y-0 sm:space-x-4">

                                <!-- Botão voltar -->
                                <a href="{{ route('clients.index') }}"
                                    class="inline-flex items-center justify-center w-full px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 sm:w-auto">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                    Voltar aos Clientes
                                </a>

                                <!-- Botões de ação principais -->
                                <div
                                    class="flex flex-col w-full space-y-2 sm:flex-row sm:space-y-0 sm:space-x-3 sm:w-auto">
                                    <a href="#"
                                        class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-blue-700 border border-blue-200 rounded-md bg-blue-50 hover:bg-blue-100">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Ver Planos
                                    </a>

                                    <a href="#"
                                        class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-md hover:from-blue-700 hover:to-blue-800 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                        </svg>
                                        Fazer Upgrade Agora
                                    </a>
                                </div>
                            </div>

                            <!-- Nota de suporte -->
                            <div class="flex items-center justify-center mt-4 text-xs text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Precisa de ajuda? Entre em contato com nosso suporte
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Estilos adicionais -->
            <style>
                @keyframes slideDown {
                    from {
                        opacity: 0;
                        transform: translateY(-20px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .container>div {
                    animation: slideDown 0.4s ease-out;
                }
            </style>
    @else
        <form action="{{ route('clients.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="p-6 bg-white rounded-lg shadow-sm ring-1 ring-gray-900/5">
            <h2 class="mb-6 text-lg font-semibold">Informações do Cliente</h2>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Nome *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('name')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('email')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Telefone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('phone')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Empresa</label>
                    <input type="text" name="company" value="{{ old('company') }}"
                           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('company')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">NUIT</label>
                    <input type="text" name="tax_number" value="{{ old('tax_number') }}"
                           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('tax_number')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Endereço</label>
                    <textarea name="address" rows="3" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('address') }}</textarea>
                    @error('address')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('clients.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                Criar Cliente
            </button>
        </div>
    </form>
    @endif
    
</div>
@endsection
