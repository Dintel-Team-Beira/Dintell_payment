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
        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->onDelete('cascade');

            // Tipo e referência do item
            $table->enum('type', ['product', 'service'])->index();
            $table->unsignedBigInteger('item_id'); // ID do produto ou serviço

            // Dados básicos do item
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('tax_rate', 5, 2)->default(0);

            // Dados específicos de produtos
            $table->string('category')->nullable();
            $table->string('unit')->nullable();

            // Dados específicos de serviços
            $table->string('complexity_level')->nullable();
            $table->decimal('estimated_hours', 8, 2)->nullable();

            // Campos calculados (podem ser armazenados para performance)
            $table->decimal('subtotal', 12, 2)->storedAs('quantity * unit_price');
            $table->decimal('tax_amount', 12, 2)->storedAs('(quantity * unit_price) * (tax_rate / 100)');
            $table->decimal('total', 12, 2)->storedAs('(quantity * unit_price) * (1 + tax_rate / 100)');

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['type', 'item_id']);
            $table->index('category');
            $table->index('complexity_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_items');
    }
};