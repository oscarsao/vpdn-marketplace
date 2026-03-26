<?php

namespace App\Console\Commands;

use App\Models\AddedService;
use App\Models\AssignedAdvisor;
use App\Models\Client;
use App\Traits\EmailManagementTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Notification;

class PlanExpirationCommand extends Command
{
    use EmailManagementTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:plan-expiration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía notificación previa y al vencer el Plan del Cliente';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() :  void
    {

        $today = Carbon::now()->format('d-m-Y H:i:s');

        Log::info('======================================');
        Log::info('Inicio de notification:plan-expiration');
        Log::info('Fecha: ' . $today);
        Log::info('======================================');

        //Obtener clientes con planes activos

        $addServs = AddedService::leftJoin('services', 'services.id', '=', 'added_services.service_id')
                                        ->leftJoin('clients', 'clients.id', '=', 'added_services.client_id')
                                        ->leftJoin('users', 'users.id', '=', 'clients.user_id')
                                        ->where('services.type', 'Plan')
                                        ->where('added_services.flag_active_plan', true)
                                        ->select('added_services.id as id', 'added_services.created_at as created_at', 'clients.id as id_client', 'clients.names as names_client', 'clients.surnames as surnames_client', 'services.name as name_service', 'services.slug as slug_service', 'users.email as email_user');


        /****************************************************************************************************************
        ************************ Notificación Previa al vencimiento del plan ********************************************
        *****************************************************************************************************************/

        $addServsAnnual = clone $addServs;
        $addServsAnnual = $addServsAnnual->where('services.slug', 'plan.anual')
                                                    ->whereMonth('added_services.created_at', Carbon::now()->addDays(7)->format('m'))
                                                    ->whereDay('added_services.created_at', Carbon::now()->addDays(7)->format('d'))
                                                    ->get();

        $addServsMonthly = clone $addServs;
        $addServsMonthly = $addServsMonthly->where('services.slug', 'plan.mensual')
                                                ->whereDay('added_services.created_at', Carbon::now()->addDays(3)->format('d'))
                                                ->get();

        $addServsFreeTrial = clone $addServs;
        $addServsFreeTrial = $addServsFreeTrial->where('services.slug', 'plan.free.trial')
                                                ->whereDate('added_services.created_at', Carbon::now()->subDays(4)->toDateString())
                                                ->get();

        foreach ($addServsAnnual as $addServ)
            $this->planExpirationNotification($addServ, 'pre.notification');

        foreach ($addServsMonthly as $addServ)
            $this->planExpirationNotification($addServ, 'pre.notification');

        foreach ($addServsFreeTrial as $addServ)
            $this->planExpirationNotification($addServ, 'pre.notification');




        /****************************************************************************************************************
        ******************************** Notificación al vencer el plan *************************************************
        *****************************************************************************************************************/

        $addServsAnnual = clone $addServs;
        $addServsAnnual = $addServsAnnual->where('services.slug', 'plan.anual')
                                                    ->whereMonth('added_services.created_at', Carbon::now()->format('m'))
                                                    ->whereDay('added_services.created_at', Carbon::now()->format('d'))
                                                    ->get();

        $addServsMonthly = clone $addServs;
        $addServsMonthly = $addServsMonthly->where('services.slug', 'plan.mensual')
                                                ->whereDay('added_services.created_at', Carbon::now()->format('d'))
                                                ->get();

        $addServsFreeTrial = clone $addServs;
        $addServsFreeTrial = $addServsFreeTrial->where('services.slug', 'plan.free.trial')
                                                ->whereDate('added_services.created_at', Carbon::now()->subDays(7)->toDateString())
                                                ->get();

        foreach ($addServsAnnual as $addServ)
            $this->planExpirationNotification($addServ, 'notification');

        foreach ($addServsMonthly as $addServ)
            $this->planExpirationNotification($addServ, 'notification');

        foreach ($addServsFreeTrial as $addServ)
            $this->planExpirationNotification($addServ, 'notification');


        /****************************************************************************************************************
        ************************************ Suspensión del Plan ********************************************************
        *****************************************************************************************************************/

        $addServsFreeTrial = clone $addServs;
        $addServsFreeTrial = $addServsFreeTrial->where('services.slug', 'plan.free.trial')
                                                ->whereDate('added_services.created_at', Carbon::now()->subDays(8)->toDateString())
                                                ->get();

        foreach ($addServsFreeTrial as $addServ)
            $this->removeService($addServ);


        Log::info('===================================');
        Log::info('Fin de notification:plan-expiration');
        Log::info('===================================');

    }


    private function removeService($addServ): void
    {
        $client = Client::find($addServ->id_client);
        $type = 'Plan';

        // Remover servicio

        AddedService::where('id', $addServ->id)
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


        // Agregar Timeline cuando se le retira el Plan
        addClientTimeline($client->id, 1, 'AddedService', 'delete', false);
    }

}
