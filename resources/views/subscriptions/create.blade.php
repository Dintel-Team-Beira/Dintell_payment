@extends('layouts.app')

@section('title', 'Nova Subscrição')

@section('content')
<div class="max-w-8xl mx-auto">
    <form action="{{ route('subscriptions.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-6">Nova Subscrição</h2>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div>
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                    <label class="block text-sm font-medium text-gray-700">Cliente *</label>
                    <select name="client_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Selecionar cliente</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id', request('client_id')) == $client->id ? 'selected' : '' }}>
                                {{ $client->name }} ({{ $client->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('client_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Plano *</label>
                    <select name="subscription_plan_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Selecionar plano</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('subscription_plan_id') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} - MT {{ number_format($plan->price, 2) }}
                            </option>
                        @endforeach
                    </select>
                    @error('subscription_plan_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Domínio *</label>
                    <input type="text" name="domain" value="{{ old('domain') }}" required
                           placeholder="exemplo.com"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('domain')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Subdomínio</label>
                    <input type="text" name="subdomain" value="{{ old('subdomain') }}"
                           placeholder="app.exemplo.com"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('subdomain')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Data de Início *</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', now()->format('Y-m-d\TH:i')) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('starts_at')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Data de Expiração</label>
                    <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('ends_at')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Trial até</label>
                    <input type="datetime-local" name="trial_ends_at" value="{{ old('trial_ends_at') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('trial_ends_at')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Valor Pago *</label>
                    <input type="number" step="0.01" name="amount_paid" value="{{ old('amount_paid') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('amount_paid')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Método de Pagamento</label>
                    <select name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Selecionar método</option>
                        <option value="mpesa" {{ old('payment_method') === 'mpesa' ? 'selected' : '' }}>MPesa</option>
                        <option value="visa" {{ old('payment_method') === 'visa' ? 'selected' : '' }}>Visa</option>
                        <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Transferência</option>
                        <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Dinheiro</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex items-center space-x-6">
                <div class="flex items-center">
                    <input type="checkbox" name="auto_renew" value="1" {{ old('auto_renew') ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900">Renovação automática</label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="email_notifications" value="1" {{ old('email_notifications', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900">Notificações por email</label>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('subscriptions.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                Criar Subscrição
            </button>
        </div>
    </form>
</div>
@endsection