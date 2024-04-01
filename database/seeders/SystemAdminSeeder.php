<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SystemAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'is_admin'              => true,
            'name'                  => 'System Admin',
            'email'                 => 'sysadmin@delfi.vn',
            'email_verified_at'     => now(),
            'password'              => Hash::make('sysadmin@'),
            'type'                  => 'SYSTEM_ADMIN',
            'status'                => 'ACTIVE',
            'created_at'            => now(),
            'updated_at'            => now()
        ]);

        $role = Role::where('name', 'system-admin')->first();
        $user->assignRole($role);
    }
}
