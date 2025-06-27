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
        Schema::table('invoice_items', function (Blueprint $table) {
            // Se existe total_price, renomear para total (padrão)
            if (Schema::hasColumn('invoice_items', 'total_price') && !Schema::hasColumn('invoice_items', 'total')) {
                $table->renameColumn('total_price', 'total');
            }

            // Garantir que campos essenciais existem
            if (!Schema::hasColumn('invoice_items', 'name')) {
                $table->string('name')->nullable()->after('invoice_id');
            }

            if (!Schema::hasColumn('invoice_items', 'description')) {
                $table->text('description')->nullable()->after('name');
            }

            if (!Schema::hasColumn('invoice_items', 'quantity')) {
                $table->decimal('quantity', 10, 2)->after('description');
            }

            if (!Schema::hasColumn('invoice_items', 'unit_price')) {
                $table->decimal('unit_price', 12, 2)->after('quantity');
            }

            if (!Schema::hasColumn('invoice_items', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->default(0)->after('unit_price');
            }

            if (!Schema::hasColumn('invoice_items', 'total')) {
                $table->decimal('total', 12, 2)->after('tax_rate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            // Reverter mudanças se necessário
            if (Schema::hasColumn('invoice_items', 'total')) {
                $table->renameColumn('total', 'total_price');
            }
        });
    }
};