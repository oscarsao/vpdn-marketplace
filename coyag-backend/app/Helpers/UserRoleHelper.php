<?php

use App\Models\AddedService;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use jeremykenedy\LaravelRoles\Models\Role;

function checkIfTheUserHasTheRole($roles, $userRoles, string $checkByIdOrSlug) : bool
{
    /**
     * Comprueba si un usuario tiene un slug dentro del arreglo de roles enviados
     *
     * $roles, puede ser un string o un array
     * $userRoles contiene los roles (getRoles()) del usuario que se quiere validar
     *          pudiendo ser Auth::user()->getRoles(), $employee->user->getRoles() o $client->user->getRoles()
     * $checkByIdOrSlug es un string que debe recibir 'id' o 'slug'
     */

     if(is_int($roles))
        $roles = strval($roles);

     $arrRoles = (is_string($roles)) ? explode(',', $roles) : $roles;

     for($i = 0; $i < count($userRoles); $i++)
     {
        if($checkByIdOrSlug == 'id')
        {
            if(in_array($userRoles[$i]->id, $arrRoles))
                return true;
        }
        else
        {
            if(in_array($userRoles[$i]->slug, $arrRoles))
                return true;
        }
     }

     return false;
}

function getRoleOfClientIndicatingTheTypeOfService(Client $client, string $typeOfService) : Role
{
    /**
     * Retorna el Rol del Cliente acorde al Tipo de Servicio que se recibe
     * Si el cliente no tiene un rol asociado a dicho servicio retornará cliente.registrado
     *
     * Se presume que typeOfService tenga alguno de los posibles valores:
     *      Plan o Inmigration
     */

    $clientRoles = $client->user->getRoles();

    $service = null;

    for($i = 0; $i < count($clientRoles); $i++)
    {
        $service = Service::where('roles_slug', $clientRoles[$i]->slug)
                    ->where('type', $typeOfService)
                    ->where('id', '!=', 1)
                    ->first();

        if($service)
            break;
    }

    if($service)
        return Role::where('slug', $service->roles_slug)->first();

    return Role::where('slug', 'cliente.registrado')->first();
}

function getIdServiceOfClientIndicatingTheTypeOfService(Client $client, string $typeOfService)
{
    /**
     * Retorna el Servicio del Cliente acorde al Tipo de Servicio que se recibe
     * Si el cliente no tiene un rol asociado a dicho servicio retornará cliente.registrado
     *
     * Se presume que typeOfService tenga alguno de los posibles valores:
     *      Plan o Inmigration
     */

    $addedServices = AddedService::where('client_id', $client->id)
                                ->where('flag_active_plan', true)
                                ->get();

    $service;

    for($i = 0; $i < count($addedServices); $i++)
    {
        if($addedServices[$i]->service_id != 1)
        {
            $service = Service::where('type', $typeOfService)
                                ->where('id', $addedServices[$i]->service_id)
                                ->first();

            if($service)
                return $service->id;
        }
    }

    return 0;

}

