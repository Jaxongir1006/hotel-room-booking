<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use function Laravel\Prompts\select;

#[Signature('app:make-admin {email? : Email of the user to promote}')]
#[Description('Promote an existing user to administrator role')]
class MakeAdmin extends Command
{
    public function handle(): int
    {
        $email = $this->argument('email');

        if (! $email) {
            $emails = User::query()
                ->orderBy('name')
                ->pluck('email', 'id')
                ->all();

            if (empty($emails)) {
                $this->error('No users found. Register an account first.');

                return self::FAILURE;
            }

            $email = select(
                label: 'Which user should become an admin?',
                options: $emails,
            );
        }

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            $this->error("No user found with email [{$email}].");

            return self::FAILURE;
        }

        if ($user->role === UserRole::Admin) {
            $this->info("{$user->name} is already an administrator.");

            return self::SUCCESS;
        }

        $user->forceFill([
            'role' => UserRole::Admin,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        $this->info("{$user->name} ({$user->email}) is now an administrator.");
        $this->line('Sign in and visit /admin to access the admin panel.');

        return self::SUCCESS;
    }
}
