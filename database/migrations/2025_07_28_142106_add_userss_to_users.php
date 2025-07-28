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
        Schema::table('users', function (Blueprint $table) {
            // Relacionamento com empresa (tenant)
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');

            // Tipos de usuário
            $table->boolean('is_super_admin')->default(false); // Admin do SaaS
            $table->enum('role', ['admin', 'user', 'viewer'])->default('user'); // Role dentro da empresa

            // Informações de login e atividade
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->string('login_ip')->nullable();
            $table->json('preferences')->nullable(); // Preferências do usuário

            // Status do usuário
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable()->change();

            // Informações adicionais
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->string('timezone')->default('Africa/Maputo');
            $table->string('language')->default('pt');

            // Índices
            $table->index(['company_id', 'role']);
            $table->index(['is_super_admin']);
            $table->index(['is_active']);
            $table->index(['last_activity_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn([
                'company_id',
                'is_super_admin',
                'role',
                'last_login_at',
                'last_activity_at',
                'login_ip',
                'preferences',
                'is_active',
                'phone',
                'bio',
                'avatar',
                'timezone',
                'language'
            ]);
        });
    }
};
