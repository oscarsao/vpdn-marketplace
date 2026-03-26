<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;

class DistrictsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        District::create([
            'municipality_id'  =>  79,
            'name'  =>  'Arganzuela'
        ]);

        District::create([
            'municipality_id'  =>  79,
            'name'  =>  'Barajas'
        ]);

        District::create([
            'municipality_id'  =>  79,
            'name'  =>  'Carabanchel'
        ]);

        District::create([
            'municipality_id'  =>  79,
            'name'  =>  'Centro'
        ]);

        District::create([
            'municipality_id'  =>  79,
            'name'  =>  'Chamartín'
        ]);

        District::create([
            'municipality_id'  =>  79,
            'name'  =>  'Chamberí'
        ]);

        District::create([
            'municipality_id'  =>  79,
            'name'  =>  'Ciudad Lineal'
        ]);

        District::create([
            'municipality_id'  =>  79,
            'name'  =>  'Fuencarral-El Pardo'
        ]);

        District::create([
            'municipality_id'  =>  79,
            'name'  =>  'Hortaleza'
        ]);

        District::create([
            'municipality_id'  =>  79,
            'name'  =>  'Latina'
        ]);

        District::create([
            'municipality_id'  =>  79,
            'name'  =>  'Moncloa-Aravaca'
        ]);

        District::create([
            'municipality_id'  =>  79,
            'name'  =>  'Moratalaz'
        ]);

        District::create([
            'municipality_id'  =>  79,
            'name'  =>  'Puente de Vallecas'
        ]);

        District::create([
            'municipality_id'  =>  79,
            'name'  =>  'Retiro'
        ]);

        District::create([
            'municipality_id'  =>  79,
            'name'  =>  'Salamanca'
        ]);

        District::create([
            'municipality_id'  =>  79,
            'name'  =>  'San Blas-Canillejas'
        ]);

        District::create([
            'municipality_id'  =>  79,
            'name'  =>  'Tetuán'
        ]);

        District::create([
            'municipality_id'  =>  79,
            'name'  =>  'Usera'
        ]);

        District::create([
            'municipality_id'  =>  79,
            'name'  =>  'Vicálvaro'
        ]);

        District::create([
            'municipality_id'  =>  79,
            'name'  =>  'Villa de Vallecas'
        ]);

        District::create([
            'municipality_id'  =>  79,
            'name'  =>  'Villaverde'
        ]);
    }
}
