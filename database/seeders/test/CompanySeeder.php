<?php

namespace Database\Seeders\test;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Company::create([
            'is_default'        => false,
            'code'              => 'DEFLIVN',
            'name'              => 'Delfi Technologies',
            'address'           => '38 Phan Đình Giót, phường 2, Tân Bình, HCM',
            'city'              => 'HCM',
            'limited_users'     => null,
            'limited_events'    => null,
            'limited_campaigns' => null,
        ]);

        for ($i = 0; $i < 10; $i++) {
            Company::create([
                'is_default'        => false,
                'code'              => 'DEFLIVN' . $i,
                'name'              => 'Delfi Technologies ' . $i,
                'address'           => $i . '38 Phan Đình Giót, phường 2, Tân Bình, HCM',
                'city'              => 'HCM',
                'limited_users'     => null,
                'limited_events'    => null,
                'limited_campaigns' => null,
            ]);
        }
    }
}
