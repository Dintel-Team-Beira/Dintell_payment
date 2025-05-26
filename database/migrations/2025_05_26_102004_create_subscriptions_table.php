<?php
// database/migrations/create_subscriptions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->string('domain');
            $table->string('subdomain')->nullable();
            $table->string('api_key')->unique();
            $table->enum('status', ['active', 'inactive', 'suspended', 'cancelled', 'expired', 'trial'])->default('trial');
            $table->enum('manual_status', ['enabled', 'disabled'])->default('enabled'); // Controle manual
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('suspension_reason')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->json('suspension_page_config')->nullable();

            // Pagamentos
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('total_revenue', 10, 2)->default(0);
            $table->timestamp('last_payment_date')->nullable();
            $table->timestamp('next_payment_due')->nullable();
            $table->integer('payment_failures')->default(0);
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();

            // Métricas de uso
            $table->bigInteger('total_requests')->default(0);
            $table->bigInteger('monthly_requests')->default(0);
            $table->timestamp('last_request_at')->nullable();
            $table->decimal('storage_used_gb', 8, 2)->default(0);
            $table->decimal('bandwidth_used_gb', 8, 2)->default(0);

            // Automação
            $table->boolean('auto_renew')->default(true);
            $table->boolean('email_notifications')->default(true);
            $table->integer('expiry_warning_days')->default(7);
            $table->timestamp('last_warning_sent')->nullable();

            $table->timestamps();

            // Índices para performance
            $table->index(['domain', 'status']);
            $table->index(['api_key']);
            $table->index(['status', 'ends_at']);
            $table->index(['client_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
};