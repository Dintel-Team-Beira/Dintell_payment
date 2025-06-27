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
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number')->unique();
                $table->foreignId('client_id')->constrained();
                $table->foreignId('quote_id')->nullable()->constrained();
                $table->date('invoice_date');
                $table->date('due_date');
                $table->decimal('subtotal', 12, 2);
                $table->decimal('tax_amount', 12, 2)->default(0);
                $table->decimal('discount_amount', 12, 2)->default(0);
                $table->decimal('total', 12, 2);
                $table->enum('status', [
                    'draft',
                    'pending',
                    'sent',
                    'paid',
                    'overdue',
                    'cancelled',
                    'refunded'
                ])->default('draft');
                $table->text('notes')->nullable();
                $table->text('terms_conditions')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['client_id', 'status']);
                $table->index(['invoice_date', 'status']);
                $table->index('due_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};