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
        Schema::table('quotes', function (Blueprint $table) {
            // Adicionar colunas em falta
            if (!Schema::hasColumn('quotes', 'status_updated_at')) {
                $table->timestamp('status_updated_at')->nullable()->after('status');
            }

            if (!Schema::hasColumn('quotes', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('tax_amount');
            }

            if (!Schema::hasColumn('quotes', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('status_updated_at');
            }

            if (!Schema::hasColumn('quotes', 'converted_to_invoice_at')) {
                $table->timestamp('converted_to_invoice_at')->nullable()->after('sent_at');
            }

            if (!Schema::hasColumn('quotes', 'invoice_id')) {
                $table->foreignId('invoice_id')->nullable()->constrained()->after('converted_to_invoice_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn([
                'status_updated_at',
                'discount_amount',
                'sent_at',
                'converted_to_invoice_at',
                'invoice_id'
            ]);
        });
    }
};