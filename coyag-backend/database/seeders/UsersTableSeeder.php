<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $empleadoRole = config('roles.models.role')::where('name', '=', 'Empleado')->first();
        $presidenteRole = config('roles.models.role')::where('name', '=', 'Presidente')->first();
        $directorTecnologiaRole = config('roles.models.role')::where('name', '=', 'Director de Tecnología')->first();
        $recepcionLobbyRole = config('roles.models.role')::where('name', '=', 'Recepción Lobby - Auxiliar')->first();
        $directorComercialRole = config('roles.models.role')::where('name', '=', 'Director Comercial')->first();
        $gerenteComercialRole = config('roles.models.role')::where('name', '=', 'Gerente Comercial')->first();
        $coordinadorComercialRole = config('roles.models.role')::where('name', '=', 'Coordinador Comercial')->first();
        $asesorComercialRole = config('roles.models.role')::where('name', '=', 'Asesor Comercial')->first();
        $directorEjecutivoRole = config('roles.models.role')::where('name', '=', 'Director Ejecutivo')->first();
        $gerenteEjecutivoRole = config('roles.models.role')::where('name', '=', 'Gerente Ejecutivo')->first();
        $coordinadorEjecutivoRole = config('roles.models.role')::where('name', '=', 'Coordinador Ejecutivo')->first();
        $asesorEjecutivoRole = config('roles.models.role')::where('name', '=', 'Asesor Ejecutivo')->first();
        $usuarioPremiumRole = config('roles.models.role')::where('name', '=', 'Usuario Premium mayor 6 meses')->first();
        $usuarioEstandarRole = config('roles.models.role')::where('name', '=', 'Usuario Estándar mayor 6 meses')->first();
        $usuarioLiteRole = config('roles.models.role')::where('name', '=', 'Usuario Lite')->first();
        $usuarioRegistradoRole = config('roles.models.role')::where('name', '=', 'Usuario Registrado')->first();
        $permissions = config('roles.models.permission')::all();

        $directorExtranjeriaRole = config('roles.models.role')::where('name', '=', 'Director Extranjería')->first();
        $gerenteExtranjeriaRole = config('roles.models.role')::where('name', '=', 'Gerente Extranjería')->first();
        $coordinadorExtranjeriaRole = config('roles.models.role')::where('name', '=', 'Coordinador Extranjería')->first();
        $asesorExtranjeriaRole = config('roles.models.role')::where('name', '=', 'Asesor Extranjería')->first();

        $directorMercantilRole = config('roles.models.role')::where('name', '=', 'Director Mercantil')->first();
        $gerenteMercantilRole = config('roles.models.role')::where('name', '=', 'Gerente Mercantil')->first();
        $coordinadorMercantilRole = config('roles.models.role')::where('name', '=', 'Coordinador Mercantil')->first();
        $asesorMercantilRole = config('roles.models.role')::where('name', '=', 'Asesor Mercantil')->first();

        $directorFinancieroRole = config('roles.models.role')::where('name', '=', 'Director Financiero')->first();
        $gerenteFinancieroRole = config('roles.models.role')::where('name', '=', 'Gerente Financiero')->first();
        $coordinadorFinancieroRole = config('roles.models.role')::where('name', '=', 'Coordinador Financiero')->first();
        $asesorFinancieroRole = config('roles.models.role')::where('name', '=', 'Asesor Financiero')->first();

        $directorContabilidadRole = config('roles.models.role')::where('name', '=', 'Director Contabilidad')->first();

        $directorMarketingRole = config('roles.models.role')::where('name', '=', 'Director de Marketing')->first();
        $gerenteMarketingRole = config('roles.models.role')::where('name', '=', 'Gerente de Marketing')->first();
        $coordinadorMarketingRole = config('roles.models.role')::where('name', '=', 'Coordinador de Marketing')->first();
        $asesorMarketingRole = config('roles.models.role')::where('name', '=', 'Asesor de Marketing')->first();


        /*
         * Add Users
         *
         */

        // SISTEMA
        if (config('roles.models.defaultUser')::where('email', '=', 'system@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'system@coyag.es',
                'password'      =>  bcrypt(Str::random(18)),
                'flag_login'    =>  false,
                'observation_flag_login'    =>  'El sistema no necesita autenticarse'
            ]);

            $newUser->attachRole($empleadoRole);
            foreach ($permissions as $permission) {
                $newUser->attachPermission($permission);
            }
        }

        // PRESIDENCIA
        if (config('roles.models.defaultUser')::where('email', '=', 'gabriel@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'gabriel@coyag.es',
                'password'      =>  bcrypt('password'),
                'flag_login'    =>  true,
                'observation_flag_login'    =>  null
            ]);

            $newUser->attachRole($presidenteRole);
            foreach ($permissions as $permission) {
                $newUser->attachPermission($permission);
            }
        }


        // TECNOLOGÍA
        if (config('roles.models.defaultUser')::where('email', '=', 'humberto@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'humberto@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($directorTecnologiaRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'gustavo@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'gustavo@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($directorTecnologiaRole);
        }

        // LOBBY
        if (config('roles.models.defaultUser')::where('email', '=', 'recepcionlobby1@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'recepcionlobby1@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($recepcionLobbyRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'recepcionlobby2@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'recepcionlobby2@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($recepcionLobbyRole);
        }

        // COMERCIAL
        if (config('roles.models.defaultUser')::where('email', '=', 'directorcomercial@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'directorcomercial@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($directorComercialRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'gerentecomercial@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'gerentecomercial@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($gerenteComercialRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'coordinadorcomercial@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'coordinadorcomercial@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($coordinadorComercialRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'asesorcomercial1@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'asesorcomercial1@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($asesorComercialRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'asesorcomercial2@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'asesorcomercial2@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($asesorComercialRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'asesorcomercial3@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'asesorcomercial3@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($asesorComercialRole);
        }

        // EJECUTIVO
        if (config('roles.models.defaultUser')::where('email', '=', 'directorejecutivo@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'directorejecutivo@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($directorEjecutivoRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'gerenteejecutivo@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'gerenteejecutivo@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($gerenteEjecutivoRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'coordinadorejecutivo@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'coordinadorejecutivo@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($coordinadorEjecutivoRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'asesorejecutivo1@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'asesorejecutivo1@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($asesorEjecutivoRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'asesorejecutivo2@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'asesorejecutivo2@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($asesorEjecutivoRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'asesorejecutivo3@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'asesorejecutivo3@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($asesorEjecutivoRole);
        }


        // EXTRANJERÍA
        if (config('roles.models.defaultUser')::where('email', '=', 'directorextranjeria@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'directorextranjeria@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($directorExtranjeriaRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'gerenteextranjeria@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'gerenteextranjeria@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($gerenteExtranjeriaRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'coordinadorextranjeria@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'coordinadorextranjeria@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($coordinadorExtranjeriaRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'asesorextranjeria1@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'asesorextranjeria1@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($asesorExtranjeriaRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'asesorextranjeria2@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'asesorextranjeria2@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($asesorExtranjeriaRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'asesorextranjeria3@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'asesorextranjeria3@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($asesorExtranjeriaRole);
        }


        // MERCANTIL
        if (config('roles.models.defaultUser')::where('email', '=', 'directormercantil@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'directormercantil@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($directorMercantilRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'gerentemercantil@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'gerentemercantil@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($gerenteMercantilRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'coordinadormercantil@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'coordinadormercantil@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($coordinadorMercantilRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'asesormercantil1@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'asesormercantil1@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($asesorMercantilRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'asesormercantil2@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'asesormercantil2@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($asesorMercantilRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'asesormercantil3@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'asesormercantil3@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($asesorMercantilRole);
        }


        // FINANCIERO
        if (config('roles.models.defaultUser')::where('email', '=', 'directorfinanciero@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'directorfinanciero@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($directorFinancieroRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'gerentefinanciero@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'gerentefinanciero@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($gerenteFinancieroRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'coordinadorfinanciero@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'coordinadorfinanciero@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($coordinadorFinancieroRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'asesorfinanciero1@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'asesorfinanciero1@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($asesorFinancieroRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'asesorfinanciero2@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'asesorfinanciero2@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($asesorFinancieroRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'asesorfinanciero3@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'asesorfinanciero3@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($asesorFinancieroRole);
        }


        // CONTABILIDAD
        if (config('roles.models.defaultUser')::where('email', '=', 'directorcontabilidad@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'directorcontabilidad@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($directorContabilidadRole);
        }


        // MARKETING
        if (config('roles.models.defaultUser')::where('email', '=', 'directormarketing@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'directormarketing@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($directorMarketingRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'gerentemarketing@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'gerentemarketing@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($gerenteMarketingRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'coordinadormarketing@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'coordinadormarketing@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($coordinadorMarketingRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'asesormarketing1@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'asesormarketing1@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($asesorMarketingRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'asesormarketing2@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'asesormarketing2@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($asesorMarketingRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'asesormarketing3@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'asesormarketing3@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($asesorMarketingRole);
        }



        // CLIENTES
        if (config('roles.models.defaultUser')::where('email', '=', 'usuariopremium@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'usuariopremium@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($usuarioPremiumRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'usuarioestandar@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'usuarioestandar@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($usuarioEstandarRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'usuariolite@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'usuariolite@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($usuarioLiteRole);
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'usuarioregistrado@coyag.es')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'email'         => 'usuarioregistrado@coyag.es',
                'password'      => bcrypt('password'),
                'flag_login'    => true,
                'observation_flag_login'    =>  null
            ]);

            $newUser;
            $newUser->attachRole($usuarioRegistradoRole);
        }


    }
}
