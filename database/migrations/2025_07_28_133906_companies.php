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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique(); // Para subdomain ou path
            $table->string('domain')->nullable(); // Domínio personalizado
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Moçambique');
            $table->string('tax_number')->nullable(); // NUIT
            $table->string('logo')->nullable();

            // Configurações de faturação
            $table->string('currency', 3)->default('MZN');
            $table->decimal('default_tax_rate', 5, 2)->default(16.00);
            $table->json('bank_accounts')->nullable(); // Array de contas bancárias
            $table->string('mpesa_number')->nullable();

            // Configurações do SaaS
            $table->enum('status', ['active', 'inactive', 'suspended', 'trial'])->default('trial');
            $table->timestamp('trial_ends_at')->nullable();
            $table->string('subscription_plan')->nullable(); // basic, premium, enterprise
            $table->integer('max_users')->default(1);
            $table->integer('max_invoices_per_month')->default(10);
            $table->integer('max_clients')->default(50);
            $table->boolean('custom_domain_enabled')->default(false);
            $table->boolean('api_access_enabled')->default(false);

            // Configurações de personalização
            $table->json('theme_settings')->nullable(); // Cores, layouts
            $table->json('feature_flags')->nullable(); // Features habilitadas
            $table->json('settings')->nullable(); // Outras configurações

            // Dados de cobrança
            $table->string('billing_email')->nullable();
            $table->text('billing_address')->nullable();
            $table->string('payment_method')->nullable(); // mpesa, bank_transfer, etc
            $table->decimal('monthly_fee', 10, 2)->nullable();
            $table->timestamp('last_payment_at')->nullable();
            $table->timestamp('next_payment_due')->nullable();

            // Estatísticas de uso
            $table->integer('current_users_count')->default(0);
            $table->integer('current_month_invoices')->default(0);
            $table->integer('total_invoices')->default(0);
            $table->integer('total_clients')->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0);

            // Audit trail
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamp('last_activity_at')->nullable();
            $table->json('metadata')->nullable(); // Dados extras

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['status', 'subscription_plan']);
            $table->index(['trial_ends_at']);
            $table->index(['next_payment_due']);
            $table->index(['slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
