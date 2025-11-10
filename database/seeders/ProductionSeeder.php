<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'manage_templates',
            'manage_subscriptions',
            'manage_purchases',
            'view_reports',
            'purchase_assets',
            'download_assets',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $rolePermissions = [
            'admin' => [
                'manage_templates',
                'manage_subscriptions',
                'manage_purchases',
                'view_reports',
                'download_assets',
            ],
            'client' => [
                'purchase_assets',
                'download_assets',
            ],
        ];

        foreach ($rolePermissions as $role => $permissionsForRole) {
            $roleModel = Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);

            $roleModel->syncPermissions($permissionsForRole);
        }

        $this->call([PlanSeeder::class, TemplateSeeder::class]);
    }
}
