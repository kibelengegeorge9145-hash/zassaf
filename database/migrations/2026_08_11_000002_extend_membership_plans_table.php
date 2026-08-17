<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->decimal('registration_fee', 12, 2)->default(0)->after('price');
            $table->decimal('monthly_fee', 12, 2)->default(0)->after('registration_fee');
            $table->date('launch_date')->nullable()->after('billing_cycle');
            $table->string('status')->default('active')->after('launch_date')->index();
        });
    }

    public function down(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->dropColumn(['registration_fee', 'monthly_fee', 'launch_date', 'status']);
        });
    }
};
