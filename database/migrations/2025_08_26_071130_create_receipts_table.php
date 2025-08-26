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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
$table->string('receipt_number')->unique();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            
            $table->decimal('amount_paid', 12, 2);
            $table->enum('payment_method', [
                'cash', 
                'bank_transfer', 
                'check', 
                'credit_card', 
                'mobile_money', 
                'other'
            ]);
            
            $table->datetime('payment_date');
            $table->string('transaction_reference')->nullable();
            $table->text('notes')->nullable();
            
            $table->enum('status', ['active', 'cancelled'])->default('active');
            $table->foreignId('issued_by')->nullable()->constrained('users');
            
            $table->timestamps();
            
            // Índices para performance
            $table->index(['company_id', 'payment_date']);
            $table->index(['invoice_id', 'status']);
            $table->index(['client_id', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
