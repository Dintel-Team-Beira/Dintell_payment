<!-- Formulário componentizado para criar/editar empresas -->
@php
    $isEdit = isset($company) && $company->exists;
    $formAction = $isEdit ? route('admin.companies.update', $company) : route('admin.companies.store');
    $formMethod = $isEdit ? 'PUT' : 'POST';
    $submitText = $isEdit ? 'Atualizar Empresa' : 'Criar Empresa';
@endphp

<form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" class="space-y-8">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 gap-8 xl:grid-cols-3">
        <!-- Coluna Principal -->
        <div class="space-y-8 xl:col-span-2">
            <!-- Informações Básicas -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Informações da Empresa</h3>
                            <p class="text-sm text-gray-600">Dados básicos da empresa</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Nome da Empresa -->
                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-700">
                                Nome da Empresa *
                            </label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name', $isEdit ? $company->name : '') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-300 @enderror">
                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <label for="slug" class="block mb-2 text-sm font-medium text-gray-700">
                                Slug (URL única)
                            </label>
                            <input type="text"
                                   name="slug"
                                   id="slug"
                                   value="{{ old('slug', $isEdit ? $company->slug : '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('slug') border-red-300 @enderror"
                                   placeholder="empresa-exemplo">
                            <p class="mt-1 text-xs text-gray-500">{{ $isEdit ? 'Slug atual da empresa' : 'Se deixado em branco, será gerado automaticamente' }}</p>
                            @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-700">
                                Email *
                            </label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   value="{{ old('email', $isEdit ? $company->email : '') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-300 @enderror">
                            @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Telefone -->
                        <!-- Telefone -->
                        <div>
                            <label for="phone" class="block mb-2 text-sm font-medium text-gray-700">
                                Telefone
                            </label>
                            <input type="text"
                                   name="phone"
                                   id="phone"
                                   value="{{ old('phone', $isEdit ? $company->phone : '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-300 @enderror">
                            @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- NUIT -->
                        <div>
                            <label for="tax_number" class="block mb-2 text-sm font-medium text-gray-700">
                                NUIT
                            </label>
                            <input type="text"
                                   name="tax_number"
                                   id="tax_number"
                                   value="{{ old('tax_number', $isEdit ? $company->tax_number : '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tax_number') border-red-300 @enderror">
                            @error('tax_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Cidade -->
                        <div>
                            <label for="city" class="block mb-2 text-sm font-medium text-gray-700">
                                Cidade
                            </label>
                            <input type="text"
                                   name="city"
                                   id="city"
                                   value="{{ old('city', $isEdit ? $company->city : '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('city') border-red-300 @enderror">
                            @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Endereço -->
                    <div>
                        <label for="address" class="block mb-2 text-sm font-medium text-gray-700">
                            Endereço
                        </label>
                        <textarea name="address"
                                  id="address"
                                  rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-300 @enderror">{{ old('address', $isEdit ? $company->address : '') }}</textarea>
                        @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Logo -->
                    <div>
                        <label for="logo" class="block mb-2 text-sm font-medium text-gray-700">
                            Logo da Empresa
                        </label>

                        @if($isEdit && $company->logo)
                        <div class="mb-4">
                            <p class="mb-2 text-sm text-gray-600">Logo atual:</p>
                            <img src="{{ Storage::url($company->logo) }}" alt="{{ $company->name }}" class="w-20 h-20 rounded-lg">
                        </div>
                        @endif

                        <div class="flex items-center justify-center w-full">
                            <label for="logo" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500">
                                        <span class="font-semibold">Clique para upload</span>
                                        {{ $isEdit && $company->logo ? 'ou arraste para alterar' : 'ou arraste e solte' }}
                                    </p>
                                    <p class="text-xs text-gray-500">PNG, JPG ou GIF (MAX. 2MB)</p>
                                </div>
                                <input id="logo" name="logo" type="file" class="hidden" accept="image/*" />
                            </label>
                        </div>
                        @error('logo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            @if($isEdit)
            <!-- Configurações de Faturação (apenas para edição) -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Configurações de Faturação</h3>
                            <p class="text-sm text-gray-600">Configurações fiscais e monetárias</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Moeda -->
                        <div>
                            <label for="currency" class="block mb-2 text-sm font-medium text-gray-700">
                                Moeda
                            </label>
                            <select name="currency"
                                    id="currency"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('currency') border-red-300 @enderror">
                                <option value="MZN" {{ old('currency', $company->currency) === 'MZN' ? 'selected' : '' }}>MZN - Metical Moçambicano</option>
                                <option value="USD" {{ old('currency', $company->currency) === 'USD' ? 'selected' : '' }}>USD - Dólar Americano</option>
                                <option value="EUR" {{ old('currency', $company->currency) === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            </select>
                            @error('currency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Taxa de IVA -->
                        <div>
                            <label for="default_tax_rate" class="block mb-2 text-sm font-medium text-gray-700">
                                Taxa de IVA Padrão (%)
                            </label>
                            <input type="number"
                                   name="default_tax_rate"
                                   id="default_tax_rate"
                                   value="{{ old('default_tax_rate', $company->default_tax_rate) }}"
                                   min="0"
                                   max="100"
                                   step="0.01"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('default_tax_rate') border-red-300 @enderror">
                            @error('default_tax_rate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- M-Pesa -->
                        <div>
                            <label for="mpesa_number" class="block mb-2 text-sm font-medium text-gray-700">
                                Número M-Pesa
                            </label>
                            <input type="text"
                                   name="mpesa_number"
                                   id="mpesa_number"
                                   value="{{ old('mpesa_number', $company->mpesa_number) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('mpesa_number') border-red-300 @enderror">
                            @error('mpesa_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Configurações de Acesso -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Configurações de Acesso</h3>
                            <p class="text-sm text-gray-600">Permissões e funcionalidades</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Domínio Personalizado -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox"
                                       name="custom_domain_enabled"
                                       value="1"
                                       {{ old('custom_domain_enabled', $isEdit ? $company->custom_domain_enabled : false) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                <span class="ml-2 text-sm font-medium text-gray-700">Domínio personalizado habilitado</span>
                            </label>
                        </div>

                        <!-- API Access -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox"
                                       name="api_access_enabled"
                                       value="1"
                                       {{ old('api_access_enabled', $isEdit ? $company->api_access_enabled : false) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                <span class="ml-2 text-sm font-medium text-gray-700">Acesso à API habilitado</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Plano de Subscrição -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Plano de Subscrição</h3>
                            <p class="text-sm text-gray-600">Selecione o plano</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($subscriptionPlans as $key => $plan)
                        <label class="relative flex items-start p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 focus:outline-none">
                            <div class="flex items-center h-5">
                                <input type="radio"
                                       name="subscription_plan"
                                       value="{{ $key }}"
                                       {{ old('subscription_plan', $isEdit ? $company->subscription_plan : 'basic') == $key ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
                            </div>
                            <div class="ml-3 text-sm">
                                <div class="font-medium text-gray-900">{{ $plan['name'] }}</div>
                                <div class="text-gray-600">{{ number_format($plan['price'], 2) }} MT/mês</div>
                                <div class="mt-2 space-y-1">
                                    <div class="text-xs text-gray-500">
                                        • {{ $plan['max_users'] == 999 ? 'Usuários ilimitados' : $plan['max_users'] . ' usuário(s)' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        • {{ $plan['max_invoices_per_month'] == 999999 ? 'Faturas ilimitadas' : $plan['max_invoices_per_month'] . ' faturas/mês' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        • {{ $plan['max_clients'] == 999999 ? 'Clientes ilimitados' : $plan['max_clients'] . ' clientes' }}
                                    </div>
                                    @foreach($plan['features'] as $feature)
                                    <div class="text-xs text-gray-500">• {{ $feature }}</div>
                                    @endforeach
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('subscription_plan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Status da Empresa -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Status</h3>
                            <p class="text-sm text-gray-600">{{ $isEdit ? 'Status atual da empresa' : 'Defina o status inicial' }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="radio"
                                   name="status"
                                   value="trial"
                                   {{ old('status', $isEdit ? $company->status : 'trial') == 'trial' ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Período de Teste</span>
                        </label>

                        <label class="flex items-center">
                            <input type="radio"
                                   name="status"
                                   value="active"
                                   {{ old('status', $isEdit ? $company->status : '') == 'active' ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ativo</span>
                        </label>

                        <label class="flex items-center">
                            <input type="radio"
                                   name="status"
                                   value="suspended"
                                   {{ old('status', $isEdit ? $company->status : '') == 'suspended' ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Suspenso</span>
                        </label>

                        @if($isEdit)
                        <label class="flex items-center">
                            <input type="radio"
                                   name="status"
                                   value="inactive"
                                   {{ old('status', $company->status) == 'inactive' ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Inativo</span>
                        </label>
                        @endif
                    </div>

                    <!-- Campo de dias de trial -->
                    @if(!$isEdit)
                    <div id="trialDaysField" class="mt-4" style="display: none;">
                        <label for="trial_days" class="block mb-2 text-sm font-medium text-gray-700">
                            Dias de Trial
                        </label>
                        <input type="number"
                               name="trial_days"
                               id="trial_days"
                               value="{{ old('trial_days', 30) }}"
                               min="1"
                               max="90"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    @elseif($company->is_trial && $company->trial_ends_at)
                    <div class="p-3 mt-4 border border-blue-200 rounded-lg bg-blue-50">
                        <p class="text-sm text-blue-800">
                            <strong>Trial atual:</strong> Expira em {{ $company->trial_days_left }} dias ({{ $company->trial_ends_at->format('d/m/Y') }})
                        </p>
                    </div>
                    @endif

                    @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @if($isEdit)
            <!-- Limites Personalizados (apenas para edição) -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 mr-3 bg-orange-100 rounded-lg">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Limites Personalizados</h3>
                            <p class="text-sm text-gray-600">Sobrescrever limites do plano</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label for="max_users" class="block mb-2 text-sm font-medium text-gray-700">
                            Máximo de Usuários
                        </label>
                        <input type="number"
                               name="max_users"
                               id="max_users"
                               value="{{ old('max_users', $company->max_users) }}"
                               min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="max_invoices_per_month" class="block mb-2 text-sm font-medium text-gray-700">
                            Máximo de Faturas por Mês
                        </label>
                        <input type="number"
                               name="max_invoices_per_month"
                               id="max_invoices_per_month"
                               value="{{ old('max_invoices_per_month', $company->max_invoices_per_month) }}"
                               min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="max_clients" class="block mb-2 text-sm font-medium text-gray-700">
                            Máximo de Clientes
                        </label>
                        <input type="number"
                               name="max_clients"
                               id="max_clients"
                               value="{{ old('max_clients', $company->max_clients) }}"
                               min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="monthly_fee" class="block mb-2 text-sm font-medium text-gray-700">
                            Taxa Mensal Personalizada (MT)
                        </label>
                        <input type="number"
                               name="monthly_fee"
                               id="monthly_fee"
                               value="{{ old('monthly_fee', $company->monthly_fee) }}"
                               min="0"
                               step="0.01"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Deixe vazio para usar preço do plano</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Ações -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="p-6">
                    <div class="space-y-3">
                        <button type="submit"
                                class="inline-flex items-center justify-center w-full px-6 py-3 text-sm font-medium text-white transition-colors bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $submitText }}
                        </button>

                        <a href="{{ $isEdit ? route('admin.companies.show', $company) : route('admin.companies.index') }}"
                           class="inline-flex items-center justify-center w-full px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancelar
                        </a>
                    </div>

                    <div class="pt-4 mt-4 border-t border-gray-200">
                        <div class="flex items-center justify-center text-xs text-gray-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Campos marcados com * são obrigatórios
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form
