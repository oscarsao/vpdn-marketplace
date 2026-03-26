<?php

namespace App\Traits;

use App\Models\AddedService;
use App\Models\Client;
use App\Models\Service;

trait ClientTrait
{

    public static function checkActiveServiceType(Client $client, $serviceType = null) : bool
    {
        /*
            Determina si el cliente tiene un Servicio activo del tipo frontend

            $serviceType no puede ser un AddOn
        */

        if(!empty($serviceType) && $serviceType == 'AddOn')
            return false;

        if(Service::where('type', $serviceType)->count() == 0)
            return false;

        $countAddedService = AddedService::leftJoin('services', 'services.id', '=', 'added_services.service_id')
                                ->where('added_services.client_id', $client->id)
                                ->where('added_services.flag_active_plan', true)
                                ->where('services.type', $serviceType)
                                ->count();

        return ($countAddedService > 0) ? true : false;
    }


    public static function checkActiveServiceTypeFrontend(Client $client, $frontend = null) : bool
    {
        /*
            Determina si el cliente tiene un Servicio activo del tipo frontend
        */
        $type;

        switch($frontend) {
            case 'extranjeria': $type = 'Inmigration'; break;
            case 'videoportal': $type = 'Plan'; break;
            default: return false;
        }

        $countAddedService = AddedService::leftJoin('services', 'services.id', '=', 'added_services.service_id')
                                ->where('added_services.client_id', $client->id)
                                ->where('added_services.flag_active_plan', true)
                                ->where('services.type', $type)
                                ->count();

        return ($countAddedService > 0) ? true : false;
    }


}
