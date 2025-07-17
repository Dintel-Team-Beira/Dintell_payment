<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillingFeaturesToInvoices extends Migration

{
    public function up()
    {
        // Adicionar campos para descontos na tabela invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'credit_card', 'other'])
                  ->default('bank_transfer')
                  ->after('status');

            // Desconto comercial
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('tax_amount');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_percentage');

            // Para notas de crédito/débito
            $table->enum('document_type', ['invoice', 'credit_note', 'debit_note'])
                  ->default('invoice')
                  ->after('invoice_number');
            $table->unsignedBigInteger('related_invoice_id')->nullable()->after('quote_id');
            $table->text('adjustment_reason')->nullable()->after('notes');

            // Campos para venda à dinheiro
            $table->boolean('is_cash_sale')->default(false)->after('payment_method');
            $table->decimal('cash_received', 10, 2)->default(0)->after('paid_amount');
            $table->decimal('change_given', 10, 2)->default(0)->after('cash_received');

            // Índices
            $table->index('document_type');
            $table->index('payment_method');
            $table->foreign('related_invoice_id')->references('id')->on('invoices')->onDelete('set null');
        });

        // Adicionar campos na tabela quotes também
        Schema::table('quotes', function (Blueprint $table) {
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('tax_amount');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_percentage');
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['related_invoice_id']);
            $table->dropColumn([
                'payment_method',
                'discount_percentage',
                'discount_amount',
                'document_type',
                'related_invoice_id',
                'adjustment_reason',
                'is_cash_sale',
                'cash_received',
                'change_given'
            ]);
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn([
                'discount_percentage',
                'discount_amount'
            ]);
        });
    }
}
