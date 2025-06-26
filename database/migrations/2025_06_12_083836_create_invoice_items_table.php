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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();

            // Relacionamentos
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('service_id')->nullable()->constrained()->onDelete('set null');

            // Informações do Item
            $table->string('type')->default('product'); // 'product' ou 'service'
            $table->string('name'); // Nome do produto/serviço no momento da fatura
            $table->text('description')->nullable();

            // Quantidades e Preços
            $table->decimal('quantity', 10, 2)->default(1);
            $table->string('unit', 20)->default('unidade');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);

            // Impostos
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);

            // Para serviços por hora
            $table->decimal('hours_worked', 8, 2)->nullable();

            // Ordem dos itens
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            // Índices
            $table->index(['invoice_id', 'sort_order']);
            $table->index(['product_id', 'service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};