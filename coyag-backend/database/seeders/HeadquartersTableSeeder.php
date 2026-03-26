<?php

namespace Database\Seeders;

use App\Models\Headquarter;
use Illuminate\Database\Seeder;

class HeadquartersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Headquarter::create(['name' => 'Sin sede', 'city' => 'N/A', 'country_id' => 1, 'address' => 'N/A' ]);
        Headquarter::create(['name' => 'Oficina Caracas', 'city' => 'Caracas', 'country_id' => 3, 'address' => 'Avenida Casanova, C.C. Galerías El Recreo, Nivel 3, Epicentro Profesional. Sabana Grande']);
        Headquarter::create(['name' => 'Oficina Madrid', 'city' => 'Madrid', 'country_id' => 2, 'address' => 'Calle del Cid, número 3, piso 1. 28001']);
    }
}
