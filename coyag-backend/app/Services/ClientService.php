<?php

namespace App\Services;

use App\Models\AddedService;
use App\Models\AssignedAdvisor;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use jeremykenedy\LaravelRoles\Models\Role;

class ClientService
{

    public function store(array $data,  bool $isFreeTrial = false) : ?Client
    {
        // TODO: ClientController->store debería usar este método
        //$token = Str::random(24);

        $user = new User();
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->flag_login = true;
        //$user->observation_flag_login = 'Email sin verificar - ' . $token;
        $user->observation_flag_login = null;
        $user->email_verified_at = now();

        if(!$user->save()) return null;

        $client = new Client();
        $client->user_id = $user->id;
        $client->converted_to_registered_date = Carbon::now()->toDateTimeString();
        $auxIdPublic;
        while (true)
        {
            $auxIdPublic = Str::random(32);
            $auxC = Client::where('id_public', $auxIdPublic)->count();
            $auxE = Employee::where('id_public', $auxIdPublic)->count();
            if($auxC == 0 && $auxE == 0)
                break;
        }

        $client->id_public = $auxIdPublic;

        if(!$client->save()) {
            $user->forceDelete();
            return null;
        }

        if(isset(Auth::user()->employee->id))
            addClientTimeline($client->id, Auth::user()->employee->id, 'Client', 'create', false); //Lo registró un empleado
        else
            addClientTimeline($client->id, 1, 'Client', 'create', true); //El cliente fue quien se registró

        $clienteRegistradoRole = config('roles.models.role')::where('slug', '=', 'cliente.registrado')->first();
        $user->attachRole($clienteRegistradoRole);

        notificationRegistroCliente($client);

        /*
        try {
            Mail::to($user->email)->send(new EmailVerification($token));
        } catch(Exception $e) {
            Log::error("Error - COYAG -> $e");
        }
        */

        if($isFreeTrial)
            $this->addFreeTrial($client);

        return $client;
    }

    public function addFreeTrial(Client $client)
    {
        // Removiendo plan anterior, solo si existiese

        $this->removeService($client, 'Plan');

        // Agregando el Free Trial

        $service = Service::where('slug', 'plan.free.trial')->first();
        $role = Role::where('slug', $service->roles_slug)->first();
        $newRole = config('roles.models.role')::find($role->id);
        $client->user->attachRole($newRole);

        $addedService = storeAddedService($client->id, $service->id, 'create');

        $auxAssignedAdvisor = AssignedAdvisor::where('client_id', $client->id)->first();

        if(!$auxAssignedAdvisor)
            $employeeAssignAdvisorID = assignAdvisor($client->id, 1);

        notificationCambioPlanExtranjeriaCliente($client, $role->slug);
    }

    private function removeService($client, $type)
    {
        // Remueve un Plan de VideoPortal (Plan) o de extranjería (Inmigration)
        // Verificar si tiene algún servicio de ese tipo

        $countAddedService = AddedService::where('client_id', $client->id)
                                ->where('flag_active_plan', true)
                                ->where('service_id', getIdServiceOfClientIndicatingTheTypeOfService($client, $type))
                                ->count();

        if($countAddedService == 0)
            return false;

        // Remover servicio

        AddedService::where('client_id', $client->id)
                        ->where('flag_active_plan', true)
                        ->where('service_id', getIdServiceOfClientIndicatingTheTypeOfService($client, $type))
                        ->update(['flag_active_plan' => false, 'plan_deactivated_at' => Carbon::now()->toDateTimeString()]);


        // Remover Rol

        $role = getRoleOfClientIndicatingTheTypeOfService($client, $type);
        if($role->slug != 'cliente.registrado')
        {   // Supone un cliente sin otro servicio tipo inmigration asociado
            $oldRole = config('roles.models.role')::find($role->id);
            $client->user->detachRole( $oldRole);
        }


        // Remover Asesor, solo si aplica

        $auxCountAS = AddedService::where('client_id', $client->id)
                                ->where('flag_active_plan', true)
                                ->count();

        if($auxCountAS == 0)
            AssignedAdvisor::where('client_id', $client->id)->delete();

        return true;

    }

}
