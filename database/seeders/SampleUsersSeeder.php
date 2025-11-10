<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SampleUsersSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $clientRole = Role::where('name', 'client')->first();

        if (! $adminRole || ! $clientRole) {
            $this->command?->warn('Roles admin/client not found. Run ProductionSeeder before SampleUsersSeeder.');

            return;
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Demo Admin',
                'password' => Hash::make('password'),
            ],
        );
        $admin->syncRoles(['admin']);

        $client = User::firstOrCreate(
            ['email' => 'client@example.com'],
            [
                'name' => 'Demo Client',
                'password' => Hash::make('password'),
            ],
        );
        $client->syncRoles(['client']);
    }
}
