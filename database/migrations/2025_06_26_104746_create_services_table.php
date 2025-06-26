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
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            // Informações Básicas
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();

            // Preços
            $table->decimal('hourly_rate', 10, 2)->nullable(); // Preço por hora
            $table->decimal('fixed_price', 10, 2)->nullable(); // Preço fixo
            $table->decimal('tax_rate', 5, 2)->nullable();

            // Categorização e Complexidade
            $table->string('category', 50);
            $table->enum('complexity_level', ['baixa', 'media', 'alta'])->default('media');

            // Estimativas
            $table->decimal('estimated_hours', 8, 2)->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            // Requisitos e Entregáveis (JSON)
            $table->json('requirements')->nullable();
            $table->json('deliverables')->nullable();

            // Timestamps e Soft Deletes
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['category', 'is_active']);
            $table->index(['is_active', 'created_at']);
            $table->index('complexity_level');
            $table->index(['hourly_rate', 'fixed_price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};