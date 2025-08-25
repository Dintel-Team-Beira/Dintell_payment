@extends('layouts.app')

@section('title', 'Nova Subscrição')

@section('content')
    <div class="mx-auto max-w-8xl">
        <form action="{{ route('subscriptions.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="p-6 bg-white rounded-lg shadow-sm ring-1 ring-gray-900/5">
                <h2 class="mb-6 text-lg font-semibold">Nova Subscrição</h2>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                        <label class="block text-sm font-medium text-gray-700">Cliente *</label>
                        <select name="client_id" required
                            class="select2 block w-full p-2 mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Selecionar cliente</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}"
                                    {{ old('client_id', request('client_id')) == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }} ({{ $client->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Plano *</label>
                        <select name="subscription_plan_id" id="subscription_plan_id" required
                            class="select2 block w-full p-2 mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Selecionar plano</option>
                            @foreach ($plans as $plan)
                                <option value="{{ $plan->id }}"
                                    {{ old('subscription_plan_id') == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->name }} - MT {{ number_format($plan->price, 2) }}
                                </option>
                            @endforeach
                        </select>
                        @error('subscription_plan_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Domínio *</label>
                        <input type="text" name="domain" value="{{ old('domain') }}" required placeholder="exemplo.com"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('domain')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Subdomínio</label>
                        <input type="text" name="subdomain" value="{{ old('subdomain') }}" placeholder="app.exemplo.com"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('subdomain')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Data de Início *</label>
                        <input type="datetime-local" name="starts_at"
                            value="{{ old('starts_at', now()->format('Y-m-d\TH:i')) }}" required
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('starts_at')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Data de Expiração</label>
                        <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('ends_at')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Trial até</label>
                        <input type="datetime-local" name="trial_ends_at" value="{{ old('trial_ends_at') }}"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('trial_ends_at')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex ">


                        <div class="w-[400px]">
                            <label class="block text-sm font-medium text-gray-700">Valor Pago *</label>
                            <input type="number" step="0.01" name="amount_paid" value="{{ old('amount_paid') }}"
                                required
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('amount_paid')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mx-5">
                            <label class="block text-sm font-medium text-gray-700">Método de Pagamento</label>
                            <select name="payment_method"
                                class="p-2 mt-1 w-[400px] border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Selecionar método</option>
                                <option value="mpesa" {{ old('payment_method') === 'mpesa' ? 'selected' : '' }}>MPesa
                                </option>
                                <option value="visa" {{ old('payment_method') === 'visa' ? 'selected' : '' }}>Visa
                                </option>
                                <option value="bank_transfer"
                                    {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Transferência
                                </option>
                                <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Dinheiro
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex items-center mt-6 space-x-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="auto_renew" value="1" {{ old('auto_renew') ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label class="block ml-2 text-sm text-gray-900">Renovação automática</label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="email_notifications" value="1"
                            {{ old('email_notifications', true) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label class="block ml-2 text-sm text-gray-900">Notificações por email</label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('subscriptions.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                    Criar Subscrição
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('.select2').select2({
                placeholder: 'Digite para buscar...',
                allowClear: true,
                width: '100%',
                minimumInputLength: 0,
                language: {
                    noResults: function() {
                        return "Nenhum resultado encontrado";
                    },
                    searching: function() {
                        return "Procurando...";
                    },
                    inputTooShort: function() {
                        return "Digite para buscar";
                    },
                    loadingMore: function() {
                        return "Carregando mais...";
                    }
                }
            });

            // Manter seleção após erro de validação Laravel
            @if (old('client_id'))
                $('#client_id').val('{{ old('
                            client_id ') }}').trigger('change');
            @endif

            @if (old('subscription_plan_id'))
                 $('#subscription_plan_id').val('{{ old('
                            subscription_plan_id ') }}').trigger('change');
            @endif

            // Aplicar estilo de erro se houver erro do Laravel
            @error('client_id')
                $('#client_id').next('.select2-container').addClass('select2-container--error');
            @enderror
            @error('subscription_plan_id')
                $('#subscription_plan_id').next('.select2-container').addClass('select2-container--error');
            @enderror

            // Remover erro ao selecionar
            $('#client_id').on('change', function() {
                if ($(this).val()) {
                    $(this).next('.select2-container').removeClass('select2-container--error');
                }
            });
             $('#subscription_plan_id').on('change', function() {
                if ($(this).val()) {
                    $(this).next('.select2-container').removeClass('select2-container--error');
                }
            });
        })
    </script>
@endpush
