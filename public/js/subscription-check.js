/**
 * 🚀 Sistema Completo de Verificação de Subscrições
 *
 * Uso básico:
 * new SubscriptionChecker('sk_sua_api_key', 'seudominio.com');
 *
 * Uso avançado:
 * new SubscriptionChecker('sk_sua_api_key', 'seudominio.com', {
 *   checkInterval: 300000,    // 5 minutos
 *   debug: true,             // Mostrar logs
 *   onSuspended: customHandler,
 *   retryAttempts: 3
 * });
 */

class SubscriptionChecker {
    constructor(apiKey, domain, options = {}) {
        // Configurações básicas
        this.apiKey = apiKey;
        this.domain = domain;
        this.apiUrl = options.apiUrl || this.getApiUrl();

        // Configurações de timing
        this.checkInterval = options.checkInterval || 300000; // 5 minutos
        this.retryDelay = options.retryDelay || 30000; // 30 segundos
        this.maxRetries = options.maxRetries || 3;

        // Callbacks
        this.onSuspended = options.onSuspended || this.defaultSuspendedHandler;
        this.onActive = options.onActive || this.defaultActiveHandler;
        this.onError = options.onError || this.defaultErrorHandler;

        // Estado interno
        this.debug = options.debug || false;
        this.retryCount = 0;
        this.isRunning = false;
        this.intervalId = null;
        this.lastCheck = null;
        this.lastStatus = null;

        // Configurações de UI
        this.showStatusIndicator = options.showStatusIndicator !== false;
        this.statusPosition = options.statusPosition || 'bottom-right';

        // Auto-inicializar
        if (options.autoStart !== false) {
            this.init();
        }
    }

    /**
     * Inicializar o sistema de verificação
     */
    async init() {
        if (this.isRunning) {
            this.log('⚠️ Sistema já está rodando');
            return;
        }

        this.log('🚀 Iniciando SubscriptionChecker');
        this.log(`📍 Domínio: ${this.domain}`);
        this.log(`⏱️ Intervalo: ${this.checkInterval / 1000}s`);

        this.isRunning = true;

        // Criar indicador de status se habilitado
        if (this.showStatusIndicator) {
            this.createStatusIndicator();
        }

        // Verificação inicial
        await this.checkSubscription();

        // Configurar verificações periódicas
        this.startPeriodicChecks();

        // Event listeners
        this.setupEventListeners();

        this.log('✅ Sistema inicializado com sucesso');
    }

    /**
     * Obter URL da API baseada no ambiente
     */
    getApiUrl() {
        const baseUrl = window.location.origin;
        return `${baseUrl}/api/v1/subscription/verify`;
    }

    /**
     * Configurar event listeners
     */
    setupEventListeners() {
        // Verificar quando a página voltar ao foco
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && this.isRunning) {
                this.log('👁️ Página voltou ao foco, verificando status...');
                this.checkSubscription();
            }
        });

        // Verificar quando a conexão voltar online
        window.addEventListener('online', () => {
            this.log('🌐 Conexão restaurada, verificando status...');
            this.retryCount = 0; // Reset retry count
            this.checkSubscription();
        });

        // Pausar verificações quando offline
        window.addEventListener('offline', () => {
            this.log('📡 Conexão perdida, pausando verificações...');
        });
    }

    /**
     * Iniciar verificações periódicas
     */
    startPeriodicChecks() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
        }

        this.intervalId = setInterval(() => {
            if (navigator.onLine) {
                this.checkSubscription();
            } else {
                this.log('📡 Offline, pulando verificação');
            }
        }, this.checkInterval);
    }

    /**
     * Verificar status da subscrição
     */
    async checkSubscription() {
        if (!navigator.onLine) {
            this.log('📡 Sem conexão, pulando verificação');
            return;
        }

        this.updateStatusIndicator('checking');
        this.lastCheck = new Date();

        try {
            this.log('🔍 Verificando subscrição...');

            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    api_key: this.apiKey,
                    domain: this.domain,
                    timestamp: Date.now()
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            this.log('📡 Resposta da API:', data);

            // Reset retry count em caso de sucesso
            this.retryCount = 0;

            // Processar resposta
            await this.processApiResponse(data);

        } catch (error) {
            this.handleError(error);
        }
    }

    /**
     * Processar resposta da API
     */
    async processApiResponse(data) {
        const oldStatus = this.lastStatus;
        this.lastStatus = data.status;

        // Log mudança de status
        if (oldStatus && oldStatus !== data.status) {
            this.log(`🔄 Status mudou: ${oldStatus} → ${data.status}`);
        }

        switch (data.status) {
            case 'active':
                this.updateStatusIndicator('active');
                this.onActive(data);
                break;

            case 'blocked':
            case 'suspended':
            case 'not_found':
                this.updateStatusIndicator('blocked');
                this.onSuspended(data);
                break;

            default:
                this.log('⚠️ Status desconhecido:', data.status);
                this.updateStatusIndicator('unknown');
        }
    }

    /**
     * Manipular erros
     */
    handleError(error) {
        this.log('❌ Erro na verificação:', error.message);
        this.updateStatusIndicator('error');

        this.retryCount++;

        if (this.retryCount <= this.maxRetries) {
            this.log(`🔄 Tentativa ${this.retryCount}/${this.maxRetries} em ${this.retryDelay / 1000}s...`);
            setTimeout(() => this.checkSubscription(), this.retryDelay);
        } else {
            this.log('💥 Máximo de tentativas atingido');
            this.onError(error);
        }
    }

    /**
     * Handler padrão para status ativo
     */
    defaultActiveHandler(data) {
        this.log('✅ Subscrição ativa:', data.subscription);

        // Remover overlay de suspensão se existir
        this.removeOverlay();

        // Mostrar informações em modo debug
        if (this.debug && data.subscription) {
            this.showSubscriptionInfo(data.subscription);
        }
    }

    /**
     * Handler padrão para subscrição suspensa
     */
    defaultSuspendedHandler(data) {
        this.log('🚫 Acesso bloqueado:', data.reason || data.message);

        if (data.redirect_url) {
            this.log('🔄 Redirecionando para:', data.redirect_url);
            window.location.href = data.redirect_url;
        } else {
            this.log('🎨 Criando página de suspensão inline');
            this.createInlineSuspensionPage(data);
        }
    }

    /**
     * Handler padrão para erros
     */
    defaultErrorHandler(error) {
        this.log('⚠️ Sistema em modo de erro:', error.message);

        // Opcionalmente mostrar notificação de erro
        if (this.debug) {
            this.showErrorNotification(error);
        }
    }

    /**
     * Criar página de suspensão inline
     */
    createInlineSuspensionPage(data) {
        // Remove overlay existente
        this.removeOverlay();

        const overlay = document.createElement('div');
        overlay.id = 'subscription-overlay';
        overlay.innerHTML = this.getSuspensionPageHTML(data);

        document.body.appendChild(overlay);

        // Adicionar animação de entrada
        setTimeout(() => {
            overlay.style.opacity = '1';
        }, 10);
    }

    /**
     * Gerar HTML da página de suspensão
     */