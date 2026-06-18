<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    protected $signature = 'user:create {name} {email} {username} {password} {role=user}';
    protected $description = 'Create a new user manually';

    public function handle(): int
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $username = $this->argument('username');
        $password = $this->argument('password');
        $role = $this->argument('role');

        if (!in_array($role, ['admin', 'user'])) {
            $this->error('Role must be either "admin" or "user"');
            return 1;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'username' => $username,
            'password' => Hash::make($password),
            'role' => $role,
        ]);

        $this->info("User created successfully!");
        $this->info("Name: {$user->name}");
        $this->info("Email: {$user->email}");
        $this->info("Username: {$user->username}");
        $this->info("Role: {$user->role}");

        return 0;
    }
}
