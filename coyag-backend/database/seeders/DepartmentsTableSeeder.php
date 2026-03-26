<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Department::create([
            'name'          =>    'Sin departamento',
            'description'   =>    'No se ha definido algún departamento',
        ]);

        Department::create([
            'name'          =>    'Presidencia',
            'description'   =>    'CEO',
        ]);

        Department::create([
            'name'          =>    'Tecnología',
            'description'   =>    'Departamento de desarrollo, sistemas y soporte técnico',
        ]);

        Department::create([
            'name'          =>    'Lobby',
            'description'   =>    'Departamento de Recepción y Atención al Cliente',
        ]);

        Department::create([
            'name'          =>    'Comercial',
            'description'   =>    'Departamento de Captación de Clientes',
        ]);

        Department::create([
            'name'          =>    'Ejecutivo',
            'description'   =>    'Departamento de soluciones concretas a las necesidades de los clientes',
        ]);

        Department::create([
            'name'          =>    'Extranjería',
            'description'   =>    'Departamento Extranjería',
        ]);

        Department::create([
            'name'          =>    'Mercantil',
            'description'   =>    'Departamento Mercantil',
        ]);

        Department::create([
            'name'          =>    'Financiero',
            'description'   =>    'Departamento Financiero',
        ]);

        Department::create([
            'name'          =>    'Contabilidad',
            'description'   =>    'Departamento Contabilidad',
        ]);

        Department::create([
            'name'          =>    'Marketing',
            'description'   =>    'Departamento Marketing',
        ]);
    }
}
