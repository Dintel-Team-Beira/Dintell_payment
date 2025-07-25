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
        Schema::table('billing_settings', function (Blueprint $table) {
            // Adicionar colunas para notas de crédito e débito se não existirem
            if (!Schema::hasColumn('billing_settings', 'credit_note_prefix')) {
                $table->string('credit_note_prefix', 10)->default('NC')->after('next_quote_number');
            }

            if (!Schema::hasColumn('billing_settings', 'next_credit_note_number')) {
                $table->unsignedInteger('next_credit_note_number')->default(1)->after('credit_note_prefix');
            }

            if (!Schema::hasColumn('billing_settings', 'debit_note_prefix')) {
                $table->string('debit_note_prefix', 10)->default('ND')->after('next_credit_note_number');
            }

            if (!Schema::hasColumn('billing_settings', 'next_debit_note_number')) {
                $table->unsignedInteger('next_debit_note_number')->default(1)->after('debit_note_prefix');
            }

            // Adicionar NUIT se não existir
            if (!Schema::hasColumn('billing_settings', 'company_nuit')) {
                $table->string('company_nuit')->nullable()->after('company_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_settings', function (Blueprint $table) {
            $table->dropColumn([
                'credit_note_prefix',
                'next_credit_note_number',
                'debit_note_prefix',
                'next_debit_note_number',
                'company_nuit'
            ]);
        });
    }
};
