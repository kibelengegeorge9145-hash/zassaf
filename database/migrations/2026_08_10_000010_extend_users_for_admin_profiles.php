<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->string('profile_photo')->nullable()->after('is_active');
            $table->timestamp('last_login_at')->nullable()->after('profile_photo');
        });

        DB::table('users')
            ->whereNull('username')
            ->orderBy('id')
            ->select(['id', 'email'])
            ->chunkById(100, function ($users) {
                foreach ($users as $user) {
                    $prefix = preg_replace('/[^a-z0-9]/i', '', explode('@', $user->email)[0]);
                    $username = strtolower(substr($prefix ?: 'user', 0, 20));
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['username' => $username]);
                }
            });

        DB::table('users')->where('role', 'admin')->update(['role' => 'super_admin']);
        DB::table('users')->where('role', 'staff')->update(['role' => 'editor']);
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'super_admin')->update(['role' => 'admin']);
        DB::table('users')->where('role', 'editor')->update(['role' => 'staff']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_login_at', 'profile_photo', 'phone', 'username']);
        });
    }
};
