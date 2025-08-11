<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->enum('type', ['string', 'integer', 'boolean', 'float', 'array', 'json'])->default('string');
            $table->string('group')->default('general');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['key', 'group']);
        });

        // Inserir configurações padrão
        $this->insertDefaultSettings();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }

    /**
     * Inserir configurações padrão do sistema
     */
    private function insertDefaultSettings(): void
    {
        $defaultSettings = [
            // Configurações do Sistema
            ['key' => 'app_name', 'value' => 'SFS - Sistema de Faturação', 'type' => 'string', 'group' => 'system', 'description' => 'Nome da aplicação'],
            ['key' => 'app_description', 'value' => 'Sistema completo de faturação e subscrição para empresas', 'type' => 'string', 'group' => 'system', 'description' => 'Descrição da aplicação'],
            ['key' => 'timezone', 'value' => 'Africa/Maputo', 'type' => 'string', 'group' => 'system', 'description' => 'Fuso horário padrão do sistema'],
            ['key' => 'locale', 'value' => 'pt_BR', 'type' => 'string', 'group' => 'system', 'description' => 'Idioma padrão do sistema'],
            ['key' => 'date_format', 'value' => 'd/m/Y', 'type' => 'string', 'group' => 'system', 'description' => 'Formato de data padrão'],
            ['key' => 'currency', 'value' => 'MZN', 'type' => 'string', 'group' => 'system', 'description' => 'Moeda padrão do sistema'],
            ['key' => 'currency_symbol', 'value' => 'MT', 'type' => 'string', 'group' => 'system', 'description' => 'Símbolo da moeda'],
            ['key' => 'maintenance_mode', 'value' => 'false', 'type' => 'boolean', 'group' => 'system', 'description' => 'Modo de manutenção ativo'],
            ['key' => 'maintenance_message', 'value' => 'Sistema em manutenção. Voltamos em breve!', 'type' => 'string', 'group' => 'system', 'description' => 'Mensagem do modo de manutenção'],
            ['key' => 'registration_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'system', 'description' => 'Permitir registro de novos usuários'],
            ['key' => 'email_verification_required', 'value' => 'false', 'type' => 'boolean', 'group' => 'system', 'description' => 'Exigir verificação de email'],
            ['key' => 'max_users_per_company', 'value' => '50', 'type' => 'integer', 'group' => 'system', 'description' => 'Máximo de usuários por empresa'],
            ['key' => 'max_invoices_per_month', 'value' => '1000', 'type' => 'integer', 'group' => 'system', 'description' => 'Máximo de faturas por mês'],
            ['key' => 'session_lifetime', 'value' => '120', 'type' => 'integer', 'group' => 'system', 'description' => 'Tempo de vida da sessão em minutos'],
            ['key' => 'auto_logout_time', 'value' => '30', 'type' => 'integer', 'group' => 'system', 'description' => 'Tempo de logout automático em minutos'],

            // Configurações de Faturação
            ['key' => 'billing_default_tax_rate', 'value' => '16', 'type' => 'float', 'group' => 'billing', 'description' => 'Taxa de IVA padrão'],
            ['key' => 'billing_tax_name', 'value' => 'IVA', 'type' => 'string', 'group' => 'billing', 'description' => 'Nome do imposto'],
            ['key' => 'billing_invoice_prefix', 'value' => 'INV', 'type' => 'string', 'group' => 'billing', 'description' => 'Prefixo das faturas'],
            ['key' => 'billing_invoice_number_format', 'value' => '{prefix}-{year}-{number}', 'type' => 'string', 'group' => 'billing', 'description' => 'Formato da numeração de faturas'],
            ['key' => 'billing_quote_prefix', 'value' => 'QUO', 'type' => 'string', 'group' => 'billing', 'description' => 'Prefixo das cotações'],
            ['key' => 'billing_quote_number_format', 'value' => '{prefix}-{year}-{number}', 'type' => 'string', 'group' => 'billing', 'description' => 'Formato da numeração de cotações'],
            ['key' => 'billing_payment_terms_days', 'value' => '30', 'type' => 'integer', 'group' => 'billing', 'description' => 'Prazo de pagamento padrão em dias'],
            ['key' => 'billing_late_fee_enabled', 'value' => 'false', 'type' => 'boolean', 'group' => 'billing', 'description' => 'Ativar multa por atraso'],
            ['key' => 'billing_late_fee_type', 'value' => 'percentage', 'type' => 'string', 'group' => 'billing', 'description' => 'Tipo de multa (fixed/percentage)'],
            ['key' => 'billing_late_fee_amount', 'value' => '5', 'type' => 'float', 'group' => 'billing', 'description' => 'Valor da multa por atraso'],
            ['key' => 'billing_auto_send_reminders', 'value' => 'true', 'type' => 'boolean', 'group' => 'billing', 'description' => 'Enviar lembretes automaticamente'],
            ['key' => 'billing_reminder_days_before', 'value' => '3', 'type' => 'integer', 'group' => 'billing', 'description' => 'Dias antes do vencimento para lembrete'],
            ['key' => 'billing_reminder_days_after', 'value' => '7', 'type' => 'integer', 'group' => 'billing', 'description' => 'Dias após vencimento para lembrete'],
            ['key' => 'billing_allow_partial_payments', 'value' => 'true', 'type' => 'boolean', 'group' => 'billing', 'description' => 'Permitir pagamentos parciais'],
            ['key' => 'billing_minimum_payment_amount', 'value' => '0', 'type' => 'float', 'group' => 'billing', 'description' => 'Valor mínimo de pagamento'],
            ['key' => 'billing_invoice_notes', 'value' => 'Obrigado pela sua preferência!', 'type' => 'string', 'group' => 'billing', 'description' => 'Notas padrão das faturas'],
            ['key' => 'billing_invoice_terms', 'value' => 'Pagamento deve ser efetuado no prazo estipulado.', 'type' => 'string', 'group' => 'billing', 'description' => 'Termos padrão das faturas'],

            // Configurações de Email
            ['key' => 'email_mail_driver', 'value' => 'smtp', 'type' => 'string', 'group' => 'email', 'description' => 'Driver de email'],
            ['key' => 'email_mail_host', 'value' => 'smtp.gmail.com', 'type' => 'string', 'group' => 'email', 'description' => 'Servidor SMTP'],
            ['key' => 'email_mail_port', 'value' => '587', 'type' => 'integer', 'group' => 'email', 'description' => 'Porta SMTP'],
            ['key' => 'email_mail_encryption', 'value' => 'tls', 'type' => 'string', 'group' => 'email', 'description' => 'Criptografia do email'],
            ['key' => 'email_mail_from_address', 'value' => 'noreply@sfs.co.mz', 'type' => 'string', 'group' => 'email', 'description' => 'Email remetente padrão'],
            ['key' => 'email_mail_from_name', 'value' => 'SFS Sistema', 'type' => 'string', 'group' => 'email', 'description' => 'Nome do remetente padrão'],
            ['key' => 'email_notifications_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'email', 'description' => 'Ativar notificações por email'],

            // Configurações de Backup
            ['key' => 'backup_auto_backup_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'backup', 'description' => 'Ativar backup automático'],
            ['key' => 'backup_backup_frequency', 'value' => 'daily', 'type' => 'string', 'group' => 'backup', 'description' => 'Frequência do backup'],
            ['key' => 'backup_backup_time', 'value' => '02:00', 'type' => 'string', 'group' => 'backup', 'description' => 'Horário do backup'],
            ['key' => 'backup_backup_retention_days', 'value' => '30', 'type' => 'integer', 'group' => 'backup', 'description' => 'Dias de retenção do backup'],
            ['key' => 'backup_backup_storage', 'value' => 'local', 'type' => 'string', 'group' => 'backup', 'description' => 'Local de armazenamento do backup'],
            ['key' => 'backup_include_files', 'value' => 'true', 'type' => 'boolean', 'group' => 'backup', 'description' => 'Incluir arquivos no backup'],
            ['key' => 'backup_include_database', 'value' => 'true', 'type' => 'boolean', 'group' => 'backup', 'description' => 'Incluir banco de dados no backup'],
        ];

        foreach ($defaultSettings as $setting) {
            DB::table('system_settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
};
