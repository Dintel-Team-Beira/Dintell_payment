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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Informações Básicas
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();

            // Preços e Custos
            $table->decimal('price', 10, 2);
            $table->decimal('cost', 10, 2)->nullable();
            $table->decimal('tax_rate', 5, 2)->nullable();

            // Controle de Estoque
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_level')->default(5);

            // Categorização
            $table->string('category', 50);
            $table->string('unit', 20)->default('unidade');

            // Status e Configurações
            $table->boolean('is_active')->default(true);

            // Mídia
            $table->string('image')->nullable();

            // Informações Físicas (para produtos físicos)
            $table->decimal('weight', 8, 2)->nullable(); // em kg
            $table->string('dimensions', 100)->nullable(); // ex: 30x20x10 cm

            // Timestamps e Soft Deletes
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['category', 'is_active']);
            $table->index(['is_active', 'created_at']);
            $table->index('stock_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};