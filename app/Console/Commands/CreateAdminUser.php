<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    protected $signature = 'make:admin
                            {--name= : Naam van de admin}
                            {--email= : E-mailadres van de admin}';

    protected $description = 'Maak een nieuwe admin user aan';

    public function handle(): int
    {
        $name = $this->option('name') ?? $this->ask('Naam');
        $email = $this->option('email') ?? $this->ask('E-mailadres');
        $password = $this->secret('Wachtwoord');

        $validator = Validator::make(
            compact('name', 'email', 'password'),
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8'],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = new User(compact('name', 'email', 'password'));
        $user->role = 'admin';
        $user->save();

        $this->info("Admin user {$user->email} is aangemaakt.");

        return self::SUCCESS;
    }
}
