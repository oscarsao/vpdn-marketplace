<?php

namespace Database\Seeders;

use App\Models\VisaType;
use Illuminate\Database\Seeder;

class VisaTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VisaType::create([
            'name'          =>  'Arraigo Social',
            'flag_active'   =>  1,
        ]);

        VisaType::create([
            'name'          =>  'Asilo',
            'flag_active'   =>  1,
        ]);

        VisaType::create([
            'name'          =>  'Razones Humanitarias',
            'flag_active'   =>  1,
        ]);

        VisaType::create([
            'name'          =>  'Reagrupación - Comunitario',
            'flag_active'   =>  1,
        ]);

        VisaType::create([
            'name'          =>  'Reagrupación - Residente',
            'flag_active'   =>  1,
        ]);

        VisaType::create([
            'name'          =>  'Visado Estudiantes',
            'flag_active'   =>  1,
        ]);

        VisaType::create([
            'name'          =>  'Visado Golden',
            'flag_active'   =>  1,
        ]);

        VisaType::create([
            'name'          =>  'Visado Intraempresarial',
            'flag_active'   =>  1,
        ]);

        VisaType::create([
            'name'          =>  'Visado No Lucrativo',
            'flag_active'   =>  1,
        ]);

        VisaType::create([
            'name'          =>  'Visado Profesional Altamente Cualificado',
            'flag_active'   =>  1,
        ]);

    }
}
