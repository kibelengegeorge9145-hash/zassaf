<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sandbox_payments', function (Blueprint $table) {
            $table->string('transaction_reference', 64)->nullable()->unique()->after('provider_reference');
        });
    }

    public function down(): void
    {
        Schema::table('sandbox_payments', function (Blueprint $table) {
            $table->dropUnique(['transaction_reference']);
            $table->dropColumn('transaction_reference');
        });
    }
};
