<?php

namespace Database\Seeders\test;

use App\Models\Company;
use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = Company::where([
            'is_default'    => true,
            'code'          => 'DEFLIVN'
        ])->first();

        if (empty($company)) return;

        Event::create([
            'is_default'            => true,
            'company_id'            => $company->id,
            'code'                  => 'EVENT01',
            'name'                  => 'Event 01',
            'description'           => 'Event for testing',
            'location'              => 'Adora Center, HCM',
            'main_field_templates'  => Event::buildDefaultMainFieldTemplate(),
            'start_time'             => '2024-01-01',
            'end_time'              => '2024-01-31',
        ]);
    }
}
