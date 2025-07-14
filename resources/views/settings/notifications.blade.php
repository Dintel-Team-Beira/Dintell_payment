@extends('layouts.app')

@section('title', 'Configurações de Notificações')

@section('header-actions')
<div class="flex items-center gap-x-4">
    <a href="{{ route('settings.index') }}"
       class="flex items-center px-3 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Voltar
    </a>
</div>
@endsection

@section('content')
<div class="mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Configurações de Notificações</h1>
    </div>

    <!-- Configurações de E-mail -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Notificações por E-mail</h3>
            <p class="mt-1 text-sm text-gray-500">Configure quando enviar e-mails automáticos</p>
        </div>
        <div class="p-6">
            <form action="{{ route('settings.notifications.update') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Configurações Gerais -->
                <div class="space-y-4">
                    <h4 class="text-base font-medium text-gray-900">Envio Automático</h4>

                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input id="send_invoice_emails"
                                   name="send_invoice_emails"
                                   type="checkbox"
                                   value="1"
                                   {{ old('send_invoice_emails', $settings->send_invoice_emails) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="send_invoice_emails" class="block ml-3 text-sm text-gray-700">
                                Enviar facturas por e-mail automaticamente
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input id="send_quote_emails"
                                   name="send_quote_emails"
                                   type="checkbox"
                                   value="1"
                                   {{ old('send_quote_emails', $settings->send_quote_emails) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="send_quote_emails" class="block ml-3 text-sm text-gray-700">
                                Enviar orçamentos por e-mail automaticamente
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input id="send_overdue_reminders"
                                   name="send_overdue_reminders"
                                   type="checkbox"
                                   value="1"
                                   {{ old('send_overdue_reminders', $settings->send_overdue_reminders) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="send_overdue_reminders" class="block ml-3 text-sm text-gray-700">
                                Enviar lembretes de facturas vencidas
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Configuração de Lembretes -->
                <div class="pt-6 border-t border-gray-200">
                    <h4 class="mb-4 text-base font-medium text-gray-900">Lembretes de Vencimento</h4>

                    <div>
                        <label for="reminder_days" class="block mb-2 text-sm font-medium text-gray-700">
                            Enviar lembrete quantos dias antes do vencimento?
                        </label>
                        <select name="reminder_days"
                                id="reminder_days"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                            <option value="1" {{ old('reminder_days', $settings->reminder_days ?? 7) == 1 ? 'selected' : '' }}>1 dia antes</option>
                            <option value="3" {{ old('reminder_days', $settings->reminder_days ?? 7) == 3 ? 'selected' : '' }}>3 dias antes</option>
                            <option value="7" {{ old('reminder_days', $settings->reminder_days ?? 7) == 7 ? 'selected' : '' }}>7 dias antes</option>
                            <option value="15" {{ old('reminder_days', $settings->reminder_days ?? 7) == 15 ? 'selected' : '' }}>15 dias antes</option>
                            <option value="30" {{ old('reminder_days', $settings->reminder_days ?? 7) == 30 ? 'selected' : '' }}>30 dias antes</option>
                        </select>
                    </div>
                </div>

                <!-- Templates de E-mail -->
                <div class="pt-6 border-t border-gray-200">
                    <h4 class="mb-4 text-base font-medium text-gray-900">Templates de E-mail</h4>

                    <div class="space-y-6">
                        <div>
                            <label for="email_template_invoice" class="block mb-2 text-sm font-medium text-gray-700">
                                Template para Facturas
                            </label>
                            <textarea name="email_template_invoice"
                                      id="email_template_invoice"
                                      rows="4"
                                      class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                                      placeholder="Prezado(a) {cliente_nome},&#10;&#10;Segue em anexo a fatura {fatura_numero} no valor de {fatura_total}.&#10;&#10;Atenciosamente,&#10;{empresa_nome}">{{ old('email_template_invoice', $settings->email_template_invoice) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">
                                Variáveis disponíveis: {cliente_nome}, {fatura_numero}, {fatura_total}, {empresa_nome}
                            </p>
                        </div>

                        <div>
                            <label for="email_template_quote" class="block mb-2 text-sm font-medium text-gray-700">
                                Template para Orçamentos
                            </label>
                            <textarea name="email_template_quote"
                                      id="email_template_quote"
                                      rows="4"
                                      class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                                      placeholder="Prezado(a) {cliente_nome},&#10;&#10;Segue em anexo o orçamento {orcamento_numero} no valor de {orcamento_total}.&#10;&#10;Atenciosamente,&#10;{empresa_nome}">{{ old('email_template_quote', $settings->email_template_quote) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">
                                Variáveis disponíveis: {cliente_nome}, {orcamento_numero}, {orcamento_total}, {empresa_nome}
                            </p>
                        </div>

                        <div>
                            <label for="email_template_reminder" class="block mb-2 text-sm font-medium text-gray-700">
                                Template para Lembretes
                            </label>
                            <textarea name="email_template_reminder"
                                      id="email_template_reminder"
                                      rows="4"
                                      class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                                      placeholder="Prezado(a) {cliente_nome},&#10;&#10;Lembramos que a fatura {fatura_numero} vence em {dias_vencimento} dias.&#10;&#10;Atenciosamente,&#10;{empresa_nome}">{{ old('email_template_reminder', $settings->email_template_reminder) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">
                                Variáveis disponíveis: {cliente_nome}, {fatura_numero}, {dias_vencimento}, {empresa_nome}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="flex justify-end pt-6 space-x-3 border-t border-gray-200">
                    <a href="{{ route('settings.index') }}"
                       class="px-3 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancelar
                    </a>
                    <button type="button"
                            class="px-3 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                            onclick="resetTemplates()">
                        Restaurar Templates Padrão
                    </button>
                    <button type="submit"
                            class="px-3 py-2 text-sm font-semibold text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Salvar Configurações
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview de E-mails -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Preview dos E-mails</h3>
            <p class="mt-1 text-sm text-gray-500">Veja como os e-mails aparecerão para os clientes</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Preview Fatura -->
                <div class="p-4 rounded-lg bg-gray-50">
                    <h4 class="flex items-center mb-3 font-medium text-gray-900">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        E-mail de Fatura
                    </h4>
                    <div class="p-3 text-sm bg-white border rounded">
                        <div class="mb-2 font-medium text-gray-800">De: {{ $settings->company_email ?? 'empresa@email.com' }}</div>
                        <div class="mb-2 font-medium text-gray-800">Assunto: Fatura FAT000001</div>
                        <div class="text-gray-600 whitespace-pre-line">{{ $settings->email_template_invoice ?: "Prezado(a) Cliente,\n\nSegue em anexo a fatura FAT000001 no valor de 1.000,00 MT.\n\nAtenciosamente,\n" . $settings->company_name }}</div>
                    </div>
                </div>

                <!-- Preview Orçamento -->
                <div class="p-4 rounded-lg bg-gray-50">
                    <h4 class="flex items-center mb-3 font-medium text-gray-900">
                        <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        E-mail de Orçamento
                    </h4>
                    <div class="p-3 text-sm bg-white border rounded">
                        <div class="mb-2 font-medium text-gray-800">De: {{ $settings->company_email ?? 'empresa@email.com' }}</div>
                        <div class="mb-2 font-medium text-gray-800">Assunto: Orçamento COT000001</div>
                        <div class="text-gray-600 whitespace-pre-line">{{ $settings->email_template_quote ?: "Prezado(a) Cliente,\n\nSegue em anexo o orçamento COT000001 no valor de 2.500,00 MT.\n\nAtenciosamente,\n" . $settings->company_name }}</div>
                    </div>
                </div>

                <!-- Preview Lembrete -->
                <div class="p-4 rounded-lg bg-gray-50">
                    <h4 class="flex items-center mb-3 font-medium text-gray-900">
                        <svg class="w-4 h-4 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM5 7a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2H7a2 2 0 01-2-2V7z"/>
                        </svg>
                        E-mail de Lembrete
                    </h4>
                    <div class="p-3 text-sm bg-white border rounded">
                        <div class="mb-2 font-medium text-gray-800">De: {{ $settings->company_email ?? 'empresa@email.com' }}</div>
                        <div class="mb-2 font-medium text-gray-800">Assunto: Lembrete - Fatura FAT000001</div>
                        <div class="text-gray-600 whitespace-pre-line">{{ $settings->email_template_reminder ?: "Prezado(a) Cliente,\n\nLembramos que a fatura FAT000001 vence em 7 dias.\n\nAtenciosamente,\n" . $settings->company_name }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function resetTemplates() {
    if (confirm('Tem certeza que deseja restaurar os templates padrão? As alterações atuais serão perdidas.')) {
        document.getElementById('email_template_invoice').value = 'Prezado(a) {cliente_nome},\n\nSegue em anexo a fatura {fatura_numero} no valor de {fatura_total}.\n\nAtenciosamente,\n{empresa_nome}';
        document.getElementById('email_template_quote').value = 'Prezado(a) {cliente_nome},\n\nSegue em anexo o orçamento {orcamento_numero} no valor de {orcamento_total}.\n\nAtenciosamente,\n{empresa_nome}';
        document.getElementById('email_template_reminder').value = 'Prezado(a) {cliente_nome},\n\nLembramos que a fatura {fatura_numero} vence em {dias_vencimento} dias.\n\nAtenciosamente,\n{empresa_nome}';
    }
}
</script>
@endpush
@endsection
