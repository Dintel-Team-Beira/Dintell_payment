<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            // Adicionar campo de desconto se não existir
            if (!Schema::hasColumn('quotes', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('tax_amount');
            }

            // Adicionar índices para melhor performance
            $table->index(['status', 'created_at']);
            $table->index(['client_id', 'status']);
            $table->index(['quote_date', 'status']);
            $table->index('valid_until');
        });

        // Criar tabela para notas das cotações (se necessário)
        if (!Schema::hasTable('quote_notes')) {
            Schema::create('quote_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quote_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->text('note');
                $table->enum('type', ['internal', 'client'])->default('internal');
                $table->timestamps();

                $table->index(['quote_id', 'created_at']);
            });
        }

        // Atualizar status de cotações expiradas
        DB::statement("
            UPDATE quotes
            SET status = 'expired', status_updated_at = NOW()
            WHERE valid_until < CURDATE()
            AND status NOT IN ('accepted', 'rejected', 'expired')
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['client_id', 'status']);
            $table->dropIndex(['quote_date', 'status']);
            $table->dropIndex(['valid_until']);

            if (Schema::hasColumn('quotes', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }
        });

        Schema::dropIfExists('quote_notes');
    }
};