@extends('layouts.app')

@section('title', 'Editar Subscrição')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Subscrição</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $subscription->domain }} - {{ $subscription->client->name }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                    {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' :
                       ($subscription->status === 'suspended' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                    {{ ucfirst($subscription->status) }}
                </span>
                <a href="{{ route('subscriptions.show', $subscription) }}"
                   class="text-blue-600 hover:text-blue-800">Ver Detalhes</a>
            </div>
        </div>
    </div>

    <form action="{{ route('subscriptions.update', $subscription) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Informações Básicas</h2>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cliente *</label>
                    <select name="client_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id', $subscription->client_id) == $client->id ? 'selected' : '' }}>
                                {{ $client->name }} ({{ $client->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('client_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Plano *</label>
                    <select name="subscription_plan_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('subscription_plan_id', $subscription->subscription_plan_id) == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} - MT {{ number_format($plan->price, 2) }}
                            </option>
                        @endforeach
                    </select>
                    @error('subscription_plan_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Domínio *</label>
                    <input type="text" name="domain" value="{{ old('domain', $subscription->domain) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('domain')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Subdomínio</label>
                    <input type="text" name="subdomain" value="{{ old('subdomain', $subscription->subdomain) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('subdomain')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Status and Control -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Status e Controle</h2>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status *</label>
                    <select name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="active" {{ old('status', $subscription->status) === 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="inactive" {{ old('status', $subscription->status) === 'inactive' ? 'selected' : '' }}>Inativo</option>
                        <option value="suspended" {{ old('status', $subscription->status) === 'suspended' ? 'selected' : '' }}>Suspenso</option>
                        <option value="cancelled" {{ old('status', $subscription->status) === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                        <option value="expired" {{ old('status', $subscription->status) === 'expired' ? 'selected' : '' }}>Expirado</option>
                        <option value="trial" {{ old('status', $subscription->status) === 'trial' ? 'selected' : '' }}>Trial</option>
                    </select>
                    @error('status')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Controle Manual *</label>
                    <select name="manual_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="enabled" {{ old('manual_status', $subscription->manual_status) === 'enabled' ? 'selected' : '' }}>Habilitado</option>
                        <option value="disabled" {{ old('manual_status', $subscription->manual_status) === 'disabled' ? 'selected' : '' }}>Desabilitado</option>
                    </select>
                    @error('manual_status')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            @if($subscription->status === 'suspended' || old('status') === 'suspended')
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Motivo da Suspensão</label>
                <textarea name="suspension_reason" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Descreva o motivo da suspensão...">{{ old('suspension_reason', $subscription->suspension_reason) }}</textarea>
                @error('suspension_reason')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            @endif
        </div>

        <!-- Dates -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Datas</h2>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Data de Início *</label>
                    <input type="datetime-local" name="starts_at"
                           value="{{ old('starts_at', $subscription->starts_at ? $subscription->starts_at->format('Y-m-d\TH:i') : '') }}"
                           required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('starts_at')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Data de Expiração</label>
                    <input type="datetime-local" name="ends_at"
                           value="{{ old('ends_at', $subscription->ends_at ? $subscription->ends_at->format('Y-m-d\TH:i') : '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('ends_at')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Trial até</label>
                    <input type="datetime-local" name="trial_ends_at"
                           value="{{ old('trial_ends_at', $subscription->trial_ends_at ? $subscription->trial_ends_at->format('Y-m-d\TH:i') : '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('trial_ends_at')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Informações de Pagamento</h2>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Valor Pago *</label>
                    <input type="number" step="0.01" name="amount_paid"
                           value="{{ old('amount_paid', $subscription->amount_paid) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('amount_paid')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Método de Pagamento</label>
                    <select name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Selecionar método</option>
                        <option value="mpesa" {{ old('payment_method', $subscription->payment_method) === 'mpesa' ? 'selected' : '' }}>MPesa</option>
                        <option value="visa" {{ old('payment_method', $subscription->payment_method) === 'visa' ? 'selected' : '' }}>Visa</option>
                        <option value="bank_transfer" {{ old('payment_method', $subscription->payment_method) === 'bank_transfer' ? 'selected' : '' }}>Transferência</option>
                        <option value="cash" {{ old('payment_method', $subscription->payment_method) === 'cash' ? 'selected' : '' }}>Dinheiro</option>
                    </select>
                    @error('payment_method')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Referência do Pagamento</label>
                    <input type="text" name="payment_reference"
                           value="{{ old('payment_reference', $subscription->payment_reference) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('payment_reference')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Falhas de Pagamento</label>
                    <input type="number" name="payment_failures" min="0"
                           value="{{ old('payment_failures', $subscription->payment_failures) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('payment_failures')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Configurações</h2>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Dias de Aviso de Expiração</label>
                    <input type="number" name="expiry_warning_days" min="1" max="30"
                           value="{{ old('expiry_warning_days', $subscription->expiry_warning_days) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('expiry_warning_days')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="auto_renew" value="1"
                               {{ old('auto_renew', $subscription->auto_renew) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label class="ml-2 block text-sm text-gray-900">Renovação automática</label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="email_notifications" value="1"
                               {{ old('email_notifications', $subscription->email_notifications) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label class="ml-2 block text-sm text-gray-900">Notificações por email</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usage Statistics (Read-only) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Estatísticas de Uso</h2>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm font-medium text-gray-500">Total de Requests</div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($subscription->total_requests) }}</div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm font-medium text-gray-500">Requests Mensais</div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($subscription->monthly_requests) }}</div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm font-medium text-gray-500">Storage Usado</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $subscription->storage_used_gb }}GB</div>
                    <div class="text-xs text-gray-500">de {{ $subscription->plan->max_storage_gb }}GB</div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm font-medium text-gray-500">Bandwidth Usado</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $subscription->bandwidth_used_gb }}GB</div>
                    <div class="text-xs text-gray-500">de {{ $subscription->plan->max_bandwidth_gb }}GB</div>
                </div>
            </div>

            <div class="mt-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Uso Geral</span>
                    <span class="font-medium">{{ number_format($subscription->usage_percentage, 1) }}%</span>
                </div>
                <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min($subscription->usage_percentage, 100) }}%"></div>
                </div>
            </div>
        </div>

        <!-- API Key (Read-only) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Chave da API</h2>

            <div class="flex items-center space-x-4">
                <div class="flex-1">
                    <code class="block w-full p-3 bg-gray-50 border border-gray-200 rounded-md text-sm font-mono">
                        {{ $subscription->api_key }}
                    </code>
                </div>

                <div class="flex-shrink-0">
                    <button type="button" onclick="copyApiKey()"
                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Copiar
                    </button>
                </div>

                <div class="flex-shrink-0">
                    <form method="POST" action="{{ route('subscriptions.regenerate-key', $subscription) }}" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Regenerar chave API? Isso pode quebrar integrações existentes.')"
                                class="px-3 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                            Regenerar
                        </button>
                    </form>
                </div>
            </div>

            <p class="mt-2 text-sm text-gray-500">
                Esta chave é usada para autenticar requisições da API. Mantenha-a segura e não a compartilhe publicamente.
            </p>
        </div>

        <!-- Submit Buttons -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    Criado em: {{ $subscription->created_at->format('d/m/Y H:i') }}
                    @if($subscription->updated_at != $subscription->created_at)
                        • Atualizado em: {{ $subscription->updated_at->format('d/m/Y H:i') }}
                    @endif
                </div>

                <div class="flex space-x-3">
                    <a href="{{ route('subscriptions.show', $subscription) }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                        Salvar Alterações
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function copyApiKey() {
    const apiKey = '{{ $subscription->api_key }}';
    navigator.clipboard.writeText(apiKey).then(function() {
        // Feedback visual
        const button = event.target;
        const originalText = button.textContent;
        button.textContent = 'Copiado!';
        button.classList.add('bg-green-50', 'text-green-700', 'border-green-200');

        setTimeout(() => {
            button.textContent = originalText;
            button.classList.remove('bg-green-50', 'text-green-700', 'border-green-200');
        }, 2000);
    }).catch(function() {
        alert('Erro ao copiar. Selecione manualmente a chave.');
    });
}

// Show/hide suspension reason field based on status
document.querySelector('[name="status"]').addEventListener('change', function() {
    const suspensionField = document.querySelector('[name="suspension_reason"]').closest('.mt-4');
    if (this.value === 'suspended') {
        suspensionField.style.display = 'block';
    } else {
        suspensionField.style.display = 'none';
    }
});
</script>
@endsection