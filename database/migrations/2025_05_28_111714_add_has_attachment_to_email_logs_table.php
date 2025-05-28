<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{  public function up(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->boolean('has_attachment')->default(false)->after('status');
            $table->text('attachment_path')->nullable()->after('has_attachment');
            $table->string('attachment_name')->nullable()->after('attachment_path');
        });
    }

    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropColumn(['has_attachment', 'attachment_path', 'attachment_name']);
        });
    }
};
