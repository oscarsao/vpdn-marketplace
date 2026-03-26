<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use Illuminate\Database\Seeder;

class BusinessTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BusinessType::create(['name' => 'Fondo de comercio']);
        BusinessType::create(['name' => 'Franquicia']);
        BusinessType::create(['name' => 'Inmuebles']);
    }
}
