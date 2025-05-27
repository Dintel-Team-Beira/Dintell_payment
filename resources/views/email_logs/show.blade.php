@extends('layouts.app')

@section('title', 'Detalhes do Email - #' . $emailLog->id)

@section('header-actions')
<div class="flex items-center gap-x-3">
    <a href="{{ route('email-logs.index') }}"
       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-all duration-200 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Voltar
    </a>

    @if($emailLog->status === 'failed')
        <form method="POST" action="{{ route('email-logs.resend', $emailLog) }}" class="inline">
            @csrf
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-all duration-200 bg-red-600 rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    onclick="return confirm('Tem certeza que deseja reenviar este email?')">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Reenviar
            </button>
        </form>
    @endif

    <button onclick="copyEmailDetails()"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 transition-all duration-200 border border-blue-200 rounded-lg bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
        </svg>
        Copiar
    </button>
</div>
@endsection

@section('content')
<div class="mx-auto max-w-8xl">
    <!-- Header Section -->
    <div class="mb-8 bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-8">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-4">
                    <!-- Status Badge Icon -->
                    <div class="flex-shrink-0">
                        @if($emailLog->status === 'sent')
                            <div class="flex items-center justify-center rounded-full w-14 h-14 bg-emerald-100">
                                <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        @elseif($emailLog->status === 'failed')
                            <div class="flex items-center justify-center bg-red-100 rounded-full w-14 h-14">
                                <svg class="text-red-600 w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        @else
                            <div class="flex items-center justify-center rounded-full w-14 h-14 bg-amber-100">
                                <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Email Info -->
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Email Log #{{ $emailLog->id }}</h1>
                        <p class="mt-1 text-gray-600">{{ $emailLog->to_email }}</p>
                        <div class="flex items-center mt-3 space-x-3">
                            <!-- Status Badge -->
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $emailLog->status === 'sent' ? 'bg-emerald-100 text-emerald-800' :
                                   ($emailLog->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                <div class="w-2 h-2 rounded-full mr-2
                                    {{ $emailLog->status === 'sent' ? 'bg-emerald-500' :
                                       ($emailLog->status === 'failed' ? 'bg-red-500' : 'bg-amber-500') }}"></div>
                                {{ ucfirst($emailLog->status) }}
                            </span>

                            <!-- Type Badge -->
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $emailLog->type === 'suspended' ? 'bg-orange-100 text-orange-800' :
                                   ($emailLog->type === 'activated' ? 'bg-green-100 text-green-800' :
                                    ($emailLog->type === 'expiring' ? 'bg-blue-100 text-blue-800' :
                                     ($emailLog->type === 'payment' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'))) }}">
                                @switch($emailLog->type)
                                    @case('suspended')
                                        ⚠️ Suspensão
                                        @break
                                    @case('activated')
                                        ✅ Ativação
                                        @break
                                    @case('expiring')
                                        ⏰ Expiração
                                        @break
                                    @case('payment')
                                        💰 Pagamento
                                        @break
                                    @case('renewed')
                                        🔄 Renovação
                                        @break
                                    @case('test')
                                        🧪 Teste
                                        @break
                                    @default
                                        {{ ucfirst($emailLog->type) }}
                                @endswitch
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="text-right">
                    <div class="text-sm text-gray-500">Criado em</div>
                    <div class="font-medium text-gray-900">{{ $emailLog->created_at->format('d/m/Y') }}</div>
                    <div class="text-sm text-gray-500">{{ $emailLog->created_at->format('H:i') }}</div>
                    @if($emailLog->sent_at)
                        <div class="mt-2 text-sm text-gray-500">Enviado em</div>
                        <div class="font-medium text-emerald-600">{{ $emailLog->sent_at->format('d/m/Y H:i') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Left Column - Email Content -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Email Details Card -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="flex items-center text-lg font-semibold text-gray-900">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Detalhes do Email
                    </h3>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Recipient -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Destinatário</label>
                        <div class="flex items-center p-3 rounded-lg bg-gray-50">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                            <span class="font-medium text-gray-900">{{ $emailLog->to_email }}</span>
                        </div>
                    </div>

                    <!-- Subject -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Assunto</label>
                        <div class="p-3 rounded-lg bg-gray-50">
                            <span class="text-gray-900">{{ $emailLog->subject }}</span>
                        </div>
                    </div>

                    <!-- Content -->
                    @if($emailLog->content)
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Conteúdo</label>
                        <div class="p-4 border rounded-lg bg-gray-50">
                            <div class="text-sm leading-relaxed text-gray-900 whitespace-pre-wrap">{{ $emailLog->content }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Error Details (if failed) -->
            @if($emailLog->status === 'failed' && $emailLog->error_message)
            <div class="bg-white border border-red-200 shadow-sm rounded-xl">
                <div class="px-6 py-5 border-b border-red-200 bg-red-50">
                    <h3 class="flex items-center text-lg font-semibold text-red-900">
                        <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        Detalhes do Erro
                    </h3>
                </div>

                <div class="p-6">
                    <div class="p-4 border border-red-200 rounded-lg bg-red-50">
                        <pre class="font-mono text-sm text-red-800 whitespace-pre-wrap">{{ $emailLog->error_message }}</pre>
                    </div>

                    <div class="p-3 mt-4 border border-blue-200 rounded-lg bg-blue-50">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm text-blue-700">
                                <strong>Dica:</strong> Verifique a configuração do servidor de email, credenciais e conectividade.
                                Você pode tentar reenviar este email usando o botão no topo da página.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Timeline -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="flex items-center text-lg font-semibold text-gray-900">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Cronologia
                    </h3>
                </div>

                <div class="p-6">
                    <div class="flow-root">
                        <ul class="space-y-6">
                            <!-- Email Created -->
                            <li class="relative flex gap-x-4">
                                <div class="absolute top-0 left-0 flex justify-center w-6 -bottom-6">
                                    <div class="w-px bg-gray-200"></div>
                                </div>
                                <div class="relative flex items-center justify-center flex-none w-6 h-6 bg-white">
                                    <div class="h-1.5 w-1.5 rounded-full bg-blue-500 ring-1 ring-blue-500"></div>
                                </div>
                                <div class="flex-auto py-0.5">
                                    <div class="text-sm font-medium text-gray-900">Email criado no sistema</div>
                                    <div class="text-sm text-gray-500">{{ $emailLog->created_at->format('d/m/Y H:i:s') }}</div>
                                </div>
                            </li>

                            <!-- Email Processing/Status -->
                            @if($emailLog->status === 'sent' && $emailLog->sent_at)
                            <li class="relative flex gap-x-4">
                                <div class="relative flex items-center justify-center flex-none w-6 h-6 bg-white">
                                    <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="flex-auto py-0.5">
                                    <div class="text-sm font-medium text-gray-900">Email enviado com sucesso</div>
                                    <div class="text-sm text-gray-500">{{ $emailLog->sent_at->format('d/m/Y H:i:s') }}</div>
                                    <div class="mt-1 text-xs text-emerald-600">
                                        Tempo de processamento: {{ $emailLog->created_at->diffInSeconds($emailLog->sent_at) }}s
                                    </div>
                                </div>
                            </li>
                            @elseif($emailLog->status === 'failed')
                            <li class="relative flex gap-x-4">
                                <div class="relative flex items-center justify-center flex-none w-6 h-6 bg-white">
                                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="flex-auto py-0.5">
                                    <div class="text-sm font-medium text-gray-900">Falha no envio</div>
                                    <div class="text-sm text-gray-500">{{ $emailLog->updated_at->format('d/m/Y H:i:s') }}</div>
                                    <div class="mt-1 text-xs text-red-600">Requer atenção - pode ser reenviado</div>
                                </div>
                            </li>
                            @else
                            <li class="relative flex gap-x-4">
                                <div class="relative flex items-center justify-center flex-none w-6 h-6 bg-white">
                                    <div class="h-1.5 w-1.5 rounded-full bg-amber-500 ring-1 ring-amber-500 animate-pulse"></div>
                                </div>
                                <div class="flex-auto py-0.5">
                                    <div class="text-sm font-medium text-gray-900">Na fila para envio</div>
                                    <div class="text-sm text-gray-500">Aguardando processamento</div>
                                    <div class="mt-1 text-xs text-amber-600">O email será processado em breve</div>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-6">
            <!-- Related Info -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informações Relacionadas</h3>
                </div>

                <div class="p-6 space-y-4">
                    @if($emailLog->client)
                    <div class="flex items-center p-3 rounded-lg bg-blue-50">
                        <div class="flex items-center justify-center w-10 h-10 mr-3 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $emailLog->client->name }}</div>
                            <div class="text-sm text-gray-600">{{ $emailLog->client->email }}</div>
                        </div>
                    </div>
                    @endif

                    @if($emailLog->subscription)
                    <div class="flex items-center p-3 rounded-lg bg-emerald-50">
                        <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-lg bg-emerald-100">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $emailLog->subscription->domain }}</div>
                            @if($emailLog->subscription->plan)
                                <div class="text-sm text-gray-600">{{ $emailLog->subscription->plan->name }}</div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Email ID -->
                    <div class="p-3 rounded-lg bg-gray-50">
                        <div class="mb-1 text-sm font-medium text-gray-700">ID do Email</div>
                        <div class="px-2 py-1 font-mono text-sm text-gray-900 bg-white border rounded">{{ $emailLog->id }}</div>
                    </div>
                </div>
            </div>

            <!-- Technical Details -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Detalhes Técnicos</h3>
                </div>

                <div class="p-6">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-700">Tipo de Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($emailLog->type) }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-700">Status Atual</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $emailLog->status === 'sent' ? 'bg-emerald-100 text-emerald-800' :
                                       ($emailLog->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ ucfirst($emailLog->status) }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-700">Data de Criação</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $emailLog->created_at->format('d/m/Y H:i:s') }}</dd>
                        </div>

                        @if($emailLog->sent_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-700">Data de Envio</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $emailLog->sent_at->format('d/m/Y H:i:s') }}</dd>
                        </div>
                        @endif

                        <div>
                            <dt class="text-sm font-medium text-gray-700">Última Atualização</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $emailLog->updated_at->format('d/m/Y H:i:s') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-700">Tempo desde criação</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $emailLog->created_at->diffForHumans() }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Ações Rápidas</h3>
                </div>

                <div class="p-6">
                    <div class="space-y-3">
                        @if($emailLog->status === 'failed')
                        <form method="POST" action="{{ route('email-logs.resend', $emailLog) }}" class="w-full">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white transition-colors bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                    onclick="return confirm('Reenviar este email?')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Ver Subscrição
                        </a>
                        @endif

                        <a href="{{ route('email-logs.index') }}"
                           class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-indigo-700 transition-colors rounded-lg bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Voltar aos Logs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
<div id="successMessage" class="fixed z-50 max-w-md p-4 border rounded-lg shadow-lg top-4 right-4 bg-emerald-100 border-emerald-400 text-emerald-700">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
</div>
@endif

@if($errors->any())
<div id="errorMessage" class="fixed z-50 max-w-md p-4 text-red-700 bg-red-100 border border-red-400 rounded-lg shadow-lg top-4 right-4">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $errors->first() }}
    </div>
</div>
@endif

<!-- Copy Success Toast -->
<div id="copyToast" class="fixed z-50 max-w-md p-4 text-blue-700 transition-transform duration-300 transform translate-x-full bg-blue-100 border border-blue-400 rounded-lg shadow-lg top-4 right-4">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
        </svg>
        Detalhes copiados com sucesso!
    </div>
</div>

<script>
// Copy email details to clipboard
function copyEmailDetails() {
    const details = {
        id: {{ $emailLog->id }},
        to_email: '{{ $emailLog->to_email }}',
        subject: '{{ addslashes($emailLog->subject) }}',
        type: '{{ $emailLog->type }}',
        status: '{{ $emailLog->status }}',
        created_at: '{{ $emailLog->created_at->format('Y-m-d H:i:s') }}',
        @if($emailLog->sent_at)
        sent_at: '{{ $emailLog->sent_at->format('Y-m-d H:i:s') }}',
        @endif
        @if($emailLog->error_message)
        error_message: '{{ addslashes(Str::limit($emailLog->error_message, 200)) }}',
        @endif
        @if($emailLog->client)
        client_name: '{{ addslashes($emailLog->client->name) }}',
        client_email: '{{ $emailLog->client->email }}',
        @endif
        @if($emailLog->subscription)
        subscription_domain: '{{ $emailLog->subscription->domain }}',
        @if($emailLog->subscription->plan)
        subscription_plan: '{{ addslashes($emailLog->subscription->plan->name) }}',
        @endif
        @endif
        @if($emailLog->content)
        content: '{{ addslashes(Str::limit($emailLog->content, 500)) }}',
        @endif
    };

    const formattedText = Object.entries(details)
        .filter(([key, value]) => value !== null && value !== undefined && value !== '')
        .map(([key, value]) => `${key.replace(/_/g, ' ').toUpperCase()}: ${value}`)
        .join('\n');

    navigator.clipboard.writeText(formattedText).then(function() {
        showCopyToast();
    }).catch(function(err) {
        console.error('Erro ao copiar: ', err);
        fallbackCopyTextToClipboard(formattedText);
    });
}

// Fallback copy method
function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.left = "-999999px";
    textArea.style.top = "-999999px";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        document.execCommand('copy');
        showCopyToast();
    } catch (err) {
        console.error('Fallback: Erro ao copiar', err);
        alert('Erro ao copiar detalhes. Tente novamente.');
    }

    document.body.removeChild(textArea);
}

// Show copy success toast
function showCopyToast() {
    const toast = document.getElementById('copyToast');
    toast.classList.remove('translate-x-full');
    toast.classList.add('translate-x-0');

    setTimeout(() => {
        toast.classList.remove('translate-x-0');
        toast.classList.add('translate-x-full');
    }, 3000);
}

// Auto-hide flash messages
document.addEventListener('DOMContentLoaded', function() {
    const messages = ['successMessage', 'errorMessage'];

    messages.forEach(messageId => {
        const element = document.getElementById(messageId);
        if (element) {
            setTimeout(() => {
                element.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
                element.style.opacity = '0';
                element.style.transform = 'translateX(100%)';
                setTimeout(() => element.remove(), 500);
            }, 5000);
        }
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + C to copy details (when not selecting text)
    if ((e.ctrlKey || e.metaKey) && e.key === 'c' && !window.getSelection().toString()) {
        e.preventDefault();
        copyEmailDetails();
    }

    // Escape to go back
    if (e.key === 'Escape') {
        window.location.href = '{{ route('email-logs.index') }}';
    }

    // R key to resend (if failed)
    @if($emailLog->status === 'failed')
    if (e.key === 'r' || e.key === 'R') {
        if (confirm('Reenviar este email?')) {
            document.querySelector('form[action*="resend"]').submit();
        }
    }
    @endif
});

// Auto-refresh for queued emails
@if($emailLog->status === 'queued')
let refreshInterval = setInterval(function() {
    // Check if the page is still visible
    if (!document.hidden) {
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Parse the response and check if status changed
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newStatus = doc.querySelector('[data-email-status]')?.dataset.emailStatus;

            if (newStatus && newStatus !== 'queued') {
                clearInterval(refreshInterval);
                location.reload();
            }
        })
        .catch(error => {
            console.log('Auto-refresh error:', error);
        });
    }
}, 15000); // Check every 15 seconds

// Stop auto-refresh when user leaves the page
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        clearInterval(refreshInterval);
    }
});
@endif

// Enhanced button interactions
document.addEventListener('DOMContentLoaded', function() {
    // Add loading state to resend button
    const resendForm = document.querySelector('form[action*="resend"]');
    if (resendForm) {
        resendForm.addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;

            button.disabled = true;
            button.innerHTML = `
                <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Reenviando...
            `;

            // Reset button state if form submission fails
            setTimeout(() => {
                button.disabled = false;
                button.innerHTML = originalText;
            }, 10000);
        });
    }
});

// Performance optimization - lazy load heavy content
document.addEventListener('DOMContentLoaded', function() {
    // Animate timeline items on scroll
    const timelineItems = document.querySelectorAll('.flow-root li');

    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    timelineItems.forEach(item => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        item.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
        observer.observe(item);
    });
});
</script>

<style>
/* Custom animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

/* Status indicator pulse for queued emails */
@if($emailLog->status === 'queued')
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
@endif


.hover-lift {
    transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
}

.hover-lift:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Custom scrollbar for content areas */
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f7fafc;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

/* Loading button state */
.btn-loading {
    opacity: 0.8;
    cursor: not-allowed;
}

/* Enhanced focus states */
.focus-ring:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    border-color: #3b82f6;
}

/* Error message styling */
.error-content {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
}

/* Timeline enhancements */
.timeline-item {
    position: relative;
}

.timeline-connector {
    position: absolute;
    left: 12px;
    top: 24px;
    bottom: -24px;
    width: 2px;
    background: linear-gradient(to bottom, #e5e7eb, transparent);
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    .lg\:col-span-2 {
        margin-bottom: 2rem;
    }
}

@media (max-width: 640px) {
    .space-y-6 > * + * {
        margin-top: 1rem;
    }

    .px-6 {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .py-8 {
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
    }
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }

    .bg-white {
        background: white !important;
    }

    .shadow-sm,
    .shadow-lg {
        box-shadow: none !important;
    }

    .border {
        border: 1px solid #d1d5db !important;
    }
}
</style>

@endsection