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
                      <div class="flex items-center space-x-2">
                          <span class="text-sm text-gray-500" id="previewStatus">Aguardando seleção...</span>
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

<script>
        const typeSelect = document.getElementById('typeSelect');
        const templateSelect = document.getElementById('templateSelect');
        const refreshBtn = document.getElementById('refreshPreview');
        const downloadBtn = document.getElementById('downloadPdf');
        const previewStatus = document.getElementById('previewStatus');
        const templateInfo = document.getElementById('templateInfo');
        const initialState = document.getElementById('initialState');
        const loadingState = document.getElementById('loadingState');
        const previewContainer = document.getElementById('previewContainer');
        const templatePreview = document.getElementById('templatePreview');

        // Dados dos templates (serão carregados via AJAX)
        let availableTemplates = {};

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
                window.open(`/dintell/template-preview/preview/${selectedTemplateId}/download`, '_blank');
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
                        option.textContent = `${template.name}${template.is_default ? ' (Padrão)' : ''}`;
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
        }

        function showLoadingState() {
            initialState.classList.add('hidden');
            loadingState.classList.remove('hidden');
            previewContainer.classList.add('hidden');
            previewStatus.textContent = 'Carregando...';
        }

        function showPreview() {
            initialState.classList.add('hidden');
            loadingState.classList.add('hidden');
            previewContainer.classList.remove('hidden');
            previewStatus.textContent = 'Preview carregado';
        }

        function loadTemplate(templateId) {
            showLoadingState();
            
            // Carregar preview do template
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
            
            if (template) {
                templateInfo.innerHTML = `
                    <div class="mb-3">
                        <h5 class="font-medium text-gray-900">${template.name}</h5>
                        <p class="text-gray-600 text-xs mt-1">Template de ${currentType === 'invoice' ? 'Fatura' : 'Cotação'}</p>
                        ${template.is_default ? '<span class="inline-block px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full mt-1">Padrão</span>' : ''}
                    </div>
                    <div class="space-y-2 text-xs">
                        <div>
                            <span class="text-gray-500">ID:</span>
                            <span class="font-medium text-gray-900">${template.id}</span>
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

        // Estado inicial
        showInitialState();
    </script>
