<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['manager', 'customer', 'doctor', 'patient', 'insurance'];
        $permissions = ['create role', 'edit role', 'delete role', 'view role'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        $managerRole = Role::where('name', 'manager')->first();
        $managerRole->syncPermissions($permissions);

        foreach ($roles as $roleName) {
            $user = User::factory()->create([
                'name' => ucfirst($roleName) . ' User',
                'gender' => fake()->randomElement(['male', 'female']),
                'email' => $roleName . '@bwa.com',
                'phone' => fake()->phoneNumber(),
                'photo' => fake()->imageUrl(200, 200, 'people', true, 'profile'),
                'password' => Hash::make('password123'), // Default password for all users
            ]);

            $user->assignRole($roleName);
        }
    }
}
