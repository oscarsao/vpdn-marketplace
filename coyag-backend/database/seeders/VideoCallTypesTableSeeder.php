<?php

namespace Database\Seeders;

use App\Models\VideoCallType;
use Illuminate\Database\Seeder;

class VideoCallTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VideoCallType::create([
            'name'  =>  'Reuniones con Equipo',
            'slug'  =>  'reuniones.equipo',
            'Description'  =>  'Se efectúa entre el cliente y el Asesor de CyA desde un espacio cómodo para el Asesor (oficina, casa...). Se puede hablar de un o varios Negocios, saber la opinión del Asesor para escoger un Negocio, entre otros. Se podría decir que es Asesoría general.',
            'business_number'  =>  'max'
        ]);

        VideoCallType::create([
            'name'  =>  'Conexiones desde Negocios',
            'slug'  =>  'conexiones.negocios',
            'Description'  =>  'Se efectúa entre el cliente y el Asesor de CyA. El Asesor debe encontrarse en el Negocio indicado por el cliente. El cliente podría solicitar fotos del Negocio o pedir algún tipo de información adicional que solo se obtendría en el lugar del mismo.',
            'business_number'  =>  '1'
        ]);
    }
}
