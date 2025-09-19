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
            <div class="support-header-actions">
                <button id="expandButton" class="support-expand" onclick="toggleExpand()" title="Expandir para tela cheia">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
                    </svg>
                </button>
                <button class="support-minimize" onclick="toggleSupportPopup()">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13H5v-2h14v2z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Content Area -->
        <div class="support-content" id="supportContent">
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
                        <button type="submit" class="btn-primary" id="submitTicketBtn">
                            <span class="btn-text">Criar Ticket</span>
                            <span class="btn-loading">
                                <svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
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

            <!-- Ticket Chat -->
            <div id="ticketChatScreen" class="support-screen">
                <div class="screen-header">
                    <button class="back-button" onclick="showMyTickets()">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
                        </svg>
                    </button>
                    <div class="ticket-chat-header">
                        <h4 id="chatTicketTitle">Ticket #123</h4>
                        <span id="chatTicketStatus" class="ticket-status">Aberto</span>
                    </div>
                </div>

                <div class="ticket-chat-container">
                    <div id="chatMessages" class="chat-messages">
                        <!-- Messages will be loaded here -->
                    </div>

                    <div id="chatReplyForm" class="chat-reply-form">
                        <form id="replyForm">
                            @csrf
                            <div class="reply-input-container">
                                <textarea id="replyMessage" name="message" placeholder="Digite sua resposta..." required minlength="5"></textarea>
                                <button type="submit" class="send-button">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M2,21L23,12L2,3V10L17,12L2,14V21Z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="error-message" id="replyError"></div>
                        </form>
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

<!-- Toast Container -->
<div id="toastContainer" class="toast-container"></div>

<style>
    /* Global Styles */
.support-popup {
    position: fixed;
    z-index: 9999;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    transition: all 0.3s ease;
}

.support-popup.bottom-right { bottom: 20px; right: 20px; }
.support-popup.bottom-left { bottom: 20px; left: 20px; }

/* Expanded State */
.support-popup.expanded {
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 10000;
}

.support-popup.expanded .support-panel {
    width: 100vw;
    height: 100vh;
    max-height: 100vh;
    border-radius: 0;
    bottom: 0;
    right: 0;
    transform: none;
}

.support-popup.expanded .support-content {
    max-height: calc(100vh - 140px);
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
    pointer-events: auto; /* Garante que o botão seja sempre clicável */
}

.support-popup.expanded .support-button { display: none; }

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
    0% { transform: scale(1); opacity: 0.7; }
    70% { transform: scale(1.4); opacity: 0; }
    100% { transform: scale(1.4); opacity: 0; }
}

/* Popup Panel */
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
    pointer-events: none; /* Desativa cliques quando o painel está fechado */
}

.support-popup.bottom-left .support-panel { left: 0; right: auto; }
.support-panel.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
    pointer-events: auto; /* Reativa cliques quando o painel está aberto */
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

.support-header-actions {
    display: flex;
    gap: 8px;
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

.support-avatar svg { width: 20px; height: 20px; color: white; }

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

.support-expand,
.support-minimize {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    padding: 8px;
    border-radius: 8px;
    transition: background 0.2s ease;
}

.support-expand:hover,
.support-minimize:hover { background: rgba(255, 255, 255, 0.1); }

.support-expand svg,
.support-minimize svg { width: 16px; height: 16px; }

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
.welcome-content { text-align: center; }

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

.welcome-icon svg { width: 24px; height: 24px; color: white; }

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

.option-icon svg { width: 20px; height: 20px; color: white; }

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

.ticket-chat-header {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
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

.back-button:hover { background: #e5e7eb; }
.back-button svg { width: 16px; height: 16px; color: #374151; }

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

.form-group textarea { resize: vertical; min-height: 80px; }

.error-message {
    color: #dc2626;
    font-size: 12px;
    margin-top: 4px;
    display: none;
}

.error-message.show { display: block; }

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

.btn-secondary:hover { background: #e5e7eb; }

.btn-loading {
    display: none;
    align-items: center;
    gap: 8px;
}

.btn-loading.show { display: flex; }
.btn-text.hide { display: none; }

/* Tickets List */
.tickets-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    max-height: 350px;
    overflow-y: auto;
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
    transform: translateY(-1px);
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

/* Chat Styles */
.ticket-chat-container {
    display: flex;
    flex-direction: column;
    height: calc(100% - 80px);
    min-height: 300px;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    background: #f8fafc;
    max-height: 300px;
}

.support-popup.expanded .chat-messages { max-height: calc(100vh - 300px); }

.chat-message {
    display: flex;
    gap: 12px;
    animation: fadeInUp 0.3s ease;
}

.chat-message.admin { flex-direction: row; }
.chat-message.user { flex-direction: row-reverse; }

.message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 12px;
    font-weight: 600;
}

.message-avatar.admin { background: #8b5cf6; color: white; }
.message-avatar.user { background: #3b82f6; color: white; }

.message-content {
    flex: 1;
    max-width: 70%;
}

.message-bubble {
    padding: 12px 16px;
    border-radius: 16px;
    word-wrap: break-word;
    line-height: 1.4;
}

.message-bubble.admin {
    background: white;
    border: 1px solid #e5e7eb;
    color: #374151;
}

.message-bubble.user {
    background: #3b82f6;
    color: white;
}

.message-time {
    font-size: 11px;
    color: #9ca3af;
    margin-top: 4px;
}

.chat-reply-form {
    padding: 16px;
    border-top: 1px solid #e5e7eb;
    background: white;
}

.reply-input-container {
    display: flex;
    gap: 8px;
    align-items: flex-end;
}

.reply-input-container textarea {
    flex: 1;
    min-height: 40px;
    max-height: 120px;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 20px;
    resize: none;
    font-size: 14px;
    transition: all 0.2s ease;
}

.reply-input-container textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.send-button {
    width: 40px;
    height: 40px;
    background: #667eea;
    border: none;
    border-radius: 50%;
    color: white;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.send-button:hover {
    background: #5a67d8;
    transform: scale(1.05);
}

.send-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.send-button svg { width: 18px; height: 18px; }

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
.status-open { background: #fef2f2; color: #991b1b; }
.status-pending { background: #fffbeb; color: #92400e; }
.status-resolved { background: #f0fdf4; color: #166534; }
.status-closed { background: #f3f4f6; color: #374151; }

/* Toast Notifications */
.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10001;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.toast {
    max-width: 400px;
    padding: 16px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    border-left: 4px solid #667eea;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.3s ease;
}

.toast.show {
    opacity: 1;
    transform: translateX(0);
}

.toast.success { border-left-color: #10b981; }
.toast.error { border-left-color: #ef4444; }
.toast.warning { border-left-color: #f59e0b; }

.toast-content {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.toast-icon { width: 20px; height: 20px; flex-shrink: 0; }

.toast-message {
    flex: 1;
    font-size: 14px;
    color: #374151;
}

.toast-close {
    background: none;
    border: none;
    width: 20px;
    height: 20px;
    cursor: pointer;
    color: #9ca3af;
    padding: 0;
}

.toast-close:hover { color: #6b7280; }

/* Loading Spinner */
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
}

/* Animations */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.animate-fade-in { animation: fadeIn 0.3s ease-out; }
.animate-spin { animation: spin 1s linear infinite; }

/* Scrollbar */
.support-content::-webkit-scrollbar,
.chat-messages::-webkit-scrollbar { width: 6px; }

.support-content::-webkit-scrollbar-track,
.chat-messages::-webkit-scrollbar-track { background: #f1f5f9; }

.support-content::-webkit-scrollbar-thumb,
.chat-messages::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.support-content::-webkit-scrollbar-thumb:hover,
.chat-messages::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<script>
class SupportPopup {
    constructor() {
        this.isOpen = false;
        this.isExpanded = false;
        this.currentScreen = 'welcome';
        this.currentTicketId = null;
        this.init();
    }

    // Initialize event listeners and load initial data
    init() {
        this.bindEvents();
        this.loadUserTickets();
    }

    // Bind all event listeners
    bindEvents() {
        // Form submission
        const form = document.getElementById('supportTicketForm');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitTicket();
            });
        }

        // Reply form submission
        const replyForm = document.getElementById('replyForm');
        if (replyForm) {
            replyForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitReply();
            });
        }

        // Auto-resize reply textarea
        const replyTextarea = document.getElementById('replyMessage');
        if (replyTextarea) {
            replyTextarea.addEventListener('input', this.autoResizeTextarea.bind(this));
        }

        // Close popup when clicking outside (except in expanded mode)
        document.addEventListener('click', (e) => {
            const popup = document.getElementById('supportPopup');
            if (popup && !popup.contains(e.target) && this.isOpen && !this.isExpanded) {
                this.close();
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (this.isExpanded) {
                    this.toggleExpand();
                } else if (this.isOpen) {
                    this.close();
                }
            }
        });
    }

    // Toggle popup visibility
    toggle() {
        this.isOpen ? this.close() : this.open();
    }

    // Open popup
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

    // Close popup
    close() {
        const button = document.getElementById('supportButton');
        const panel = document.getElementById('supportPanel');
        const popup = document.getElementById('supportPopup');
        if (button && panel && popup) {
            button.classList.remove('active');
            panel.classList.remove('active');
            popup.classList.remove('expanded');
            this.isOpen = false;
            this.isExpanded = false;
        }
    }

    // Toggle expanded mode
    toggleExpand() {
        const popup = document.getElementById('supportPopup');
        if (popup) {
            this.isExpanded = !this.isExpanded;
            popup.classList.toggle('expanded', this.isExpanded);
            const content = document.getElementById('supportContent');
            if (content) {
                content.style.maxHeight = this.isExpanded ? 'calc(100vh - 140px)' : '450px';
            }
        }
    }

    // Show specific screen
    showScreen(screenName) {
        document.querySelectorAll('.support-screen').forEach(screen => {
            screen.classList.remove('active');
        });

        const targetScreen = document.getElementById(screenName + 'Screen');
        if (targetScreen) {
            setTimeout(() => {
                targetScreen.classList.add('active');
                this.currentScreen = screenName;
                if (screenName === 'myTickets') {
                    this.loadUserTickets();
                }
            }, 150);
        }
    }

    // Submit ticket form
    async submitTicket() {
        const form = document.getElementById('supportTicketForm');
        const formData = new FormData(form);
        const submitButton = document.getElementById('submitTicketBtn');
        const btnText = submitButton.querySelector('.btn-text');
        const btnLoading = submitButton.querySelector('.btn-loading');

        this.clearFormErrors();
        if (!this.validateTicketForm(formData)) return;

        btnText.classList.add('hide');
        btnLoading.classList.add('show');
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

            // if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            try {
                if (response.ok)
                {

                } 
            } catch (error) {
                console.log(error);
                
            }
            const result = await response.json();

            if (result.success) {
                this.showSuccessMessage('Ticket criado com sucesso!', result.ticket_number || result.data?.ticket_number);
                form.reset();
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
            btnText.classList.remove('hide');
            btnLoading.classList.remove('show');
            submitButton.disabled = false;
        }
    }

    // Submit reply to ticket
    async submitReply() {
        if (!this.currentTicketId) return;

        const form = document.getElementById('replyForm');
        const formData = new FormData(form);
        const message = formData.get('message')?.trim();
        const sendButton = form.querySelector('.send-button');

        if (!message || message.length < 5) {
            this.showFieldError('replyError', 'A mensagem deve ter pelo menos 5 caracteres.');
            return;
        }

        sendButton.disabled = true;

        try {
            const response = await fetch(`/api/support/tickets/${this.currentTicketId}/reply`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                form.reset();
                this.loadTicketChat(this.currentTicketId);
                this.showToast('Resposta enviada com sucesso!', 'success');
            } else {
                throw new Error(result.message || 'Erro ao enviar resposta');
            }
        } catch (error) {
            console.error('Erro ao enviar resposta:', error);
            this.showToast(error.message || 'Erro ao enviar resposta', 'error');
        } finally {
            sendButton.disabled = false;
        }
    }

    // Validate ticket form
    validateTicketForm(formData) {
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

    // Show field error
    showFieldError(fieldId, message) {
        const errorElement = document.getElementById(fieldId);
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.add('show');
        }
    }

    // Clear form errors
    clearFormErrors() {
        document.querySelectorAll('.error-message').forEach(error => {
            error.classList.remove('show');
            error.textContent = '';
        });
    }

    // Show form errors
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

    // Load user tickets
    async loadUserTickets() {
        const container = document.getElementById('ticketsList');
        if (!container) return;

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

            if (!response.ok) throw new Error('Erro ao carregar tickets');
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
                    <div style="color: #ef4444; text-align: center;">
                        <p>Erro ao carregar tickets</p>
                        <button onclick="supportPopup.loadUserTickets()" class="btn-secondary" style="margin-top: 12px; padding: 8px 16px;">
                            Tentar novamente
                        </button>
                    </div>
                </div>
            `;
        }
    }

    // Render tickets list
    renderTickets(tickets) {
        const container = document.getElementById('ticketsList');
        if (!container) return;

        if (!tickets || tickets.length === 0) {
            container.innerHTML = `
                <div class="loading-container">
                    <div style="text-align: center; color: #6b7280;">
                        <p>Você ainda não possui tickets de suporte.</p>
                        <button onclick="showTicketForm()" class="btn-primary" style="margin-top: 12px; padding: 8px 16px;">
                            Criar primeiro ticket
                        </button>
                    </div>
                </div>
            `;
            return;
        }

        container.innerHTML = tickets.map(ticket => `
            <div class="ticket-item animate-fade-in" onclick="supportPopup.openTicketChat('${ticket.id}')">
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

    // Open ticket chat
    async openTicketChat(ticketId) {
        this.currentTicketId = ticketId;
        this.showScreen('ticketChat');

        try {
            await this.loadTicketChat(ticketId);
        } catch (error) {
            console.log(error);
               
        }
    }

    // Load ticket chat
    async loadTicketChat(ticketId) {
        const chatContainer = document.getElementById('chatMessages');
        const titleElement = document.getElementById('chatTicketTitle');
        const statusElement = document.getElementById('chatTicketStatus');
        if (!chatContainer) return;

        chatContainer.innerHTML = `
            <div class="loading-container">
                <div class="loading-spinner"></div>
                <p>Carregando conversa...</p>
            </div>
        `;

        try {
            const response = await fetch(`/api/support/tickets/${ticketId}/{{ auth()->user()->id }}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            //debugger;
            console.log(response);
            // if (!response.ok) throw new Error('Erro ao carregar ticket');
            
            try {
                if (response.ok){

                }
            } catch (error) {
                console.log(error);
                
            }
            const result = await response.json();

            if (result.success && result.ticket) {
                const ticket = result.ticket;
                if (titleElement) {
                    titleElement.textContent = `Ticket #${ticket.ticket_number || ticket.id}`;
                }
                if (statusElement) {
                    statusElement.textContent = this.getStatusLabel(ticket.status);
                    statusElement.className = `ticket-status status-${ticket.status}`;
                }
                this.renderChatMessages(ticket, chatContainer);
            } else {
                throw new Error(result.message || 'Erro ao carregar ticket');
            }
        } catch (error) {
            console.error('Erro ao carregar chat:', error);
            chatContainer.innerHTML = `
                <div class="loading-container">
                    <div style="color: #ef4444; text-align: center;">
                        <p>Erro ao carregar conversa</p>
                        <button onclick="supportPopup.loadTicketChat('${ticketId}')" class="btn-secondary" style="margin-top: 12px; padding: 8px 16px;">
                            Tentar novamente
                        </button>
                    </div>
                </div>
            `;
        }
    }

    // Render chat messages
    renderChatMessages(ticket, container) {
        const messages = [{
            id: 'original',
            message: ticket.description,
            user: ticket.user || { name: 'Você' },
            created_at: ticket.created_at,
            is_admin: false
        }];

        if (ticket.replies && ticket.replies.length > 0) {
            messages.push(...ticket.replies);
        }

        if (messages.length === 0) {
            container.innerHTML = '<div class="loading-container"><p>Nenhuma mensagem ainda.</p></div>';
            return;
        }

        container.innerHTML = messages.map(message => {
            const isAdmin = message.is_admin || false;
            const userName = message.user?.name || 'Usuário';
            const userInitial = userName.charAt(0).toUpperCase();
            return `
                <div class="chat-message ${isAdmin ? 'admin' : 'user'}">
                    <div class="message-avatar ${isAdmin ? 'admin' : 'user'}">${userInitial}</div>
                    <div class="message-content">
                        <div class="message-bubble ${isAdmin ? 'admin' : 'user'}">${message.message.replace(/\n/g, '<br>')}</div>
                        <div class="message-time">${userName} • ${this.formatDate(message.created_at)}</div>
                    </div>
                </div>
            `;
        }).join('');

        setTimeout(() => {
            container.scrollTop = container.scrollHeight;
        }, 100);
    }

    // Auto-resize textarea
    autoResizeTextarea(e) {
        const textarea = e.target;
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
    }

    // Show success message
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

    // Reset welcome screen
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
                            <path d="M11,18H13V16H11V18M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
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

    // Utility methods (assumed implementations)
    getStatusLabel(status) {
        const statusMap = {
            open: 'Aberto',
            pending: 'Pendente',
            resolved: 'Resolvido',
            closed: 'Fechado'
        };
        return statusMap[status] || 'Aberto';
    }

    getPriorityLabel(priority) {
        const priorityMap = {
            low: 'Baixa',
            normal: 'Normal',
            high: 'Alta',
            urgent: 'Urgente'
        };
        return priorityMap[priority] || 'Normal';
    }

    formatDate(date) {
        return new Date(date).toLocaleString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Show toast notification
    showToast(message, type = 'info') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast ${type} show`;
        toast.innerHTML = `
            <div class="toast-content">
                <div class="toast-message">${message}</div>
                <button class="toast-close">&times;</button>
            </div>
        `;
        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);

        toast.querySelector('.toast-close').addEventListener('click', () => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        });
    }
}

// Global functions for HTML onclick events
function toggleSupportPopup() {
    window.supportPopup.toggle();
}

function toggleExpand() {
    window.supportPopup.toggleExpand();
}

function showWelcomeScreen() {
    window.supportPopup.showScreen('welcome');
}

function showTicketForm() {
    window.supportPopup.showScreen('ticketForm');
}

function showMyTickets() {
    window.supportPopup.showScreen('myTickets');
}

function showFAQ() {
    window.supportPopup.showScreen('faq');
}

// Initialize popup
window.supportPopup = new SupportPopup();

</script>
