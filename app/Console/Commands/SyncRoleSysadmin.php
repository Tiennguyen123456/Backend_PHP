<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncRoleSysadmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-role-sysadmin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $permissions = new \Spatie\Permission\Models\Permission();
        $roleModel = new \Spatie\Permission\Models\Role();
        $role = $roleModel->where('name', 'system-admin')->first();
        $role->syncPermissions($permissions->all());
    }
}
