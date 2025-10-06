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
        Schema::table('products', function (Blueprint $table) {
            //
             // 1. Adicionar company_id
            $table->foreignId('company_id')
                  ->after('id')
                  ->constrained('companies')
                  ->onDelete('cascade');
            
            // 2. Adicionar category_id
            $table->foreignId('category_id')
                  ->nullable()
                  ->after('company_id')
                  ->constrained('categories')
                  ->onDelete('set null');
            
            // 3. Remover unique do code (vamos recriar depois)
            $table->dropUnique(['code']);
            
            // 4. Adicionar índices
            $table->index('company_id');
            $table->index(['company_id', 'is_active']);
            
            // 5. Criar unique composto (code único por empresa)
            $table->unique(['company_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Reverter na ordem inversa
            $table->dropUnique(['company_id', 'code']);
            $table->dropIndex(['company_id', 'is_active']);
            $table->dropIndex(['company_id']);
            
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
            
            // Recriar unique simples
            $table->unique('code');
        });
    }
};
