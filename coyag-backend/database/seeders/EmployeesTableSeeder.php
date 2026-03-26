<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EmployeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SISTEMA
        Employee::create([
            'user_id'           =>      1,
            'headquarter_id'   =>      2,
            'mobile_phone'      =>      '+123456789',
            'department_id'     =>      2,
            'job_title'         =>      'System',
            'name'              =>      'Coyag',
            'surname'           =>      'System',
            'id_public'         =>      Str::random(32)
        ]);


        // PRESIDENCIA
        Employee::create([
            'user_id'           =>      2,
            'headquarter_id'   =>      2,
            'mobile_phone'      =>      '+34123456789',
            'department_id'     =>      2,
            'job_title'         =>      'CEO',
            'name'              =>      'Gabriel',
            'surname'           =>      'Eustache',
            'id_public'         =>      Str::random(32)
        ]);

        // TECNOLOGÍA
        Employee::create([
            'user_id'           =>      3,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584124564545',
            'department_id'     =>      3,
            'job_title'         =>      'Jefe',
            'name'              =>      'Humberto',
            'surname'           =>      'Albarrán',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      4,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      3,
            'job_title'         =>      'Jefe',
            'name'              =>      'Gustavo',
            'surname'           =>      'Escobar',
            'id_public'         =>      Str::random(32)
        ]);

        // LOBBY
        Employee::create([
            'user_id'           =>      5,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      4,
            'job_title'         =>      'Recepción Lobby - Auxiliar',
            'name'              =>      'Recepción 1',
            'surname'           =>      'Lobby',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      6,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      4,
            'job_title'         =>      'Recepción Lobby - Auxiliar',
            'name'              =>      'Recepción 2',
            'surname'           =>      'Lobby',
            'id_public'         =>      Str::random(32)
        ]);

        // COMERCIAL
        Employee::create([
            'user_id'           =>      7,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      5,
            'job_title'         =>      'Director Comercial',
            'name'              =>      'Director',
            'surname'           =>      'Comercial',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      8,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      5,
            'job_title'         =>      'Gerente Comercial',
            'name'              =>      'Gerente',
            'surname'           =>      'Comercial',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      9,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      5,
            'job_title'         =>      'Coordinador Comercial',
            'name'              =>      'Coordinador',
            'surname'           =>      'Comercial',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      10,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      5,
            'job_title'         =>      'Asesor Comercial',
            'name'              =>      'Asesor 1',
            'surname'           =>      'Comercial',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      11,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      5,
            'job_title'         =>      'Asesor Comercial',
            'name'              =>      'Asesor 2',
            'surname'           =>      'Comercial',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      12,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      5,
            'job_title'         =>      'Asesor Comercial',
            'name'              =>      'Asesor 3',
            'surname'           =>      'Comercial',
            'id_public'         =>      Str::random(32)
        ]);

        //EJECUTIVO
        Employee::create([
            'user_id'           =>      13,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      6,
            'job_title'         =>      'Director Ejecutivo',
            'name'              =>      'Director',
            'surname'           =>      'Ejecutivo',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      14,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      6,
            'job_title'         =>      'Gerente Ejecutivo',
            'name'              =>      'Gerente',
            'surname'           =>      'Ejecutivo',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      15,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      6,
            'job_title'         =>      'Coordinador Ejecutivo',
            'name'              =>      'Coordinador',
            'surname'           =>      'Ejecutivo',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      16,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      6,
            'job_title'         =>      'Asesor Ejecutivo',
            'name'              =>      'Asesor 1',
            'surname'           =>      'Ejecutivo',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      17,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      6,
            'job_title'         =>      'Asesor Ejecutivo',
            'name'              =>      'Asesor 2',
            'surname'           =>      'Ejecutivo',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      18,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      6,
            'job_title'         =>      'Asesor Ejecutivo',
            'name'              =>      'Asesor 3',
            'surname'           =>      'Ejecutivo',
            'id_public'         =>      Str::random(32)
        ]);


        //EXTRANJERIA
        Employee::create([
            'user_id'           =>      19,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      7,
            'job_title'         =>      'Director Extranjería',
            'name'              =>      'Director',
            'surname'           =>      'Extranjería',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      20,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      7,
            'job_title'         =>      'Gerente Extranjería',
            'name'              =>      'Gerente',
            'surname'           =>      'Extranjería',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      21,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      7,
            'job_title'         =>      'Coordinador Extranjería',
            'name'              =>      'Coordinador',
            'surname'           =>      'Extranjería',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      22,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      7,
            'job_title'         =>      'Asesor Extranjería',
            'name'              =>      'Asesor 1',
            'surname'           =>      'Extranjería',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      23,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      7,
            'job_title'         =>      'Asesor Extranjería',
            'name'              =>      'Asesor 2',
            'surname'           =>      'Extranjería',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      24,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      7,
            'job_title'         =>      'Asesor Extranjería',
            'name'              =>      'Asesor 3',
            'surname'           =>      'Extranjería',
            'id_public'         =>      Str::random(32)
        ]);


        //MERCANTIL
        Employee::create([
            'user_id'           =>      25,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      8,
            'job_title'         =>      'Director Mercantil',
            'name'              =>      'Director',
            'surname'           =>      'Mercantil',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      26,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      8,
            'job_title'         =>      'Gerente Mercantil',
            'name'              =>      'Gerente',
            'surname'           =>      'Mercantil',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      27,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      8,
            'job_title'         =>      'Coordinador Mercantil',
            'name'              =>      'Coordinador',
            'surname'           =>      'Mercantil',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      28,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      8,
            'job_title'         =>      'Asesor Mercantil',
            'name'              =>      'Asesor 1',
            'surname'           =>      'Mercantil',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      29,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      8,
            'job_title'         =>      'Asesor Mercantil',
            'name'              =>      'Asesor 2',
            'surname'           =>      'Mercantil',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      30,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      8,
            'job_title'         =>      'Asesor Mercantil',
            'name'              =>      'Asesor 3',
            'surname'           =>      'Mercantil',
            'id_public'         =>      Str::random(32)
        ]);



        //FINANCIERO
        Employee::create([
            'user_id'           =>      31,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      9,
            'job_title'         =>      'Director Financiero',
            'name'              =>      'Director',
            'surname'           =>      'Financiero',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      32,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      9,
            'job_title'         =>      'Gerente Financiero',
            'name'              =>      'Gerente',
            'surname'           =>      'Financiero',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      33,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      9,
            'job_title'         =>      'Coordinador Financiero',
            'name'              =>      'Coordinador',
            'surname'           =>      'Financiero',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      34,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      9,
            'job_title'         =>      'Asesor Financiero',
            'name'              =>      'Asesor 1',
            'surname'           =>      'Financiero',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      35,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      9,
            'job_title'         =>      'Asesor Financiero',
            'name'              =>      'Asesor 2',
            'surname'           =>      'Financiero',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      36,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      9,
            'job_title'         =>      'Asesor Financiero',
            'name'              =>      'Asesor 3',
            'surname'           =>      'Financiero',
            'id_public'         =>      Str::random(32)
        ]);


        //CONTABILIDAD
        Employee::create([
            'user_id'           =>      37,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      10,
            'job_title'         =>      'Director Contabilidad',
            'name'              =>      'Director',
            'surname'           =>      'Contabilidad',
            'id_public'         =>      Str::random(32)
        ]);


        //MARKETING
        Employee::create([
            'user_id'           =>      38,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      11,
            'job_title'         =>      'Director Marketing',
            'name'              =>      'Director',
            'surname'           =>      'Marketing',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      39,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      11,
            'job_title'         =>      'Gerente Marketing',
            'name'              =>      'Gerente',
            'surname'           =>      'Marketing',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      40,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      11,
            'job_title'         =>      'Coordinador Marketing',
            'name'              =>      'Coordinador',
            'surname'           =>      'Marketing',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      41,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      11,
            'job_title'         =>      'Asesor Marketing',
            'name'              =>      'Asesor 1',
            'surname'           =>      'Marketing',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      42,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      11,
            'job_title'         =>      'Asesor Marketing',
            'name'              =>      'Asesor 2',
            'surname'           =>      'Marketing',
            'id_public'         =>      Str::random(32)
        ]);

        Employee::create([
            'user_id'           =>      43,
            'headquarter_id'   =>      3,
            'mobile_phone'      =>      '+584147897885',
            'department_id'     =>      11,
            'job_title'         =>      'Asesor Marketing',
            'name'              =>      'Asesor 3',
            'surname'           =>      'Marketing',
            'id_public'         =>      Str::random(32)
        ]);


    }
}
