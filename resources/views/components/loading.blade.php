
{{-- Sistema de Loading Global - SFS --}}

<!-- Loading Overlay Global -->
<div id="globalLoading" class="hidden sfs-loading-overlay">

    <!-- Container principal do loading -->
    <div class="sfs-loading-container">
        <!-- Logo da SFS -->
        <div class="sfs-loading-logo">
            <img src="{{ asset('akaunting-loading.gif') }}" alt="SFS Logo" class="sfs-logo-image">
        </div>

</div>

{{-- @push('styles') --}}
<style>
/* ===============================================
   SFS LOADING SYSTEM - ESTILOS PRINCIPAIS
   =============================================== */

.sfs-loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
    overflow: hidden;
}

.sfs-loading-overlay.hidden {
    opacity: 0;
    visibility: hidden;
}

/* Background animado */
.sfs-loading-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 1;
}

.sfs-floating-element {
    position: absolute;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: sfs-float 6s ease-in-out infinite;
}

.sfs-floating-element:nth-child(1) {
    width: 80px;
    height: 80px;
    top: 10%;
    left: 10%;
    animation-delay: 0s;
}

.sfs-floating-element:nth-child(2) {
    width: 60px;
    height: 60px;
    top: 70%;
    right: 20%;
    animation-delay: 2s;
}

.sfs-floating-element:nth-child(3) {
    width: 40px;
    height: 40px;
    top: 30%;
    right: 10%;
    animation-delay: 4s;
}

.sfs-floating-element:nth-child(4) {
    width: 100px;
    height: 100px;
    bottom: 20%;
    left: 20%;
    animation-delay: 1s;
}

@keyframes sfs-float {
    0%, 100% {
        transform: translateY(0px) rotate(0deg);
        opacity: 0.3;
    }
    50% {
        transform: translateY(-30px) rotate(180deg);
        opacity: 0.8;
    }
}

/* Container principal */
.sfs-loading-container {
    position: relative;
    z-index: 10;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

/* Logo e Spinner */
.sfs-loading-logo {
    position: relative;
    width: 120px;
    height: 120px;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sfs-logo-image {
    width: 100px;
    height: 100px;
    object-fit: contain;
    border-radius: 50%;

    padding: 10px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    z-index: 2;
}

.sfs-loading-spinner {
    position: absolute;
    top: 0;
    left: 0;
    width: 120px;
    height: 120px;
    border: 4px solid rgba(255, 255, 255, 0.3);
    border-top: 4px solid #ffffff;
    border-radius: 50%;
    animation: sfs-spin 2s linear infinite;
    z-index: 1;
}

@keyframes sfs-spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Título */
.sfs-loading-title {
    margin-bottom: 2rem;
    color: white;
}

.sfs-loading-title h2 {
    font-size: 3rem;
    font-weight: bold;
    margin: 0;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    letter-spacing: 2px;
}

.sfs-loading-title p {
    font-size: 1rem;
    margin: 0.5rem 0 0 0;
    opacity: 0.9;
    font-weight: 300;
}

/* Mensagens rotativas */
.sfs-loading-messages {
    margin-bottom: 2rem;
    height: 30px;
    position: relative;
}

.sfs-message {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.1rem;
    font-weight: 500;
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
    white-space: nowrap;
}

.sfs-message.active {
    opacity: 1;
}

/* Barra de progresso */
.sfs-progress-container {
    width: 300px;
    margin-bottom: 2rem;
}

.sfs-progress-bar {
    width: 100%;
    height: 6px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.sfs-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #ffffff, #f0f0f0);
    border-radius: 3px;
    width: 0%;
    animation: sfs-progress 3s ease-in-out infinite;
}

@keyframes sfs-progress {
    0% { width: 0%; }
    50% { width: 70%; }
    100% { width: 100%; }
}

.sfs-progress-text {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    text-align: center;
}

/* Footer */
.sfs-loading-footer {
    position: absolute;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    text-align: center;
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.85rem;
    z-index: 10;
}

.sfs-version {
    margin-top: 0.5rem;
    font-size: 0.75rem;
    opacity: 0.6;
}

/* ===============================================
   STATES PARA BOTÕES E FORMULÁRIOS
   =============================================== */

.btn-loading {
    position: relative;
    pointer-events: none;
    opacity: 0.7;
}

.btn-loading::after {
    content: '';
    position: absolute;
    left: 50%;
    top: 50%;
    width: 16px;
    height: 16px;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: sfs-spin 0.8s linear infinite;
}

/* ===============================================
   RESPONSIVIDADE
   =============================================== */

@media (max-width: 768px) {
    .sfs-loading-logo {
        width: 100px;
        height: 100px;
    }

    .sfs-logo-image {
        width: 70px;
        height: 70px;
    }

    .sfs-loading-spinner {
        width: 100px;
        height: 100px;
    }

    .sfs-loading-title h2 {
        font-size: 2.5rem;
    }

    .sfs-progress-container {
        width: 250px;
    }

    .sfs-floating-element:nth-child(1),
    .sfs-floating-element:nth-child(4) {
        width: 60px;
        height: 60px;
    }
}

@media (max-width: 480px) {
    .sfs-loading-title h2 {
        font-size: 2rem;
    }

    .sfs-progress-container {
        width: 200px;
    }

    .sfs-message {
        font-size: 1rem;
    }
}

/* ===============================================
   LOADING PARA DARK MODE (se necessário)
   =============================================== */

@media (prefers-color-scheme: dark) {
    .sfs-loading-overlay {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    }

    .sfs-logo-image {
        background: rgba(255, 255, 255, 0.95);
    }
}
</style>
{{-- @endpush --}}

{{-- @push('scripts') --}}
<script>
/**
 * SFS Loading System - JavaScript
 * Integração com Laravel
 */

class SFSLoading {
    constructor() {
        this.overlay = null;
        this.isVisible = false;
        this.messageIndex = 0;
        this.messageInterval = null;
        this.progressInterval = null;
        this.init();
    }

    init() {
        this.overlay = document.getElementById('globalLoading');
        this.setupEventListeners();
        this.rotateMessages();
    }

    show(message = null) {
        if (!this.overlay || this.isVisible) return;

        this.isVisible = true;

        // Atualizar mensagem se fornecida
        if (message) {
            this.setMessage(message);
        }

        this.overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Trigger evento customizado
        window.dispatchEvent(new CustomEvent('sfs:loading:show'));
    }

    hide() {
        if (!this.overlay || !this.isVisible) return;

        this.isVisible = false;
        this.overlay.classList.add('hidden');
        document.body.style.overflow = 'auto';

        // Trigger evento customizado
        window.dispatchEvent(new CustomEvent('sfs:loading:hide'));
    }

    setMessage(text) {
        const activeMessage = this.overlay.querySelector('.sfs-message.active');
        if (activeMessage) {
            activeMessage.textContent = text;
        }
    }

    rotateMessages() {
        if (!this.overlay) return;

        const messages = this.overlay.querySelectorAll('.sfs-message');
        if (messages.length <= 1) return;

        this.messageInterval = setInterval(() => {
            messages[this.messageIndex].classList.remove('active');
            this.messageIndex = (this.messageIndex + 1) % messages.length;
            messages[this.messageIndex].classList.add('active');
        }, 2500);
    }

    setupEventListeners() {
        // Interceptar formulários
        document.addEventListener('submit', (e) => {
            const form = e.target;

            // Ignorar formulários com atributo data-no-loading
            if (form.hasAttribute('data-no-loading')) return;

            // Adicionar loading ao botão de submit
            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn) {
                this.setButtonLoading(submitBtn);
            }

            this.show('Enviando formulário...');
        });

        // Interceptar links internos
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href]');

            if (!link) return;
            if (link.hasAttribute('data-no-loading')) return;
            if (link.target === '_blank') return;
            if (link.href.includes('#')) return;
            if (link.href.includes('javascript:')) return;
            if (link.href.includes('mailto:')) return;
            if (link.href.includes('tel:')) return;

            // Apenas links internos
            try {
                const url = new URL(link.href);
                if (url.hostname === window.location.hostname) {
                    this.show('Carregando página...');
                }
            } catch (e) {
                // URL inválida, ignorar
            }
        });

        // Auto-hide em caso de erro
        window.addEventListener('load', () => {
            setTimeout(() => {
                if (this.isVisible) {
                    this.hide();
                }
            }, 30000); // 30 segundos
        });

        // Loading para requisições AJAX/Fetch
        this.interceptAjax();
    }

    setButtonLoading(button) {
        if (!button) return;

        const originalText = button.textContent || button.value;
        button.setAttribute('data-original-text', originalText);
        button.classList.add('btn-loading');
        button.disabled = true;

        if (button.tagName === 'BUTTON') {
            button.textContent = 'Carregando...';
        } else {
            button.value = 'Carregando...';
        }
    }

    restoreButton(button) {
        if (!button) return;

        const originalText = button.getAttribute('data-original-text');
        if (originalText) {
            if (button.tagName === 'BUTTON') {
                button.textContent = originalText;
            } else {
                button.value = originalText;
            }
        }

        button.classList.remove('btn-loading');
        button.disabled = false;
        button.removeAttribute('data-original-text');
    }

    interceptAjax() {
        // Interceptar Fetch
        const originalFetch = window.fetch;
        const self = this;

        window.fetch = function(...args) {
            self.show('Carregando dados...');

            return originalFetch.apply(this, args)
                .then(response => {
                    self.hide();
                    return response;
                })
                .catch(error => {
                    self.hide();
                    throw error;
                });
        };

        // Interceptar XMLHttpRequest
        const originalOpen = XMLHttpRequest.prototype.open;
        const originalSend = XMLHttpRequest.prototype.send;

        XMLHttpRequest.prototype.open = function(...args) {
            this._shouldShowLoading = true;
            return originalOpen.apply(this, args);
        };

        XMLHttpRequest.prototype.send = function(...args) {
            if (this._shouldShowLoading) {
                self.show('Processando...');

                this.addEventListener('loadend', () => {
                    self.hide();
                });
            }

            return originalSend.apply(this, args);
        };
    }

    // Método para uso manual
    async withLoading(asyncFunction, message = 'Processando...') {
        this.show(message);

        try {
            const result = await asyncFunction();
            return result;
        } catch (error) {
            console.error('Erro durante operação:', error);
            throw error;
        } finally {
            this.hide();
        }
    }

    destroy() {
        if (this.messageInterval) {
            clearInterval(this.messageInterval);
        }
        if (this.progressInterval) {
            clearInterval(this.progressInterval);
        }
        this.hide();
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    window.SFSLoading = new SFSLoading();

    // Métodos globais para compatibilidade
    window.showLoading = (message) => window.SFSLoading.show(message);
    window.hideLoading = () => window.SFSLoading.hide();
});

// Para uso em AJAX com jQuery (se disponível)
if (typeof $ !== 'undefined') {
    $(document).ready(function() {
        $.ajaxSetup({
            beforeSend: function() {
                window.SFSLoading.show('Carregando dados...');
            },
            complete: function() {
                window.SFSLoading.hide();
            },
            error: function() {
                window.SFSLoading.hide();
            }
        });
    });
}
</script>
{{-- @endpush --}}
