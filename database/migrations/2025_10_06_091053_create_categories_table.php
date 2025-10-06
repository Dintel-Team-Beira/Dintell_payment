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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
             $table->foreignId('company_id')->constrained('companies');
    
            // Básico
            $table->string('name');
            $table->text('description')->nullable();
            
            // Tipo: produto, serviço ou ambos
            $table->enum('type', ['product', 'service', 'both'])->default('both');
            
            // Hierarquia (opcional mas útil)
            $table->foreignId('parent_id')->nullable()->constrained('categories');
            
            // Organização
            $table->integer('order')->default(0); // para ordenar na lista
            $table->string('color')->nullable(); // #FF5733 (visual na UI)
            $table->string('icon')->nullable(); // nome do ícone
            
            // Status
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['company_id', 'is_active']);
            $table->index(['company_id', 'type']);
            $table->index('parent_id');
            
            // Nome único por empresa
            $table->unique(['company_id', 'name']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
