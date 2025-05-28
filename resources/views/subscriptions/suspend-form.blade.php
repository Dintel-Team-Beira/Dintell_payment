<form action="{{ route('subscriptions.suspend', $subscription) }}" method="POST" class="space-y-6">
    @csrf

    <div class="p-4 border border-red-200 rounded-lg bg-red-50">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Atenção: Suspensão de Serviço</h3>
                <div class="mt-2 text-sm text-red-700">
                    <p>Esta ação suspenderá temporariamente o website {{ $subscription->domain }}. Um email detalhado será enviado automaticamente ao cliente.</p>
                </div>
            </div>
        </div>
    </div>

    <div>
        <label for="reason" class="block text-sm font-medium text-gray-700">Motivo da Suspensão *</label>
        <select name="reason" id="reason" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
            <option value="">Selecione o motivo...</option>
            <option value="Falta de pagamento - Serviço vencido há mais de 7 dias">Falta de pagamento - Vencido há +7 dias</option>
            <option value="Violação dos Termos de Serviço">Violação dos Termos de Serviço</option>
            <option value="Uso excessivo de recursos do servidor">Uso excessivo de recursos</option>
            <option value="Solicitação do cliente">Solicitação do cliente</option>
            <option value="Manutenção técnica prolongada">Manutenção técnica</option>
            <option value="Conteúdo inadequado detectado">Conteúdo inadequado</option>
        </select>
        @error('reason')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-center">
        <input type="checkbox" name="send_notification" value="1" checked
               class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
        <label for="send_notification" class="block ml-2 text-sm text-gray-900">
            Enviar notificação detalhada por email (recomendado)
        </label>
    </div>

    <div class="flex justify-end space-x-3">
        <button type="button" onclick="history.back()"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
            Cancelar
        </button>
        <button type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            Suspender Serviço
        </button>
    </div>
</form>