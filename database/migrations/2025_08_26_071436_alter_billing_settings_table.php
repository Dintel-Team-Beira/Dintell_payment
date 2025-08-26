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
        //
          Schema::table('billing_settings', function (Blueprint $table) {
            $table->string('receipt_prefix', 10)->default('REC');
            $table->unsignedInteger('receipt_next_number')->default(1)->after('receipt_prefix');
            $table->unsignedTinyInteger('receipt_number_length')->default(6)->after('receipt_next_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('billing_settings', function (Blueprint $table) {
            $table->dropColumn(['receipt_prefix', 'receipt_next_number', 'receipt_number_length']);
        });
    }
};
