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
        // Tabela de tickets de suporte
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');

            $table->string('subject');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'pending', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->enum('category', ['technical', 'billing', 'general', 'feature_request', 'bug_report'])->default('general');

            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();

            $table->integer('satisfaction_rating')->nullable(); // 1-5
            $table->text('satisfaction_comment')->nullable();

            $table->json('attachments')->nullable();
            $table->json('metadata')->nullable(); // Para dados extras como browser, OS, etc.

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['company_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index('ticket_number');
        });

        // Tabela de respostas aos tickets
        Schema::create('support_ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('support_tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->text('message');
            $table->boolean('is_internal')->default(false); // Notas internas
            $table->boolean('is_system')->default(false); // Mensagens automáticas do sistema

            $table->json('attachments')->nullable();
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            $table->index(['ticket_id', 'created_at']);
        });

        // Tabela de visualizações de tickets (para marcar como lido)
        Schema::create('support_ticket_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('support_tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('viewed_at');

            $table->unique(['ticket_id', 'user_id']);
        });

        // Tabela de categorias personalizadas (opcional)
        Schema::create('support_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#3B82F6'); // Cor hex
            $table->string('icon')->nullable(); // Classe de ícone
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_ticket_views');
        Schema::dropIfExists('support_ticket_replies');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('support_categories');
    }
};
