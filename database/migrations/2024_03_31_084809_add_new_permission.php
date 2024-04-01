<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $permissions = [
                'post:view',
                'post:create',
                'post:delete',
                'dashboard:view',
                'client:scan-qr',
            ];

            foreach ($permissions as $permission) {
                $exists = DB::table('permissions')->where('name', $permission)->exists();
                if (!$exists) {
                    DB::table('permissions')->insert([
                        'name'          => $permission,
                        'guard_name'    => 'api',
                        'created_at'    => now(),
                    ]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
