<?php
// database/migrations/create_subscription_plans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('setup_fee', 10, 2)->default(0);
            $table->enum('billing_cycle', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly', 'lifetime']);
            $table->integer('billing_cycle_days'); // Para cálculo exato
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('max_domains')->default(1);
            $table->integer('max_storage_gb')->default(1);
            $table->integer('max_bandwidth_gb')->default(10);
            $table->string('color_theme')->default('#3B82F6');
            $table->integer('trial_days')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscription_plans');
    }
};