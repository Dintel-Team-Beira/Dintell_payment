<?php
// database/migrations/create_email_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');
            $table->string('to_email');
            $table->string('subject');
            $table->enum('type', ['suspended', 'activated', 'expiring', 'payment', 'renewed', 'cancelled']);
            $table->text('content');
            $table->enum('status', ['sent', 'failed', 'queued'])->default('queued');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'created_at']);
            $table->index(['to_email', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('email_logs');
    }
};