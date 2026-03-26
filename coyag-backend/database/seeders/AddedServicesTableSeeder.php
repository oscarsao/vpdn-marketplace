<?php

namespace Database\Seeders;

use App\Models\AddedService;
use Illuminate\Database\Seeder;

class AddedServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AddedService::create([
            'client_id'         =>      1,
            'service_id'        =>      4,
            'flag_active_plan'  =>      true
        ]);

        AddedService::create([
            'client_id'         =>      2,
            'service_id'        =>      3,
            'flag_active_plan'  =>      true
        ]);

        AddedService::create([
            'client_id'         =>      3,
            'service_id'        =>      2,
            'flag_active_plan'  =>      true
        ]);

        AddedService::create([
            'client_id'         =>      4,
            'service_id'        =>      1,
            'flag_active_plan'  =>      true
        ]);
    }
}
