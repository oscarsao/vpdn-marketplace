<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Seeder;

class SectorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sector::create(['name'  =>  'Agricultura']);
        Sector::create(['name'  =>  'Alimentación']);
        Sector::create(['name'  =>  'Animales Domesticos']);
        Sector::create(['name'  =>  'Artes Gráficas']);
        Sector::create(['name'  =>  'Asegurador']);
        Sector::create(['name'  =>  'Comercio']);
        Sector::create(['name'  =>  'Construcción']);
        Sector::create(['name'  =>  'Decoración']);
        Sector::create(['name'  =>  'Deportes']);
        Sector::create(['name'  =>  'Dietética']);
        Sector::create(['name'  =>  'Eléctronica']);
        Sector::create(['name'  =>  'Estética / Cosmética']);
        Sector::create(['name'  =>  'Farmacia']);
        Sector::create(['name'  =>  'Financiero']);
        Sector::create(['name'  =>  'Ganadería']);
        Sector::create(['name'  =>  'Hosteleria']);
        Sector::create(['name'  =>  'Industria Alimentaria']);
        Sector::create(['name'  =>  'Industria Textil']);
        Sector::create(['name'  =>  'Informática']);
        Sector::create(['name'  =>  'Inmobiliario']);
        Sector::create(['name'  =>  'Moda']);
        Sector::create(['name'  =>  'Ocio / Tiempo Libre']);
        Sector::create(['name'  =>  'Restauración']);
        Sector::create(['name'  =>  'Salud']);
        Sector::create(['name'  =>  'Servicios']);
        Sector::create(['name'  =>  'Transporte']);
        Sector::create(['name'  =>  'Sanidad']);
        Sector::create(['name'  =>  'Otro']);
    }
}
