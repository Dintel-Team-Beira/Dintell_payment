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
       Schema::table('invoices', function (Blueprint $table) {
    if (!Schema::hasColumn('invoices', 'document_type')) {
        $table->enum('document_type', ['invoice', 'credit_note', 'debit_note'])
              ->default('invoice')
              ->after('invoice_number');
    }

    if (!Schema::hasColumn('invoices', 'related_invoice_id')) {
        $table->unsignedBigInteger('related_invoice_id')->nullable()->after('quote_id');
        $table->foreign('related_invoice_id')->references('id')->on('invoices')->onDelete('set null');
    }

    if (!Schema::hasColumn('invoices', 'adjustment_reason')) {
        $table->text('adjustment_reason')->nullable()->after('notes');
    }

    if (!Schema::hasColumn('invoices', 'discount_percentage')) {
        $table->decimal('discount_percentage', 5, 2)->default(0)->after('tax_amount');
    }

    if (!Schema::hasColumn('invoices', 'discount_amount')) {
        $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_percentage');
    }

    if (!Schema::hasColumn('invoices', 'payment_method')) {
        $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'credit_card', 'other'])
              ->default('bank_transfer')
              ->after('status');
    }
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
        });
    }
};
