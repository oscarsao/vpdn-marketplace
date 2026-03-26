<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
         * Role Types
         *
         * En su mayoría son los mismos roles que COYAG, los cambios son:
         *
         *  - Cliente TTI y Cliente TTE No existen en este proyecto
         *  - Empleado es level 3
         *  - Se agregaron los Roles Visitantes free, Usuarios registrados, Usuarios registrados Lite y Usuarios Registrados Premium.
         *
         */
        $RoleItems = [
            [
                'name'        => 'Presidente',
                'slug'        => 'presidente',
                'description' => '',
                'level'       => 10,
            ],
            [
                'name'        => 'Director General Financiero',
                'slug'        => 'director.general.financiero',
                'description' => '',
                'level'       => 9,
            ],
            [
                'name'        => 'Director de Compras',
                'slug'        => 'director.compras',
                'description' => '',
                'level'       => 8,
            ],
            [
                'name'        => 'Director Contabilidad',
                'slug'        => 'director.contabilidad',
                'description' => '',
                'level'       => 8,
            ],
            [
                'name'        => 'Asistente de Dirección',
                'slug'        => 'asistente.direccion',
                'description' => '',
                'level'       => 7,
            ],
            [
                'name'        => 'Recepción Lobby - Auxiliar',
                'slug'        => 'recepcion.lobby',
                'description' => '',
                'level'       => 7,
            ],
            [
                'name'        => 'Director de Marketing',
                'slug'        => 'director.marketing',
                'description' => '',
                'level'       => 6,
            ],
            [
                'name'        => 'Gerente de Marketing',
                'slug'        => 'gerente.marketing',
                'description' => '',
                'level'       => 5,
            ],
            [
                'name'        => 'Coordinador de Marketing',
                'slug'        => 'coordinador.marketing',
                'description' => '',
                'level'       => 4,
            ],
            [
                'name'        => 'Asesor de Marketing',
                'slug'        => 'asesor.marketing',
                'description' => '',
                'level'       => 3,
            ]			,
            [
                'name'        => 'Director Comercial',
                'slug'        => 'director.comercial',
                'description' => '',
                'level'       => 6,
            ],
            [
                'name'        => 'Gerente Comercial',
                'slug'        => 'gerente.comercial',
                'description' => '',
                'level'       => 5,
            ],
            [
                'name'        => 'Coordinador Comercial',
                'slug'        => 'coordinador.comercial',
                'description' => '',
                'level'       => 4,
            ],
            [
                'name'        => 'Asesor Comercial',
                'slug'        => 'asesor.comercial',
                'description' => '',
                'level'       => 3,
            ]			,
            [
                'name'        => 'Director Ejecutivo',
                'slug'        => 'director.ejecutivo',
                'description' => '',
                'level'       => 6,
            ],
            [
                'name'        => 'Gerente Ejecutivo',
                'slug'        => 'gerente.ejecutivo',
                'description' => '',
                'level'       => 5,
            ],
            [
                'name'        => 'Coordinador Ejecutivo',
                'slug'        => 'coordinador.ejecutivo',
                'description' => '',
                'level'       => 4,
            ],
            [
                'name'        => 'Asesor Ejecutivo',
                'slug'        => 'asesor.ejecutivo',
                'description' => '',
                'level'       => 3,
            ]			,
            [
                'name'        => 'Director Financiero',
                'slug'        => 'director.financiero',
                'description' => '',
                'level'       => 6,
            ],
            [
                'name'        => 'Gerente Financiero',
                'slug'        => 'gerente.financiero',
                'description' => '',
                'level'       => 5,
            ],
            [
                'name'        => 'Coordinador Financiero',
                'slug'        => 'coordinador.financiero',
                'description' => '',
                'level'       => 4,
            ],
            [
                'name'        => 'Asesor Financiero',
                'slug'        => 'asesor.financiero',
                'description' => '',
                'level'       => 3,
            ]			,
            [
                'name'        => 'Director Extranjería',
                'slug'        => 'director.extranjeria',
                'description' => '',
                'level'       => 6,
            ],
            [
                'name'        => 'Gerente Extranjería',
                'slug'        => 'gerente.extranjeria',
                'description' => '',
                'level'       => 5,
            ],
            [
                'name'        => 'Coordinador Extranjería',
                'slug'        => 'coordinador.extranjeria',
                'description' => '',
                'level'       => 4,
            ],
            [
                'name'        => 'Asesor Extranjería',
                'slug'        => 'asesor.extranjeria',
                'description' => '',
                'level'       => 3,
            ]			,
            [
                'name'        => 'Director Jurídico',
                'slug'        => 'director.juridico',
                'description' => '',
                'level'       => 6,
            ],
            [
                'name'        => 'Gerente Jurídico',
                'slug'        => 'gerente.juridico',
                'description' => '',
                'level'       => 5,
            ],
            [
                'name'        => 'Coordinador Jurídico',
                'slug'        => 'coordinador.juridico',
                'description' => '',
                'level'       => 4,
            ],
            [
                'name'        => 'Asesor Jurídico',
                'slug'        => 'asesor.juridico',
                'description' => '',
                'level'       => 3,
            ],
            [
                'name'        => 'Director Mercantil',
                'slug'        => 'director.mercantil',
                'description' => '',
                'level'       => 6,
            ],
            [
                'name'        => 'Gerente Mercantil',
                'slug'        => 'gerente.mercantil',
                'description' => '',
                'level'       => 5,
            ],
            [
                'name'        => 'Coordinador Mercantil',
                'slug'        => 'coordinador.mercantil',
                'description' => '',
                'level'       => 4,
            ],
            [
                'name'        => 'Asesor Mercantil',
                'slug'        => 'asesor.mercantil',
                'description' => '',
                'level'       => 3,
            ]			,
            [
                'name'        => 'Director de Tecnología',
                'slug'        => 'director.tecnologia',
                'description' => '',
                'level'       => 10,
            ],
            [
                'name'        => 'Gerente de Tecnología',
                'slug'        => 'gerente.tecnologia',
                'description' => '',
                'level'       => 5,
            ],
            [
                'name'        => 'Coordinador de Tecnología',
                'slug'        => 'coordinador.tecnologia',
                'description' => '',
                'level'       => 4,
            ],
            [
                'name'        => 'Asesor de Tecnología',
                'slug'        => 'asesor.tecnologia',
                'description' => '',
                'level'       => 3,
            ],
            [
                'name'        => 'Empleado',
                'slug'        => 'empleado',
                'description' => 'Son aquellas personas que están registradas como empleado, pero sin un Departamento Asignado',
                'level'       => 3,
            ],
            [
                'name'        => 'Usuario Premium mayor 6 meses',
                'slug'        => 'usuario.premium.mayor',
                'description' => 'Son aquellos usuarios registrados que han comprado el plan Premium y su llegada es mayor a 6 meses',
                'level'       => 2,
            ],
            [
                'name'        => 'Usuario Estándar mayor 6 meses',
                'slug'        => 'usuario.estandar.mayor',
                'description' => 'Son aquellos usuarios registrados que han comprado el plan Estándar y su llegada es mayor a 6 meses',
                'level'       => 2,
            ],
            [
                'name'        => 'Usuario Lite',
                'slug'        => 'usuario.lite',
                'description' => 'Son aquellos usuarios registrados que han comprado el plan Lite',
                'level'       => 2,
            ],
            [
                'name'        => 'Cliente Registrado',
                'slug'        => 'cliente.registrado',
                'description' => 'Son aquellos clientes que se han registrado a la plataforma, pero no han realizado pago alguno o suscrito a algún servicio',
                'level'       => 1,
            ],
            [
                'name'        => 'Usuario Free',
                'slug'        => 'usuario.free',
                'description' => 'Usuario no verificado',
                'level'       => 0,
            ],
            [
                'name'        => 'Usuario Premium menor 6 meses',
                'slug'        => 'usuario.premium.menor',
                'description' => 'Son aquellos usuarios registrados que han comprado el plan Premium y su llegada es menor a 6 meses',
                'level'       => 2,
            ],
            [
                'name'        => 'Usuario Estándar menor 6 meses',
                'slug'        => 'usuario.estandar.menor',
                'description' => 'Son aquellos usuarios registrados que han comprado el plan Estándar y su llegada es menor a 6 meses',
                'level'       => 2,
            ],
            [
                'name'        => 'Cliente Fase de Evaluación',
                'slug'        => 'cliente.fase.evaluacion',
                'description' => 'Son aquellos clientes que se encuentran en la Fase de Evaluación (Servicio)',
                'level'       => 2,
            ],
            [
                'name'        => 'Cliente Fase de Análisis',
                'slug'        => 'cliente.fase.analisis',
                'description' => 'Son aquellos clientes que se encuentran en la Fase de Fase de Análisis (Servicio)',
                'level'       => 2,
            ],
            [
                'name'        => 'Cliente Fase de Ejecución',
                'slug'        => 'cliente.fase.ejecucion',
                'description' => 'Son aquellos clientes que se encuentran en la Fase de Fase de Ejecución (Servicio)',
                'level'       => 2,
            ],
            [
                'name'        => 'Cliente Fase de Asesoramiento Integral',
                'slug'        => 'cliente.fase.asesoramiento.integral',
                'description' => 'Son aquellos clientes que se encuentran en la Fase de Asesoramiento Integral (Servicio)',
                'level'       => 2,
            ],
            [
                'name'        => 'Cliente Extranjería Primera Residencia',
                'slug'        => 'cliente.extranjeria.primera.residencia',
                'description' => 'Son aquellos clientes que están en el proceso para obtener su Primera Residenca',
                'level'       => 2,
            ],
            [
                'name'        => 'Cliente Extranjería Renovación de Primera Residencia',
                'slug'        => 'cliente.extranjeria.renovacion.residencia',
                'description' => 'Son aquellos clientes que están en el proceso de Renovación de su Primera Residencia',
                'level'       => 2,
            ],
            [
                'name'        => 'Cliente Extranjería Ciudadanía',
                'slug'        => 'cliente.extranjeria.ciudadania',
                'description' => 'Son aquellos clientes que están en el proceso de Ciudadanía',
                'level'       => 2,
            ]

        ];

        /*
         * Add Role Items
         *
         */
        foreach ($RoleItems as $RoleItem) {
            $newRoleItem = config('roles.models.role')::where('slug', '=', $RoleItem['slug'])->first();
            if ($newRoleItem === null) {
                $newRoleItem = config('roles.models.role')::create([
                    'name'          => $RoleItem['name'],
                    'slug'          => $RoleItem['slug'],
                    'description'   => $RoleItem['description'],
                    'level'         => $RoleItem['level'],
                ]);
            }
        }
    }
}
