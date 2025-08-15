{{-- resources/views/components/support-popup.blade.php --}}
@props(['position' => 'bottom-right'])

<!-- Support Popup Component -->
<div id="supportPopup" class="support-popup {{ $position }}">
    <!-- Floating Button -->
    <div id="supportButton" class="support-button" onclick="toggleSupportPopup()">
        <div class="support-button-content">
            <svg class="support-icon active" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12c0 1.54.36 2.98.97 4.29L1 23l6.71-1.97C9.02 21.64 10.46 22 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2zm-1 17h-2v-2h2v2zm2.07-7.75l-.9.92C11.45 12.9 11 13.5 11 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H6c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/>
            </svg>
            <svg class="support-icon close" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
            </svg>
        </div>
        <div class="support-pulse"></div>
    </div>

    <!-- Popup Panel -->
    <div id="supportPanel" class="support-panel">
        <!-- Header -->
        <div class="support-header">
            <div class="support-header-content">
                <div class="support-avatar">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M16 17v2H2v-2s0-4 7-4 7 4 7 4zm-3.5-9.5A3.5 3.5 0 109 4a3.5 3.5 0 003.5 3.5z"/>
                    </svg>
                </div>
                <div class="support-info">
                    <h3>Como podemos ajudar?</h3>
                    <p class="support-status">
                        <span class="status-dot"></span>
                        Suporte online
                    </p>
                </div>
            </div>
            <button class="support-minimize" onclick="toggleSupportPopup()">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13H5v-2h14v2z"/>
                </svg>
            </button>
        </div>

        <!-- Content Area -->
        <div class="support-content">
            <!-- Welcome Screen -->
            <div id="welcomeScreen" class="support-screen active">
                <div class="welcome-content">
                    <div class="welcome-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <h4>Olá! 👋</h4>
                    <p>Estamos aqui para ajudar você. Escolha uma das opções abaixo:</p>

                    <div class="support-options">
                        <button class="support-option" onclick="showTicketForm()">
                            <div class="option-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                </svg>
                            </div>
                            <div class="option-content">
                                <h5>Criar Ticket</h5>
                                <p>Reporte um problema ou solicite ajuda</p>
                            </div>
                        </button>

                        <button class="support-option" onclick="showMyTickets()">
                            <div class="option-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3M19,19H5V5H19V19Z"/>
                                </svg>
                            </div>
                            <div class="option-content">
                                <h5>Meus Tickets</h5>
                                <p>Veja seus tickets existentes</p>
                            </div>
                        </button>

                        <button class="support-option" onclick="showFAQ()">
                            <div class="option-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M11,18H13V16H11V18M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,20C7.59,20 4,16.41 4,12C7.59,4 4,12A10,10 0 0,0 12,2Z"/>
                                </svg>
                            </div>
                            <div class="option-content">
                                <h5>FAQ</h5>
                                <p>Perguntas frequentes</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Ticket Form -->
            <div id="ticketFormScreen" class="support-screen">
                <div class="screen-header">
                    <button class="back-button" onclick="showWelcomeScreen()">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
                        </svg>
                    </button>
                    <h4>Criar Novo Ticket</h4>
                </div>

                <form id="supportTicketForm" class="ticket-form">
                    @csrf
                    <div class="form-group">
                        <label for="ticketCategory">Categoria <span class="text-red-500">*</span></label>
                        <select id="ticketCategory" name="category" required>
                            <option value="">Selecione uma categoria</option>
                            <option value="technical">Problema Técnico</option>
                            <option value="billing">Cobrança/Faturação</option>
                            <option value="general">Geral</option>
                            <option value="feature">Solicitação de Funcionalidade</option>
                            <option value="bug">Relatar Bug</option>
                        </select>
                        <div class="error-message" id="categoryError"></div>
                    </div>

                    <div class="form-group">
                        <label for="ticketPriority">Prioridade <span class="text-red-500">*</span></label>
                        <select id="ticketPriority" name="priority" required>
                            <option value="low">Baixa</option>
                            <option value="normal" selected>Normal</option>
                            <option value="high">Alta</option>
                            <option value="urgent">Urgente</option>
                        </select>
                        <div class="error-message" id="priorityError"></div>
                    </div>

                    <div class="form-group">
                        <label for="ticketSubject">Assunto <span class="text-red-500">*</span></label>
                        <input type="text" id="ticketSubject" name="subject" placeholder="Descreva brevemente o problema" required maxlength="255">
                        <div class="error-message" id="subjectError"></div>
                    </div>

                    <div class="form-group">
                        <label for="ticketDescription">Descrição <span class="text-red-500">*</span></label>
                        <textarea id="ticketDescription" name="description" rows="4" placeholder="Descreva detalhadamente o problema ou sua solicitação" required minlength="10"></textarea>
                        <div class="error-message" id="descriptionError"></div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="showWelcomeScreen()">Cancelar</button>
                        <button type="submit" class="btn-primary">
                            <span class="btn-text">Criar Ticket</span>
                            <span class="btn-loading" style="display: none;">
                                <svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    <path d="M9 12l2 2 4-4"></path>
                                </svg>
                                Enviando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- My Tickets -->
            <div id="myTicketsScreen" class="support-screen">
                <div class="screen-header">
                    <button class="back-button" onclick="showWelcomeScreen()">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
                        </svg>
                    </button>
                    <h4>Meus Tickets</h4>
                </div>

                <div id="ticketsList" class="tickets-list">
                    <div class="loading-container">
                        <div class="loading-spinner"></div>
                        <p>Carregando tickets...</p>
                    </div>
                </div>
            </div>

            <!-- FAQ -->
            <div id="faqScreen" class="support-screen">
                <div class="screen-header">
                    <button class="back-button" onclick="showWelcomeScreen()">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
                        </svg>
                    </button>
                    <h4>Perguntas Frequentes</h4>
                </div>

                <div class="faq-content">
                    <div class="faq-item">
                        <h5>Como criar uma nova fatura?</h5>
                        <p>Para criar uma nova fatura, acesse o menu "Faturas" e clique em "Nova Fatura". Preencha os dados do cliente e adicione os produtos ou serviços.</p>
                    </div>
                    <div class="faq-item">
                        <h5>Como alterar minha senha?</h5>
                        <p>Vá em "Meu Perfil" > "Configurações" > "Alterar Senha". Digite sua senha atual e a nova senha duas vezes.</p>
                    </div>
                    <div class="faq-item">
                        <h5>Como posso acompanhar meus pagamentos?</h5>
                        <p>No dashboard principal, você encontra um resumo dos pagamentos. Para mais detalhes, acesse "Relatórios" > "Financeiro".</p>
                    </div>
                    <div class="faq-item">
                        <h5>Como adicionar novos usuários?</h5>
                        <p>Acesse "Configurações" > "Usuários" > "Adicionar Usuário". Preencha os dados e defina as permissões.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="support-footer">
            <p>Powered by <strong>SFS Support</strong></p>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Support Popup Styles */
.support-popup {
    position: fixed;
    z-index: 9999;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.support-popup.bottom-right {
    bottom: 20px;
    right: 20px;
}

.support-popup.bottom-left {
    bottom: 20px;
    left: 20px;
}

/* Floating Button */
.support-button {
    position: relative;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.support-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
}

.support-button.active {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
}

.support-button-content {
    position: relative;
    width: 24px;
    height: 24px;
}

.support-icon {
    position: absolute;
    top: 0;
    left: 0;
    width: 24px;
    height: 24px;
    color: white;
    transition: all 0.3s ease;
}

.support-icon.close {
    opacity: 0;
    transform: rotate(90deg);
}

.support-button.active .support-icon.active {
    opacity: 0;
    transform: rotate(-90deg);
}

.support-button.active .support-icon.close {
    opacity: 1;
    transform: rotate(0deg);
}

.support-pulse {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 50%;
    background: rgba(102, 126, 234, 0.4);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 0.7;
    }
    70% {
        transform: scale(1.4);
        opacity: 0;
    }
    100% {
        transform: scale(1.4);
        opacity: 0;
    }
}

/* Popup Panel */
.support-panel {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 380px;
    max-height: 600px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    opacity: 0;
    visibility: hidden;
    transform: translateY(20px) scale(0.95);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.support-popup.bottom-left .support-panel {
    left: 0;
    right: auto;
}

.support-panel.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
}

/* Header */
.support-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.support-header-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.support-avatar {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.support-avatar svg {
    width: 20px;
    height: 20px;
    color: white;
}

.support-info h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.support-status {
    margin: 4px 0 0 0;
    font-size: 12px;
    opacity: 0.9;
    display: flex;
    align-items: center;
    gap: 6px;
}

.status-dot {
    width: 8px;
    height: 8px;
    background: #4ade80;
    border-radius: 50%;
    animation: statusPulse 2s infinite;
}

@keyframes statusPulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.support-minimize {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    padding: 8px;
    border-radius: 8px;
    transition: background 0.2s ease;
}

.support-minimize:hover {
    background: rgba(255, 255, 255, 0.1);
}

.support-minimize svg {
    width: 16px;
    height: 16px;
}

/* Content Area */
.support-content {
    position: relative;
    max-height: 450px;
    overflow-y: auto;
}

.support-screen {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    opacity: 0;
    visibility: hidden;
    transform: translateX(100%);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 24px;
    min-height: 400px;
    max-height: 450px;
    overflow-y: auto;
}

.support-screen.active {
    position: relative;
    opacity: 1;
    visibility: visible;
    transform: translateX(0);
}

/* Welcome Screen */
.welcome-content {
    text-align: center;
}

.welcome-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.welcome-icon svg {
    width: 24px;
    height: 24px;
    color: white;
}

.welcome-content h4 {
    margin: 0 0 8px 0;
    font-size: 20px;
    font-weight: 600;
    color: #1f2937;
}

.welcome-content p {
    margin: 0 0 24px 0;
    color: #6b7280;
    font-size: 14px;
    line-height: 1.5;
}

.support-options {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.support-option {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: left;
    width: 100%;
}

.support-option:hover {
    background: #f3f4f6;
    border-color: #667eea;
    transform: translateY(-1px);
}

.option-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.option-icon svg {
    width: 20px;
    height: 20px;
    color: white;
}

.option-content h5 {
    margin: 0 0 4px 0;
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
}

.option-content p {
    margin: 0;
    font-size: 12px;
    color: #6b7280;
    line-height: 1.4;
}

/* Screen Header */
.screen-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e5e7eb;
}

.back-button {
    background: #f3f4f6;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.back-button:hover {
    background: #e5e7eb;
}

.back-button svg {
    width: 16px;
    height: 16px;
    color: #374151;
}

.screen-header h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
}

/* Form Styles */
.ticket-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s ease;
    background: white;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.error-message {
    color: #dc2626;
    font-size: 12px;
    margin-top: 4px;
    display: none;
}

.error-message.show {
    display: block;
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 8px;
}

.btn-primary,
.btn-secondary {
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    flex: 1;
    position: relative;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn-loading {
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Tickets List */
.tickets-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    max-height: 350px;
    overflow-y: auto;
}

.loading-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    color: #6b7280;
}

.loading-spinner {
    width: 20px;
    height: 20px;
    border: 2px solid #e5e7eb;
    border-top: 2px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 12px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.ticket-item {
    padding: 16px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.ticket-item:hover {
    background: #f3f4f6;
    border-color: #667eea;
}

.ticket-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.ticket-number {
    font-size: 12px;
    font-weight: 600;
    color: #667eea;
}

.ticket-status {
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 500;
    text-transform: uppercase;
}

.ticket-subject {
    font-size: 14px;
    font-weight: 500;
    color: #1f2937;
    margin-bottom: 4px;
}

.ticket-meta {
    font-size: 12px;
    color: #6b7280;
}

/* FAQ */
.faq-content {
    display: flex;
    flex-direction: column;
    gap: 16px;
    max-height: 350px;
    overflow-y: auto;
}

.faq-item {
    padding: 16px;
    background: #f9fafb;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

.faq-item h5 {
    margin: 0 0 8px 0;
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
}

.faq-item p {
    margin: 0;
    font-size: 13px;
    color: #6b7280;
    line-height: 1.5;
}

/* Footer */
.support-footer {
    padding: 16px 24px;
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
    text-align: center;
}

.support-footer p {
    margin: 0;
    font-size: 12px;
    color: #6b7280;
}

/* Status Colors */
.status-open { background: #dcfce7; color: #166534; }
.status-pending { background: #fef3c7; color: #92400e; }
.status-resolved { background: #f3e8ff; color: #7c3aed; }
.status-closed { background: #f3f4f6; color: #374151; }

/* Toast Notification */
.support-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
    max-width: 400px;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.support-toast.show {
    opacity: 1;
    transform: translateX(0);
}

.support-toast-content {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1), 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e5e7eb;
}

.support-toast-error .support-toast-content {
    border-left: 4px solid #ef4444;
    background: #fef2f2;
}

.support-toast-success .support-toast-content {
    border-left: 4px solid #10b981;
    background: #f0fdf4;
}

.support-toast-icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
    margin-top: 2px;
}

.support-toast-error .support-toast-icon {
    color: #ef4444;
}

.support-toast-success .support-toast-icon {
    color: #10b981;
}

.support-toast-icon svg {
    width: 100%;
    height: 100%;
}

.support-toast-message {
    flex: 1;
    font-size: 14px;
    line-height: 1.5;
    color: #1f2937;
    margin-right: 8px;
}

.support-toast-close {
    background: none;
    border: none;
    width: 20px;
    height: 20px;
    cursor: pointer;
    color: #6b7280;
    transition: color 0.2s ease;
    flex-shrink: 0;
    padding: 0;
}

.support-toast-close:hover {
    color: #374151;
}

.support-toast-close svg {
    width: 100%;
    height: 100%;
}

/* Responsive */
@media (max-width: 480px) {
    .support-panel {
        width: calc(100vw - 40px);
        right: 20px;
        left: 20px;
    }

    .support-popup.bottom-left .support-panel {
        left: 20px;
        right: 20px;
    }

    .support-toast {
        right: 10px;
        left: 10px;
        max-width: none;
        transform: translateY(-100%);
    }

    .support-toast.show {
        transform: translateY(0);
    }
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}
</style>
@endpush

@push('scripts')
<script>
class SupportPopup {
    constructor() {
        this.isOpen = false;
        this.currentScreen = 'welcome';
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadUserTickets();
    }

    bindEvents() {
        // Form submission
        const form = document.getElementById('supportTicketForm');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitTicket();
            });
        }

        // Close popup when clicking outside
        document.addEventListener('click', (e) => {
            const popup = document.getElementById('supportPopup');
            if (popup && !popup.contains(e.target) && this.isOpen) {
                this.close();
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.close();
            }
        });
    }

    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }

    open() {
        const button = document.getElementById('supportButton');
        const panel = document.getElementById('supportPanel');

        if (button && panel) {
            button.classList.add('active');
            panel.classList.add('active');
            this.isOpen = true;
            this.showScreen('welcome');
        }
    }

    close() {
        const button = document.getElementById('supportButton');
        const panel = document.getElementById('supportPanel');

        if (button && panel) {
            button.classList.remove('active');
            panel.classList.remove('active');
            this.isOpen = false;
        }
    }

    showScreen(screenName) {
        // Hide all screens
        document.querySelectorAll('.support-screen').forEach(screen => {
            screen.classList.remove('active');
        });

        // Show target screen
        const targetScreen = document.getElementById(screenName + 'Screen');
        if (targetScreen) {
            setTimeout(() => {
                targetScreen.classList.add('active');
                this.currentScreen = screenName;

                // Load tickets if switching to my tickets screen
                if (screenName === 'myTickets') {
                    this.loadUserTickets();
                }
            }, 150);
        }
    }

    async submitTicket() {
        const form = document.getElementById('supportTicketForm');
        const formData = new FormData(form);

        // Clear previous errors
        this.clearFormErrors();

        // Validate form
        if (!this.validateForm(formData)) {
            return;
        }

        const submitButton = form.querySelector('button[type="submit"]');
        const btnText = submitButton.querySelector('.btn-text');
        const btnLoading = submitButton.querySelector('.btn-loading');

        // Show loading state
        btnText.style.display = 'none';
        btnLoading.style.display = 'flex';
        submitButton.disabled = true;

        try {
            const response = await fetch('/api/support/tickets', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (response.ok && result.success) {
                this.showSuccessMessage('Ticket criado com sucesso!', result.ticket_number || result.data?.ticket_number);
                form.reset();
                // Reload tickets list for next time
                setTimeout(() => this.loadUserTickets(), 1000);
            } else {
                if (result.errors) {
                    this.showFormErrors(result.errors);
                } else {
                    throw new Error(result.message || 'Erro ao criar ticket');
                }
            }

        } catch (error) {
            console.error('Erro ao enviar ticket:', error);
            this.showToast(error.message || 'Erro ao criar ticket. Tente novamente.', 'error');
        } finally {
            // Reset button state
            btnText.style.display = 'inline';
            btnLoading.style.display = 'none';
            submitButton.disabled = false;
        }
    }

    validateForm(formData) {
        let isValid = true;
        const category = formData.get('category');
        const priority = formData.get('priority');
        const subject = formData.get('subject');
        const description = formData.get('description');

        if (!category) {
            this.showFieldError('categoryError', 'Por favor, selecione uma categoria.');
            isValid = false;
        }

        if (!priority) {
            this.showFieldError('priorityError', 'Por favor, selecione uma prioridade.');
            isValid = false;
        }

        if (!subject || subject.trim().length < 3) {
            this.showFieldError('subjectError', 'O assunto deve ter pelo menos 3 caracteres.');
            isValid = false;
        }

        if (!description || description.trim().length < 10) {
            this.showFieldError('descriptionError', 'A descrição deve ter pelo menos 10 caracteres.');
            isValid = false;
        }

        return isValid;
    }

    showFieldError(fieldId, message) {
        const errorElement = document.getElementById(fieldId);
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.add('show');
        }
    }

    clearFormErrors() {
        document.querySelectorAll('.error-message').forEach(error => {
            error.classList.remove('show');
            error.textContent = '';
        });
    }

    showFormErrors(errors) {
        const fieldMap = {
            'category': 'categoryError',
            'priority': 'priorityError',
            'subject': 'subjectError',
            'description': 'descriptionError'
        };

        Object.keys(errors).forEach(field => {
            const errorId = fieldMap[field];
            if (errorId && errors[field][0]) {
                this.showFieldError(errorId, errors[field][0]);
            }
        });
    }

    async loadUserTickets() {
        const container = document.getElementById('ticketsList');
        if (!container) return;

        // Show loading
        container.innerHTML = `
            <div class="loading-container">
                <div class="loading-spinner"></div>
                <p>Carregando tickets...</p>
            </div>
        `;

        try {
            const response = await fetch('/api/support/tickets/my', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Erro ao carregar tickets');
            }

            const result = await response.json();

            if (result.success) {
                this.renderTickets(result.tickets || result.data || []);
            } else {
                throw new Error(result.message || 'Erro ao carregar tickets');
            }

        } catch (error) {
            console.error('Erro ao carregar tickets:', error);
            container.innerHTML = `
                <div class="loading-container">
                    <svg style="width: 48px; height: 48px; margin-bottom: 16px; opacity: 0.5; color: #ef4444;" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,2C17.53,2 22,6.47 22,12C22,17.53 17.53,22 12,22C6.47,22 2,17.53 2,12C2,6.47 6.47,2 12,2M15.59,7L12,10.59L8.41,7L7,8.41L10.59,12L7,15.59L8.41,17L12,13.41L15.59,17L17,15.59L13.41,12L17,8.41L15.59,7Z"/>
                    </svg>
                    <p>Erro ao carregar tickets</p>
                    <button onclick="supportPopup.loadUserTickets()" class="btn-secondary" style="margin-top: 12px; padding: 8px 16px;">
                        Tentar novamente
                    </button>
                </div>
            `;
        }
    }

    renderTickets(tickets) {
        const container = document.getElementById('ticketsList');
        if (!container) return;

        if (!tickets || tickets.length === 0) {
            container.innerHTML = `
                <div class="loading-container">
                    <svg style="width: 48px; height: 48px; margin-bottom: 16px; opacity: 0.5;" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                    </svg>
                    <p>Você ainda não possui tickets de suporte.</p>
                    <button onclick="showTicketForm()" class="btn-primary" style="margin-top: 12px; padding: 8px 16px;">
                        Criar primeiro ticket
                    </button>
                </div>
            `;
            return;
        }

        container.innerHTML = tickets.map(ticket => `
            <div class="ticket-item animate-fade-in" onclick="supportPopup.viewTicket('${ticket.id}')">
                <div class="ticket-header">
                    <span class="ticket-number">#${ticket.ticket_number || ticket.id}</span>
                    <span class="ticket-status status-${ticket.status || 'open'}">${this.getStatusLabel(ticket.status || 'open')}</span>
                </div>
                <div class="ticket-subject">${ticket.subject || 'Sem assunto'}</div>
                <div class="ticket-meta">
                    ${this.formatDate(ticket.created_at)} • Prioridade: ${this.getPriorityLabel(ticket.priority || 'normal')}
                </div>
            </div>
        `).join('');
    }

    viewTicket(ticketId) {
        // For now, just show a message. In a real app, this would open the ticket details
        this.showToast('Em breve: visualização completa do ticket', 'info');
    }

    showSuccessMessage(message, ticketNumber = null) {
        const content = document.querySelector('#welcomeScreen .welcome-content');
        if (!content) return;

        content.innerHTML = `
            <div class="success-message animate-fade-in" style="text-align: center;">
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                    <svg style="width: 24px; height: 24px; color: white;" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9,20.42L2.79,14.21L5.62,11.38L9,14.77L18.88,4.88L21.71,7.71L9,20.42Z"/>
                    </svg>
                </div>
                <h4 style="color: #065f46; margin-bottom: 8px;">Sucesso! ✅</h4>
                <p style="color: #047857; margin-bottom: 16px;">${message}</p>
                ${ticketNumber ? `<p style="color: #047857; font-weight: 600; margin-bottom: 16px;">Número do ticket: #${ticketNumber}</p>` : ''}
                <button class="btn-primary" onclick="supportPopup.resetWelcomeScreen(); supportPopup.loadUserTickets();" style="margin-top: 16px;">
                    Ok, entendi
                </button>
            </div>
        `;
    }

    resetWelcomeScreen() {
        const content = document.querySelector('#welcomeScreen .welcome-content');
        if (!content) return;

        content.innerHTML = `
            <div class="welcome-icon">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            </div>
            <h4>Olá! 👋</h4>
            <p>Estamos aqui para ajudar você. Escolha uma das opções abaixo:</p>

            <div class="support-options">
                <button class="support-option" onclick="showTicketForm()">
                    <div class="option-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                        </svg>
                    </div>
                    <div class="option-content">
                        <h5>Criar Ticket</h5>
                        <p>Reporte um problema ou solicite ajuda</p>
                    </div>
                </button>

                <button class="support-option" onclick="showMyTickets()">
                    <div class="option-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3M19,19H5V5H19V19Z"/>
                        </svg>
                    </div>
                    <div class="option-content">
                        <h5>Meus Tickets</h5>
                        <p>Veja seus tickets existentes</p>
                    </div>
                </button>

                <button class="support-option" onclick="showFAQ()">
                    <div class="option-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11,18H13V16H11V18M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,20C7.59,20 4,16.41 4,12C7.59,4 4,12A10,10 0 0,0 12,2Z"/>
                        </svg>
                    </div>
                    <div class="option-content">
                        <h5>FAQ</h5>
                        <p>Perguntas frequentes</p>
                    </div>
                </button>
            </div>
        `;
    }

    showToast(message, type = 'info') {
        // Remove existing toasts
        const existingToasts = document.querySelectorAll('.support-toast');
        existingToasts.forEach(toast => toast.remove());

        // Create toast element
        const toast = document.createElement('div');
        toast.className = `support-toast support-toast-${type}`;
        toast.innerHTML = `
            <div class="support-toast-content">
                <div class="support-toast-icon">
                    ${type === 'error' ?
                        '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,2C17.53,2 22,6.47 22,12C22,17.53 17.53,22 12,22C6.47,22 2,17.53 2,12C2,6.47 6.47,2 12,2M15.59,7L12,10.59L8.41,7L7,8.41L10.59,12L7,15.59L8.41,17L12,13.41L15.59,17L17,15.59L13.41,12L17,8.41L15.59,7Z"/></svg>' :
                        type === 'success' ?
                        '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M11,16.5L18,9.5L16.5,8L11,13.5L7.5,10L6,11.5L11,16.5Z"/></svg>' :
                        '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M13,9H11V7H13M13,17H11V11H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/></svg>'
                    }
                </div>
                <div class="support-toast-message">${message}</div>
                <button class="support-toast-close" onclick="this.parentElement.parentElement.remove()">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"/>
                    </svg>
                </button>
            </div>
        `;

        // Add to document
        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => toast.classList.add('show'), 100);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }
        }, 5000);
    }

    getStatusLabel(status) {
        const labels = {
            'open': 'Aberto',
            'pending': 'Pendente',
            'resolved': 'Resolvido',
            'closed': 'Fechado'
        };
        return labels[status] || status;
    }

    getPriorityLabel(priority) {
        const labels = {
            'low': 'Baixa',
            'normal': 'Normal',
            'high': 'Alta',
            'urgent': 'Urgente'
        };
        return labels[priority] || priority;
    }

    formatDate(dateString) {
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        } catch (error) {
            return 'Data inválida';
        }
    }
}

// Initialize support popup
let supportPopup;

document.addEventListener('DOMContentLoaded', function() {
    // Check if support popup exists before initializing
    if (document.getElementById('supportPopup')) {
        supportPopup = new SupportPopup();
    }
});

// Global functions for onclick handlers
function toggleSupportPopup() {
    if (supportPopup) {
        supportPopup.toggle();
    }
}

function showWelcomeScreen() {
    if (supportPopup) {
        supportPopup.showScreen('welcome');
    }
}

function showTicketForm() {
    if (supportPopup) {
        supportPopup.showScreen('ticketForm');
    }
}

function showMyTickets() {
    if (supportPopup) {
        supportPopup.showScreen('myTickets');
    }
}

function showFAQ() {
    if (supportPopup) {
        supportPopup.showScreen('faq');
    }
}
</script>
@endpush
