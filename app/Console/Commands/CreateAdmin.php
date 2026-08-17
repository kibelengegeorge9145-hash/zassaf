<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'user:create-admin
        {--name= : Full name of the administrator}
        {--email= : Email address of the administrator}
        {--username= : Username of the administrator}
        {--password= : Password for the administrator}
        {--editor : Create an Editor instead of a Super Admin}
        {--force : Update the existing user instead of aborting}';

    protected $description = 'Create an administrator account (uses ADMIN_* env vars by default).';

    public function handle(): int
    {
        $name = $this->option('name') ?? env('ADMIN_NAME', 'Zassaf Admin');
        $email = $this->option('email') ?? env('ADMIN_EMAIL', 'admin@zassaf.com');
        $username = $this->option('username') ?? env('ADMIN_USERNAME', 'zassaf');
        $password = $this->option('password') ?? env('ADMIN_PASSWORD', null);
        $role = $this->option('editor') ? User::ROLE_EDITOR : User::ROLE_SUPER_ADMIN;

        $existing = User::where('email', $email)->first();

        if ($existing && ! $this->option('force')) {
            $this->error("A user with email {$email} already exists. Pass --force to update it.");

            return self::FAILURE;
        }

        if (! $password) {
            $password = $this->secret('Enter password (min 8 characters)');

            if (! $password || mb_strlen($password) < 8) {
                $this->error('Password must be at least 8 characters.');

                return self::FAILURE;
            }
        }

        if ($existing) {
            $role = $this->option('editor')
                ? $role
                : ($existing->role ?: $role);

            $existing->forceFill([
                'name' => $name,
                'username' => $username,
                'password' => Hash::make($password),
                'role' => $role,
                'is_active' => true,
            ])->save();

            $this->info("Updated {$email} as {$role}.");

            return self::SUCCESS;
        }

        User::create([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
            'is_active' => true,
        ]);

        $this->info("Created {$email} as {$role}.");

        return self::SUCCESS;
    }
}
