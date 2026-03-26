<?php

namespace Database\Seeders;

use App\Models\FamilyType;
use Illuminate\Database\Seeder;

class FamilyTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FamilyType::create([
            'name'          =>  'Cónyugue'
        ]);

        FamilyType::create([
            'name'          =>  'Hijo'
        ]);

        FamilyType::create([
            'name'          =>  'Madre'
        ]);

        FamilyType::create([
            'name'          =>  'Padre'
        ]);

        FamilyType::create([
            'name'          =>  'Hermano'
        ]);

        FamilyType::create([
            'name'          =>  'Suegro'
        ]);

        FamilyType::create([
            'name'          =>  'Cuñado'
        ]);

        FamilyType::create([
            'name'          =>  'Sobrino'
        ]);

        FamilyType::create([
            'name'          =>  'Tío'
        ]);

        FamilyType::create([
            'name'          =>  'Primo'
        ]);

    }
}
