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
        Schema::create('company_subscriptions', function (Blueprint $table) {
            $table->id();

            // Relacionamentos
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();

            // Datas da subscrição
            $table->timestamp('starts_at'); // Mais flexível que date
            $table->timestamp('ends_at');
            $table->timestamp('trial_ends_at')->nullable(); // Para trial period específico

            // Status detalhado
            $table->enum('status', [
                'trialing',      // Em período de teste
                'active',        // Ativa e paga
                'past_due',      // Pagamento atrasado
                'canceled',      // Cancelada mas ainda ativa até ends_at
                'expired',       // Expirada
                'suspended',     // Suspensa administrativamente (acesso bloqueado)
                'grace_period',   // Período de graça após vencimento
            ])->default('trialing');

            // Informações de pagamento
            $table->decimal('amount', 10, 2); // Valor pago/a pagar
            $table->string('currency', 3)->default('MZN');
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->timestamp('last_payment_at')->nullable();
            $table->timestamp('next_payment_due')->nullable();
            $table->string('payment_method')->nullable(); // mpesa, bank_transfer, etc
            $table->string('payment_reference')->nullable(); // Referência de pagamento

            // Upgrade/Downgrade tracking
            $table->foreignId('previous_subscription_id')->nullable()
                ->constrained('company_subscriptions')
                ->nullOnDelete();
            $table->boolean('is_upgrade')->default(false);
            $table->boolean('is_downgrade')->default(false);

            // CANCELAMENTO (iniciado pelo cliente)
            $table->timestamp('canceled_at')->nullable();
            $table->text('canceled_reason')->nullable(); // Texto livre do cliente
            $table->foreignId('canceled_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->boolean('cancel_at_period_end')->default(false); // Cancela só no fim do período pago

            // SUSPENSÃO (iniciado pelo admin/sistema)
            $table->timestamp('suspended_at')->nullable();
            $table->enum('suspension_reason', [
                'payment_failed',     // Falha de pagamento
                'terms_violation',    // Violação de termos
                'fraud_suspected',    // Suspeita de fraude
                'excessive_usage',    // Uso excessivo
                'manual_admin',       // Suspensão manual
                'chargeback',         // Chargeback detectado
                'abuse_detected',      // Abuso do sistema
            ])->nullable();
            $table->text('suspension_details')->nullable(); // Detalhes técnicos/internos
            $table->text('suspension_message')->nullable(); // Mensagem customizada para o cliente
            $table->foreignId('suspended_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('reactivated_at')->nullable();
            $table->foreignId('reactivated_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->boolean('can_appeal')->default(true); // Cliente pode contestar?
            $table->integer('suspension_count')->default(0); // Quantas vezes foi suspenso

            // Renovação automática
            $table->boolean('auto_renew')->default(true);
            $table->integer('renewal_count')->default(0); // Quantas vezes renovou

            // Snapshot dos limites do plano (importante para histórico)
            $table->json('plan_limits')->nullable(); // Guarda os limites que estavam ativos
            $table->json('plan_features')->nullable(); // Features que estavam incluídas

            // Descontos e promoções
            $table->string('coupon_code')->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();

            // Metadados
            $table->text('notes')->nullable(); // Notas administrativas
            $table->json('metadata')->nullable(); // Dados extras

            // Audit trail
            $table->foreignId('created_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('updated_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes(); // Para manter histórico

            // Índices para performance
            $table->index(['company_id', 'status']);
            $table->index(['plan_id', 'status']);
            $table->index(['status', 'ends_at']);
            $table->index('next_payment_due');
            $table->index(['auto_renew', 'ends_at']);
            $table->index('trial_ends_at');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_subscriptions');
    }
};
