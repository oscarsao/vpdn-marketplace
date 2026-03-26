<?php

namespace Database\Seeders;

use App\Models\AssignedAdvisor;
use Illuminate\Database\Seeder;

class AssignedAdvisorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AssignedAdvisor::create([
            'client_id'             => 1,
            'employee_id'           => 9,
            'generator_employee_id' =>  1
            ]);

        AssignedAdvisor::create([
            'client_id'             => 2,
            'employee_id'           => 10,
            'generator_employee_id' =>  1
            ]);

        AssignedAdvisor::create([
            'client_id'             => 3,
            'employee_id'           => 11,
            'generator_employee_id' =>  1
            ]);

        AssignedAdvisor::create([
            'client_id'             => 4,
            'employee_id'           => 15,
            'generator_employee_id' =>  1
            ]);
    }
}
