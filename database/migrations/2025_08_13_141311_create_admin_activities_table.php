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
        Schema::create('admin_activities', function (Blueprint $table) {
            $table->id();

            // Relacionamento com usuário admin
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');

            // Informações da atividade
            $table->string('action'); // Ex: 'created_company', 'suspended_user', 'viewed_invoice'
            $table->string('description'); // Descrição legível da ação
            $table->string('model_type')->nullable(); // Classe do model afetado
            $table->unsignedBigInteger('model_id')->nullable(); // ID do registro afetado

            // Dados adicionais em JSON
            $table->json('properties')->nullable(); // Dados extras da ação
            $table->json('old_values')->nullable(); // Valores antes da alteração
            $table->json('new_values')->nullable(); // Valores após a alteração

            // Informações de contexto
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->string('method')->nullable(); // GET, POST, PUT, DELETE

            // Categorização
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->string('category')->nullable(); // 'user_management', 'company_management', etc.

            // Timestamps
            $table->timestamps();

            // Índices para performance
            $table->index(['admin_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index(['severity', 'created_at']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_activities');
    }
};
