<?php

namespace Database\Seeders;

use App\Models\Continent;
use Illuminate\Database\Seeder;

class ContinentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Continent::create(['name' => 'Sin continente']);
        Continent::create(['name' => 'África']);
        Continent::create(['name' => 'América']);
        Continent::create(['name' => 'Antártida']);
        Continent::create(['name' => 'Asia']);
        Continent::create(['name' => 'Europa']);
        Continent::create(['name' => 'Europa - UE']);
        Continent::create(['name' => 'Oceanía']);
    }
}
