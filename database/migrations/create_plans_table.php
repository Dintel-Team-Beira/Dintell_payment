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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('MZN');
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            // Limites do plano
            $table->integer('max_users')->nullable(); // null = ilimitado
            $table->integer('max_companies')->nullable();
            $table->integer('max_invoices_per_month')->nullable();
            $table->integer('max_clients')->nullable();
            $table->integer('max_products')->nullable();
            $table->integer('max_storage_mb')->nullable(); // em MB

            // Funcionalidades
            $table->json('features'); // Array de funcionalidades incluídas
            $table->json('limitations')->nullable(); // Array de limitações específicas

            // Configurações de teste
            $table->integer('trial_days')->default(0);
            $table->boolean('has_trial')->default(false);

            // Metadados
            $table->json('metadata')->nullable(); // Dados extras customizáveis
            $table->string('stripe_price_id')->nullable(); // Para integração com Stripe
            $table->string('color', 7)->default('#3B82F6'); // Cor do plano em hex
            $table->string('icon')->nullable(); // Nome do ícone

            $table->timestamps();

            // Índices
            $table->index(['is_active', 'sort_order']);
            $table->index('billing_cycle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
