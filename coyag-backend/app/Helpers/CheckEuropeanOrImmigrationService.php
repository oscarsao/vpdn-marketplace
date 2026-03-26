<?php

use App\Models\AddedService;
use App\Models\Client;
use App\Models\Country;
use App\Models\Service;

function checkEuropeanOrImmigrationService(Client $client) : bool
{
    /*
     * Comprueba si el cliente es europeo o tiene algún servicio de extranjería activo
     * Retorna true si esta condición se cumple
     *
     * */

    // Validar la primera nacionalidad
    if($client->first_nationality_id)
    {
        $country = Country::find($client->first_nationality_id);
        if(in_array($country->continent->name, ['Europa', 'Europa - UE']))
            return true;
    }


    // Validar la segunda nacionalidad
    if($client->second_nationality_id)
    {
        $country = Country::find($client->second_nationality_id);
        if(in_array($country->continent->name, ['Europa', 'Europa - UE']))
            return true;
    }


    // Validar si tiene algún servicio de extranjería activo
    $services = Service::where('type', 'Inmigration')->select('id')->get();
    $servicesId = [];

    for($i = 0; $i < count($services); $i++)
        array_push($servicesId, $services[$i]['id']);

    $auxCountAS = AddedService::where('client_id', $client->id)
                    ->whereIn('service_id', $servicesId)
                    ->where('flag_active_plan', true)
                    ->count();

    if($auxCountAS > 0)
        return true;


    return false;

}
