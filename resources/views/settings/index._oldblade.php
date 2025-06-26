@extends('layouts.app')

@section('title', 'Configurações do Sistema')

@section('header-actions')
<div class="flex items-center gap-x-4">
    <button type="button"
            class="flex items-center px-3 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
            onclick="backupSettings()">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Backup
    </button>

    <a href="{{ route('billing.dashboard') }}"
       class="flex items-center px-3 py-2 text-sm font-semibold text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Voltar ao Dashboard
    </a>
</div>
@endsection

@section('content')
<div class="mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Configurações do Sistema</h1>
    </div>

    <!-- Menu de navegação das configurações -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Configurações</h3>
            <p class="mt-1 text-sm text-gray-500">Gerencie todas as configurações do sistema</p>
        </div>
        <div class="p-6">
            <nav class="flex space-x-8" aria-label="Tabs">
                <a href="#empresa" class="px-1 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-500 whitespace-nowrap">
                    Empresa
                </a>
                <a href="#faturamento" class="px-1 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent whitespace-nowrap hover:text-gray-700 hover:border-gray-300">
                    Faturamento
                </a>
                <a href="#impostos" class="px-1 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent whitespace-nowrap hover:text-gray-700 hover:border-gray-300">
                    Impostos
                </a>
                <a href="#notificacoes" class="px-1 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent whitespace-nowrap hover:text-gray-700 hover:border-gray-300">
                    Notificações
                </a>
            </nav>
        </div>
    </div>

    <!-- Cards de Configurações Principais -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <!-- Configurações da Empresa -->
        <div class="p-6 transition-shadow duration-200 bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Empresa</p>
                    <p class="text-lg font-bold text-gray-900">{{ $settings->company_name }}</p>
                    <p class="mt-1 text-xs text-gray-500">{{ Str::limit($settings->company_address, 30) }}</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('settings.company') }}"
                   class="text-sm font-medium text-blue-600 hover:text-blue-900">
                    Gerenciar →
                </a>
            </div>
        </div>

        <!-- Configurações de Faturamento -->
        <div class="p-6 transition-shadow duration-200 bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Faturamento</p>
                    <div class="flex items-center mt-1 space-x-2">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                            {{ $settings->invoice_prefix }}
                        </span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">
                            {{ $settings->quote_prefix }}
                        </span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Taxa: {{ $settings->default_tax_rate }}%</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('settings.billing') }}"
                   class="text-sm font-medium text-blue-600 hover:text-blue-900">
                    Gerenciar →
                </a>
            </div>
        </div>

        <!-- Produtos -->
        <div class="p-6 transition-shadow duration-200 bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Produtos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ App\Models\Product::count() }}</p>
                    <p class="mt-1 text-xs text-gray-500">produtos cadastrados</p>
                </div>
            </div>
            <div class="flex mt-4 space-x-2">
                <a href="{{ route('products.index') }}"
                   class="text-sm font-medium text-blue-600 hover:text-blue-900">
                    Gerenciar →
                </a>
                <a href="{{ route('products.create') }}"
                   class="text-sm font-medium text-green-600 hover:text-green-900">
                    Novo
                </a>
            </div>
        </div>

        <!-- Serviços -->
        <div class="p-6 transition-shadow duration-200 bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
            <div class="flex items-center">
                <div class="p-2 bg-teal-100 rounded-lg">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Serviços</p>
                    <p class="text-2xl font-bold text-gray-900">{{ App\Models\Service::count() }}</p>
                    <p class="mt-1 text-xs text-gray-500">serviços cadastrados</p>
                </div>
            </div>
            <div class="flex mt-4 space-x-2">
                <a href="{{ route('services.index') }}"
                   class="text-sm font-medium text-blue-600 hover:text-blue-900">
                    Gerenciar →
                </a>
                <a href="{{ route('services.create') }}"
                   class="text-sm font-medium text-green-600 hover:text-green-900">
                    Novo
                </a>
            </div>
        </div>
    </div>

    <!-- Configurações Detalhadas -->
    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Configurações Rápidas</h3>
            <p class="mt-1 text-sm text-gray-500">Visualize e acesse rapidamente as principais configurações</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Configuração</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Valor Atual</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                            Próximo número de fatura
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                                {{ $settings->invoice_prefix }}{{ str_pad($settings->next_invoice_number, 6, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                Configurado
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                            <a href="{{ route('settings.billing') }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                            Próximo número de orçamento
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">
                                {{ $settings->quote_prefix }}{{ str_pad($settings->next_quote_number, 6, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                Configurado
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                            <a href="{{ route('settings.billing') }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                            Taxa padrão de IVA
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            {{ $settings->default_tax_rate }}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                Ativo
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                            <a href="{{ route('settings.taxes') }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                            E-mails automáticos
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            {{ $settings->send_invoice_emails ? 'Ativado' : 'Desativado' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $settings->send_invoice_emails ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $settings->send_invoice_emails ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                            <a href="{{ route('settings.notifications') }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                            Moeda padrão
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold text-teal-800 bg-teal-100 rounded-full">
                                {{ $settings->currency ?? 'MZN' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                Configurado
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                            <a href="{{ route('settings.billing') }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Informações do Sistema -->
    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Informações do Sistema</h3>
            <p class="mt-1 text-sm text-gray-500">Status geral e estatísticas do sistema</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="p-4 text-center rounded-lg bg-gray-50">
                    <div class="p-2 mx-auto mb-3 bg-blue-100 rounded-lg w-fit">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                        </svg>
                    </div>
                    <h6 class="mb-1 font-semibold text-gray-900">Versão do Sistema</h6>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                        v1.0.0
                    </span>
                </div>
                <div class="p-4 text-center rounded-lg bg-gray-50">
                    <div class="p-2 mx-auto mb-3 bg-green-100 rounded-lg w-fit">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 1v1a2 2 0 002 2h4a2 2 0 002-2V8M5 21h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h6 class="mb-1 font-semibold text-gray-900">Última Atualização</h6>
                    <div class="text-sm text-gray-600">{{ $settings->updated_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="p-4 text-center rounded-lg bg-gray-50">
                    <div class="p-2 mx-auto mb-3 bg-teal-100 rounded-lg w-fit">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                        </svg>
                    </div>
                    <h6 class="mb-1 font-semibold text-gray-900">Total de Registros</h6>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold text-teal-800 bg-teal-100 rounded-full">
                        {{ App\Models\Invoice::count() + App\Models\Quote::count() + App\Models\Client::count() }}
                    </span>
                </div>
                <div class="p-4 text-center rounded-lg bg-gray-50">
                    <div class="p-2 mx-auto mb-3 bg-yellow-100 rounded-lg w-fit">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h6 class="mb-1 font-semibold text-gray-900">Status do Sistema</h6>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                        Ativo
                    </span>
                </div>
            </div>

            <div class="flex flex-col gap-4 mt-8 sm:flex-row">
                <button type="button"
                        class="inline-flex items-center justify-center flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        onclick="resetSettings()">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Restaurar Padrões
                </button>
                <a href="{{ route('billing.reports') }}"
                   class="inline-flex items-center justify-center flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Ver Relatórios
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function backupSettings() {
    if (confirm('Deseja fazer backup das configurações atuais?')) {
        window.location.href = '{{ route("settings.backup") }}';
    }
}

function resetSettings() {
    if (confirm('ATENÇÃO: Esta ação irá restaurar todas as configurações para o padrão. Deseja continuar?')) {
        fetch('{{ route("settings.reset") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Configurações resetadas com sucesso!');
                location.reload();
            } else {
                alert('Erro ao resetar configurações.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao resetar configurações.');
        });
    }
}
</script>
@endpush
@endsection