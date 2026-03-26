<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Client::create([
            'user_id'               =>      44,
            'first_nationality_id'  =>      3,
            'second_nationality_id'  =>      2,
            'phone_mobile'          =>      '+585654611',
            'country_id'            =>      3,
            'names'                 =>      'Cliente',
            'surnames'              =>      'Premium',
            'converted_to_premium_date'     =>  '2020-07-28 00:19:57',
            'id_public'             =>      Str::random(32)
        ]);

        Client::create([
            'user_id'               =>      45,
            'first_nationality_id'  =>      3,
            'second_nationality_id'  =>      2,
            'phone_mobile'          =>      '+585654612',
            'country_id'            =>      3,
            'names'                 =>      'Cliente',
            'surnames'              =>      'Estándar',
            'converted_to_standard_date'     =>  '2020-07-28 00:19:57',
            'id_public'             =>      Str::random(32)
        ]);

        Client::create([
            'user_id'               =>      46,
            'phone_mobile'          =>      '+585654613',
            'country_id'            =>      2,
            'names'                 =>      'Cliente',
            'surnames'              =>      'Lite',
            'converted_to_lite_date'        =>  '2020-07-25 00:19:57',
            'id_public'             =>      Str::random(32)
        ]);

        Client::create([
            'user_id'               =>      47,
            'names'                 =>      'Cliente',
            'surnames'              =>      'Registrado',
            'converted_to_registered_date'  =>  '2020-07-25 00:19:57',
            'id_public'             =>      Str::random(32)
        ]);
    }
}
