<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ConnectRelationshipsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Por ahora se asigna todos los permisos a algunos roles, pero cuando se especifique mejor las funcionalidades
         * de cada rol, así como todos los permisos, este seeder cambiará
         */

        /* Obteniendo todos los permisos */
        $permissions = config('roles.models.permission')::all();


        // PRESIDENCIA
        $rolePresidencia = config('roles.models.role')::where('name', '=', 'Presidente')->first();
        foreach ($permissions as $permission) {
            $rolePresidencia->attachPermission($permission);
        }


        // GERENCIA DE TECNOLOGÍA
        $roleTech = config('roles.models.role')::where('name', '=', 'Director de Tecnología')->first();
        foreach ($permissions as $permission) {
            $roleTech->attachPermission($permission);
        }


        // EMPLEADOS CREADOS DE LOBBY
        $roleRecepcionLobby = config('roles.models.role')::where('name', '=', 'Recepción Lobby - Auxiliar')->first();
        foreach ($permissions as $permission) {
            $roleRecepcionLobby->attachPermission($permission);
        }


        // EMPLEADOS CREADOS DE COMERCIAL
        $roleDirectorComercial = config('roles.models.role')::where('name', '=', 'Director Comercial')->first();
        foreach ($permissions as $permission) {
            $roleDirectorComercial->attachPermission($permission);
        }

        $roleGerenteComercial = config('roles.models.role')::where('name', '=', 'Gerente Comercial')->first();
        foreach ($permissions as $permission) {
            $roleGerenteComercial->attachPermission($permission);
        }

        $roleCoordinadorComercial = config('roles.models.role')::where('name', '=', 'Coordinador Comercial')->first();
        foreach ($permissions as $permission) {
            $roleCoordinadorComercial->attachPermission($permission);
        }

        $roleAsesorComercial = config('roles.models.role')::where('name', '=', 'Asesor Comercial')->first();
        foreach ($permissions as $permission) {
            $roleAsesorComercial->attachPermission($permission);
        }


        // EMPLEADOS CREADOS DE EJECUTIVO
        $roleDirectorEjecutivo = config('roles.models.role')::where('name', '=', 'Director Ejecutivo')->first();
        foreach ($permissions as $permission) {
            $roleDirectorEjecutivo->attachPermission($permission);
        }

        $roleGerenteEjecutivo = config('roles.models.role')::where('name', '=', 'Gerente Ejecutivo')->first();
        foreach ($permissions as $permission) {
            $roleGerenteEjecutivo->attachPermission($permission);
        }

        $roleCoordinadorEjecutivo = config('roles.models.role')::where('name', '=', 'Coordinador Ejecutivo')->first();
        foreach ($permissions as $permission) {
            $roleCoordinadorEjecutivo->attachPermission($permission);
        }

        $roleAsesorEjecutivo = config('roles.models.role')::where('name', '=', 'Asesor Ejecutivo')->first();
        foreach ($permissions as $permission) {
            $roleAsesorEjecutivo->attachPermission($permission);
        }


        // EMPLEADOS CREADOS DE EXTRANJERÍA
        $roleDirectorEjecutivo = config('roles.models.role')::where('name', '=', 'Director Extranjería')->first();
        foreach ($permissions as $permission) {
            $roleDirectorEjecutivo->attachPermission($permission);
        }

        $roleGerenteEjecutivo = config('roles.models.role')::where('name', '=', 'Gerente Extranjería')->first();
        foreach ($permissions as $permission) {
            $roleGerenteEjecutivo->attachPermission($permission);
        }

        $roleCoordinadorEjecutivo = config('roles.models.role')::where('name', '=', 'Coordinador Extranjería')->first();
        foreach ($permissions as $permission) {
            $roleCoordinadorEjecutivo->attachPermission($permission);
        }

        $roleAsesorEjecutivo = config('roles.models.role')::where('name', '=', 'Asesor Extranjería')->first();
        foreach ($permissions as $permission) {
            $roleAsesorEjecutivo->attachPermission($permission);
        }


        // EMPLEADOS CREADOS DE MERCANTIL
        $roleDirectorEjecutivo = config('roles.models.role')::where('name', '=', 'Director Mercantil')->first();
        foreach ($permissions as $permission) {
            $roleDirectorEjecutivo->attachPermission($permission);
        }

        $roleGerenteEjecutivo = config('roles.models.role')::where('name', '=', 'Gerente Mercantil')->first();
        foreach ($permissions as $permission) {
            $roleGerenteEjecutivo->attachPermission($permission);
        }

        $roleCoordinadorEjecutivo = config('roles.models.role')::where('name', '=', 'Coordinador Mercantil')->first();
        foreach ($permissions as $permission) {
            $roleCoordinadorEjecutivo->attachPermission($permission);
        }

        $roleAsesorEjecutivo = config('roles.models.role')::where('name', '=', 'Asesor Mercantil')->first();
        foreach ($permissions as $permission) {
            $roleAsesorEjecutivo->attachPermission($permission);
        }


        // EMPLEADOS CREADOS DE FINANCIERO
        $roleDirectorEjecutivo = config('roles.models.role')::where('name', '=', 'Director Financiero')->first();
        foreach ($permissions as $permission) {
            $roleDirectorEjecutivo->attachPermission($permission);
        }

        $roleGerenteEjecutivo = config('roles.models.role')::where('name', '=', 'Gerente Financiero')->first();
        foreach ($permissions as $permission) {
            $roleGerenteEjecutivo->attachPermission($permission);
        }

        $roleCoordinadorEjecutivo = config('roles.models.role')::where('name', '=', 'Coordinador Financiero')->first();
        foreach ($permissions as $permission) {
            $roleCoordinadorEjecutivo->attachPermission($permission);
        }

        $roleAsesorEjecutivo = config('roles.models.role')::where('name', '=', 'Asesor Financiero')->first();
        foreach ($permissions as $permission) {
            $roleAsesorEjecutivo->attachPermission($permission);
        }


        // EMPLEADOS CREADOS DE MARKETING
        $roleDirectorEjecutivo = config('roles.models.role')::where('name', '=', 'Director de Marketing')->first();
        foreach ($permissions as $permission) {
            $roleDirectorEjecutivo->attachPermission($permission);
        }

        $roleGerenteEjecutivo = config('roles.models.role')::where('name', '=', 'Gerente de Marketing')->first();
        foreach ($permissions as $permission) {
            $roleGerenteEjecutivo->attachPermission($permission);
        }

        $roleCoordinadorEjecutivo = config('roles.models.role')::where('name', '=', 'Coordinador de Marketing')->first();
        foreach ($permissions as $permission) {
            $roleCoordinadorEjecutivo->attachPermission($permission);
        }

        $roleAsesorEjecutivo = config('roles.models.role')::where('name', '=', 'Asesor de Marketing')->first();
        foreach ($permissions as $permission) {
            $roleAsesorEjecutivo->attachPermission($permission);
        }


        // EMPLEADOS CREADOS DE CONTABILIDAD
        $roleDirectorEjecutivo = config('roles.models.role')::where('name', '=', 'Director Contabilidad')->first();
        foreach ($permissions as $permission) {
            $roleDirectorEjecutivo->attachPermission($permission);
        }


    }
}
