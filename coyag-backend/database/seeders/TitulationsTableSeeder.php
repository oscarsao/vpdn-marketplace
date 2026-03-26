<?php

namespace Database\Seeders;

use App\Models\Titulation;
use Illuminate\Database\Seeder;

class TitulationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Titulation::create(['name' => 'Sin titulación']);
        Titulation::create(['name' => 'Don']);
        Titulation::create(['name' => 'Doña']);
    }
}
