<div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
    <!-- Sidebar de Controles -->
    <div class="lg:col-span-1">
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Controles de Preview</h3>

            <!-- Seleção de Tipo -->
            <div class="mb-4">
                <label for="typeSelect" class="block mb-2 text-sm font-medium text-gray-700">
                    Tipo de Documento
                </label>
                <select id="typeSelect"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Selecione o tipo</option>
                    <option value="invoice">Fatura</option>
                    <option value="quote">Cotação</option>
                    <option value="credit">Nota de Crédito</option>
                    <option value="debit">Nota de Débito</option>
                </select>
            </div>

            <!-- Seleção de Template -->
            <div class="mb-6">
                <label for="templateSelect" class="block mb-2 text-sm font-medium text-gray-700">
                    Template
                </label>
                <select id="templateSelect"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    disabled>
                    <option value="">Primeiro selecione o tipo</option>
                </select>
            </div>

            <!-- Ações -->
            <div class="mb-6">
                <button id="refreshPreview"
                    class="w-full px-4 py-2 mb-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Atualizar Preview
                </button>

                <button id="selectTemplate"
                    class="w-full px-4 py-2 mb-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:bg-gray-400 disabled:cursor-not-allowed hidden">
                    <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Selecionar Template
                </button>

                <!-- Indicador de Template Em Uso na Sidebar -->
                <div id="selectedIndicatorSidebar" class="w-full px-4 py-2 mb-2 text-sm font-medium text-blue-800 bg-blue-100 border border-blue-200 rounded-md hidden">
                    <div class="flex items-center justify-center">
                        <svg class="inline w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Template Em Uso
                    </div>
                </div>

                <button id="downloadPdf"
                    class="w-full px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:bg-gray-400 disabled:cursor-not-allowed"
                    disabled>
                    <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download PDF
                </button>
            </div>

            <!-- Info do Template -->
            <div class="pt-6 border-t border-gray-200">
                <h4 class="mb-3 text-sm font-semibold text-gray-900">Informações</h4>
                <div id="templateInfo" class="space-y-2 text-sm text-gray-600">
                    <p>Selecione um template para ver as informações</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Area -->
    <div class="lg:col-span-3">
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <!-- Header do Preview -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Preview do Template</h3>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-500" id="previewStatus">Aguardando seleção...</span>
                        
                        <!-- Indicador de Template Em Uso -->
                        <div id="selectedIndicator" class="hidden flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-full">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-xs font-medium">Em Uso</span>
                        </div>
                        
                        <button id="selectTemplateTop"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:bg-gray-400 disabled:cursor-not-allowed hidden">
                            <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Selecionar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Preview Content -->
            <div class="p-6">
                <!-- Estado Inicial -->
                <div id="initialState" class="flex items-center justify-center py-24">
                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum template selecionado</h3>
                        <p class="text-gray-600">Escolha um tipo de documento no menu lateral para visualizar o
                            template</p>
                    </div>
                </div>

                <!-- Loading State -->
                <div id="loadingState" class="hidden flex items-center justify-center py-24">
                    <div class="text-center">
                        <div class="w-8 h-8 border-b-2 border-blue-600 rounded-full animate-spin mx-auto mb-4"></div>
                        <p class="text-gray-600">Carregando preview...</p>
                    </div>
                </div>

                <!-- Preview Iframe -->
                <div id="previewContainer" class="hidden">
                    <div class="overflow-hidden border border-gray-300 rounded-lg" style="height: 800px;">
                        <iframe id="templatePreview" src="about:blank" class="w-full h-full"
                            sandbox="allow-same-origin">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação -->
<div id="confirmModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Usar Template
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Deseja realmente usar este template? Esta ação irá definir "<span id="modalTemplateName"></span>" como o template em uso para <span id="modalTemplateType"></span>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button id="confirmSelect" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Usar Template
                </button>
                <button id="cancelSelect" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const typeSelect = document.getElementById('typeSelect');
    const templateSelect = document.getElementById('templateSelect');
    const refreshBtn = document.getElementById('refreshPreview');
    const downloadBtn = document.getElementById('downloadPdf');
    const selectTemplateBtn = document.getElementById('selectTemplate');
    const selectTemplateTopBtn = document.getElementById('selectTemplateTop');
    const previewStatus = document.getElementById('previewStatus');
    const templateInfo = document.getElementById('templateInfo');
    const initialState = document.getElementById('initialState');
    const loadingState = document.getElementById('loadingState');
    const previewContainer = document.getElementById('previewContainer');
    const templatePreview = document.getElementById('templatePreview');

    // Modal elements
    const confirmModal = document.getElementById('confirmModal');
    const modalTemplateName = document.getElementById('modalTemplateName');
    const modalTemplateType = document.getElementById('modalTemplateType');
    const confirmSelectBtn = document.getElementById('confirmSelect');
    const cancelSelectBtn = document.getElementById('cancelSelect');
    
    // Selected indicators
    const selectedIndicator = document.getElementById('selectedIndicator');
    const selectedIndicatorSidebar = document.getElementById('selectedIndicatorSidebar');

    // Dados dos templates (serão carregados via AJAX)
    let availableTemplates = {};
    let currentSelectedTemplate = null;

    // Event listeners
    typeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        
        if (selectedType) {
            loadTemplatesList(selectedType);
        } else {
            templateSelect.innerHTML = '<option value="">Primeiro selecione o tipo</option>';
            templateSelect.disabled = true;
            showInitialState();
            downloadBtn.disabled = true;
            hideSelectButtons();
        }
    });

    templateSelect.addEventListener('change', function() {
        const selectedTemplateId = this.value;
        
        if (selectedTemplateId) {
            loadTemplate(selectedTemplateId);
            updateTemplateInfo(selectedTemplateId);
            downloadBtn.disabled = false;
        } else {
            showInitialState();
            downloadBtn.disabled = true;
            hideSelectButtons();
        }
    });

    refreshBtn.addEventListener('click', function() {
        const selectedTemplateId = templateSelect.value;
        if (selectedTemplateId) {
            loadTemplate(selectedTemplateId);
        }
    });

    downloadBtn.addEventListener('click', function() {
        const selectedTemplateId = templateSelect.value;
        if (selectedTemplateId) {
            window.open(`/dintell/template-preview/download/${selectedTemplateId}`, '_blank');
        }
    });

    // Event listeners para botões de seleção
    selectTemplateBtn.addEventListener('click', function() {
        showConfirmModal();
    });

    selectTemplateTopBtn.addEventListener('click', function() {
        showConfirmModal();
    });

    // Event listeners do modal
    confirmSelectBtn.addEventListener('click', function() {
        selectCurrentTemplate();
    });

    cancelSelectBtn.addEventListener('click', function() {
        hideConfirmModal();
    });

    // Fechar modal clicando fora
    confirmModal.addEventListener('click', function(e) {
        if (e.target === confirmModal) {
            hideConfirmModal();
        }
    });

    async function loadTemplatesList(type) {
        try {
            const response = await fetch(`/dintell/template-preview/list/${type}`);
            const templates = await response.json();
            console.log(templates);
            
            availableTemplates[type] = templates;
            
            templateSelect.innerHTML = '<option value="">Selecione um template</option>';
            
            if (templates.length === 0) {
                templateSelect.innerHTML = '<option value="">Nenhum template encontrado</option>';
                templateSelect.disabled = true;
            } else {
                templates.forEach(template => {
                    const option = document.createElement('option');
                    option.value = template.id;
                    
                    // Indicadores visuais no select
                    let indicator = '';
                    let suffix = '';
                    
                    if (template.is_selected && template.is_default) {
                        indicator = '🔹 ';
                        suffix = ' (Em Uso - Padrão)';
                    } else if (template.is_selected==1) {
                        indicator = '🔹 ';
                        suffix = ' (Em Uso)';
                    } else if (template.is_default==1) {
                        indicator = '';
                        suffix = ' (Padrão)';
                    }
                    console.log(template);
                    
                    option.textContent = `${indicator}${template.name}${suffix}`;
                    templateSelect.appendChild(option);
                });
                templateSelect.disabled = false;
            }
        } catch (error) {
            console.error('Erro ao carregar templates:', error);
            templateSelect.innerHTML = '<option value="">Erro ao carregar templates</option>';
            templateSelect.disabled = true;
        }
    }

    function showInitialState() {
        initialState.classList.remove('hidden');
        loadingState.classList.add('hidden');
        previewContainer.classList.add('hidden');
        previewStatus.textContent = 'Aguardando seleção...';
        templateInfo.innerHTML = '<p>Selecione um template para ver as informações</p>';
        hideSelectButtons();
    }

    function showLoadingState() {
        initialState.classList.add('hidden');
        loadingState.classList.remove('hidden');
        previewContainer.classList.add('hidden');
        previewStatus.textContent = 'Carregando...';
        hideSelectButtons();
    }

    function showPreview() {
        initialState.classList.add('hidden');
        loadingState.classList.add('hidden');
        previewContainer.classList.remove('hidden');
        previewStatus.textContent = 'Preview carregado';
        showSelectButtons();
    }

    function showSelectButtons() {
        if (currentSelectedTemplate && currentSelectedTemplate.is_selected) {
            selectTemplateBtn.classList.add('hidden');
            selectTemplateTopBtn.classList.add('hidden');
            showSelectedIndicator();
        } else {
            selectTemplateBtn.classList.remove('hidden');
            selectTemplateTopBtn.classList.remove('hidden');
            hideSelectedIndicator();
        }
    }

    function hideSelectButtons() {
        selectTemplateBtn.classList.add('hidden');
        selectTemplateTopBtn.classList.add('hidden');
        hideSelectedIndicator();
    }

    function loadTemplate(templateId) {
        showLoadingState();
        const previewUrl = `/dintell/template-preview/preview/${templateId}`;
        templatePreview.src = previewUrl;
        
        templatePreview.onload = function() {
            showPreview();
        };
        
        templatePreview.onerror = function() {
            previewStatus.textContent = 'Erro ao carregar preview';
            showInitialState();
        };
    }

    function updateTemplateInfo(templateId) {
        const currentType = typeSelect.value;
        const templates = availableTemplates[currentType] || [];
        const template = templates.find(t => t.id == templateId);
        currentSelectedTemplate = template;
        
        if (template) {
            const isSelected = template.is_selected;
            const isDefault = template.is_default;
            
            let statusBadge = '';
            let statusText = '';
            let statusColor = '';
            
            if (isSelected && isDefault) {
                statusBadge = '<span class="inline-block px-2 py-1 text-xs font-medium text-blue-800 bg-blue-100 rounded-full mt-1 mr-1">Em Uso</span><span class="inline-block px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full mt-1">Padrão</span>';
                statusText = 'Em Uso (Padrão)';
                statusColor = 'text-blue-600';
            } else if (isSelected) {
                statusBadge = '<span class="inline-block px-2 py-1 text-xs font-medium text-blue-800 bg-blue-100 rounded-full mt-1">Em Uso</span>';
                statusText = 'Em Uso';
                statusColor = 'text-blue-600';
            } else if (isDefault) {
                statusBadge = '<span class="inline-block px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full mt-1">Padrão</span>';
                statusText = 'Padrão';
                statusColor = 'text-green-600';
            } else {
                statusBadge = '<span class="inline-block px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full mt-1">Disponível</span>';
                statusText = 'Disponível';
                statusColor = 'text-gray-600';
            }
            
            templateInfo.innerHTML = `
                <div class="mb-3">
                    <h5 class="font-medium text-gray-900">${template.name}</h5>
                    <p class="text-gray-600 text-xs mt-1">Template de ${currentType === 'invoice' ? 'Fatura' : 'Cotação'}</p>
                    ${statusBadge}
                </div>
                <div class="space-y-2 text-xs">
                    <div>
                        <span class="text-gray-500">ID:</span>
                        <span class="font-medium text-gray-900">${template.id}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Status:</span>
                        <span class="font-medium ${statusColor}">${statusText}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Criado em:</span>
                        <span class="font-medium text-gray-900">${new Date(template.created_at).toLocaleDateString('pt-BR')}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Atualizado em:</span>
                        <span class="font-medium text-gray-900">${new Date(template.updated_at).toLocaleDateString('pt-BR')}</span>
                    </div>
                </div>
            `;
        }
    }

    function showConfirmModal() {
        if (currentSelectedTemplate) {
            modalTemplateName.textContent = currentSelectedTemplate.name;
            modalTemplateType.textContent = typeSelect.value === 'invoice' ? 'faturas' : 'cotações';
            confirmModal.classList.remove('hidden');
        }
    }

    function hideConfirmModal() {
        confirmModal.classList.add('hidden');
    }

    async function selectCurrentTemplate() {
        if (!currentSelectedTemplate) return;

        try {
            const response = await fetch(`/dintell/template-preview/select/${currentSelectedTemplate.id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            const result = await response.json();

            if (response.ok) {
                // Sucesso
                hideConfirmModal();
                
                // Atualizar a interface para mostrar que foi selecionado
                updateTemplateAsSelected();
                
                // Mostrar notificação de sucesso
                showNotification('Template definido como em uso!', 'success');
                
                // Recarregar a lista de templates para atualizar o status
                loadTemplatesList(typeSelect.value);
            } else {
                throw new Error(result.message || 'Erro ao selecionar template');
            }
        } catch (error) {
            console.error('Erro ao selecionar template:', error);
            showNotification('Erro ao selecionar template: ' + error.message, 'error');
        }
    }

    function updateTemplateAsSelected() {
        // Atualizar visualmente que o template foi selecionado para uso
        if (currentSelectedTemplate) {
            currentSelectedTemplate.is_selected = true;
            updateTemplateInfo(currentSelectedTemplate.id);
        }
    }

    function showNotification(message, type = 'success') {
        // Criar notificação simples
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-md shadow-lg ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Remover após 3 segundos
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Estado inicial
    showInitialState();
    
    // Funções para mostrar/esconder indicadores
    function showSelectedIndicator() {
        selectedIndicator.classList.remove('hidden');
        selectedIndicatorSidebar.classList.remove('hidden');
    }
    
    function hideSelectedIndicator() {
        selectedIndicator.classList.add('hidden');
        selectedIndicatorSidebar.classList.add('hidden');
    }
</script>