<div class="bg-white border border-gray-200 shadow-sm rounded-xl">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Performance de Cotações</h3>
            <p class="text-sm text-gray-500">Análise detalhada das cotações</p>
        </div>
        <div class="flex space-x-2">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                {{ $quotesStats['conversion_rate'] }}% conversão
            </span>
            @if($quotesStats['quotes_growth'] >= 0)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    +{{ number_format($quotesStats['quotes_growth'], 1) }}%
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    {{ number_format($quotesStats['quotes_growth'], 1) }}%
                </span>
            @endif
        </div>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ $monthlyStats['total'] }}</div>
                <div class="text-sm text-gray-500">Este Mês</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $monthlyStats['pending'] }}</div>
                <div class="text-sm text-gray-500">Pendentes</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $monthlyStats['accepted'] }}</div>
                <div class="text-sm text-gray-500">Aceitas</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-600">{{ number_format($monthlyStats['value'], 0) }} MT</div>
                <div class="text-sm text-gray-500">Valor Total</div>
            </div>
        </div>

        <!-- Progress bar para meta de conversão -->
        <div class="mt-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Meta de Conversão</span>
                <span class="text-sm text-gray-500">{{ $quotesStats['conversion_rate'] }}% de {{ $quotesStats['conversion_target'] }}%</span>
            </div>
            <div class="w-full h-2 bg-gray-200 rounded-full">
                <div class="h-2 transition-all duration-300 bg-purple-600 rounded-full"
                     style="width: {{ min(($quotesStats['conversion_rate'] / $quotesStats['conversion_target']) * 100, 100) }}%"></div>
            </div>
        </div>
    </div>
</div>
