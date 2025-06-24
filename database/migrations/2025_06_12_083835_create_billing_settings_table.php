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
        Schema::create('billing_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->text('company_address');
            $table->string('tax_number')->nullable();
            $table->string('invoice_prefix', 10)->default('FAT');
            $table->string('quote_prefix', 10)->default('COT');
            $table->integer('next_invoice_number')->default(1);
            $table->integer('next_quote_number')->default(1);
            $table->decimal('default_tax_rate', 5, 2)->default(17.00); // IVA padrão
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_settings');
    }
};
