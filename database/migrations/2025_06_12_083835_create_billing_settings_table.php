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

            // Informações da empresa
            $table->string('company_name');
            $table->text('company_address');
            $table->string('tax_number')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_website')->nullable();
            $table->string('company_logo')->nullable();

            // Configurações de faturamento
            $table->string('invoice_prefix', 10)->default('FAT');
            $table->string('quote_prefix', 10)->default('COT');
            $table->unsignedInteger('next_invoice_number')->default(1);
            $table->unsignedInteger('next_quote_number')->default(1);
            $table->decimal('default_tax_rate', 5, 2)->default(17.00);
            $table->unsignedInteger('default_payment_terms')->nullable(); // dias
            $table->decimal('late_fee_percentage', 5, 2)->nullable();
            $table->string('currency', 3)->default('MZN');
            $table->enum('number_format', ['dot', 'comma'])->default('comma');

            // Configurações de impostos
            $table->string('tax_name')->nullable();
            $table->string('tax_registration')->nullable();
            $table->boolean('include_tax_in_price')->default(false);

            // Configurações de notificações
            $table->boolean('send_invoice_emails')->default(true);
            $table->boolean('send_quote_emails')->default(true);
            $table->boolean('send_overdue_reminders')->default(true);
            $table->unsignedInteger('reminder_days')->default(7);
            $table->text('email_template_invoice')->nullable();
            $table->text('email_template_quote')->nullable();
            $table->text('email_template_reminder')->nullable();

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