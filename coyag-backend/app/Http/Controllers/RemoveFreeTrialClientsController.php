<?php

namespace App\Http\Controllers;

use App\Models\AddedService;
use App\Models\AssignedAdvisor;
use App\Models\Client;
use App\Models\ClientTimeline;
use App\Models\EmailManagement;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RemoveFreeTrialClientsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        /*
            Este método solo destruye los clientes tipo freetrial que:
                - Solo deben ser clientes que se hayan registrado con carga masiva
                - No se hayan autenticado

            Validaciones extras:
                - En ClientTimeline no deben tener negocios visto
                - En ClientTimeline no deben tener búsquedas
                - No deben tener negocios favoritos
                - En AddedService no deben tener otros servicios diferentes a freetrial
                - En AddedService no debe tener algún servicio activo

        */

        $auxError = false;

        $today = Carbon::now()->format('d-m-Y H:i:s');

        Log::info('=====================================================');
        Log::info('Inicio borrado de Clientes Free Trial con condiciones');
        Log::info('Fecha: ' . $today);
        Log::info('======================================================');

        foreach (Client::where('registration_method', 'Carga Masiva')
                    ->get() as $key => $client
                )
        {

            if(ClientTimeline::where('client_id', $client->id)
                                ->where('module_eng', 'Auth')
                                ->where('type_crud_eng', 'create')
                                ->count() > 0)
                continue;


            if(ClientTimeline::where('client_id', $client->id)
                                ->where('module_eng', 'Business')
                                ->where('type_crud_eng', 'show')
                                ->count() > 0)
                continue;


            if(ClientTimeline::where('client_id', $client->id)
                                ->where('module_eng', 'Business')
                                ->where('type_crud_eng', 'list')
                                ->count() > 0)
                continue;


            // Negocios favoritos
            if(count($client->businesses) > 0)
                continue;


            if(AddedService::where('client_id', $client->id)
                            ->where('service_id', '!=' , 48)
                            ->count() > 0)
                continue;

            if(AddedService::where('client_id', $client->id)
                            ->where('flag_active_plan', true)
                            ->count() > 0)
                continue;

            try {
                Log::info($client->id . ' - ' . $client->user->email);

                DB::transaction(function() use ($client) {
                    foreach (AddedService::where('client_id', $client->id)->get() as $key => $addedS)
                        $addedS->forceDelete();

                    AddedService::where('client_id', $client->id)->forceDelete();
                    AssignedAdvisor::where('client_id', $client->id)->forceDelete();
                    ClientTimeline::where('client_id', $client->id)->forceDelete();
                    EmailManagement::where('client_id', $client->id)->forceDelete();
                    Notification::where('client_id', $client->id)->forceDelete();

                    $user = $client->user;
                    $client->forceDelete();
                    $user->forceDelete();
                });

            } catch (Exception $e) {
                Log::info('----- Error al eliminar al Cliente ----');
                $auxError = true;
            }

        }

        $today = Carbon::now()->format('d-m-Y H:i:s');

        Log::info('==================================================');
        Log::info('Fin borrado de Clientes Free Trial con condiciones');
        Log::info('Fecha: ' . $today);
        Log::info('==================================================');

        return response()->json([
            'status'    => 'success',
            'error'     => $auxError ? 'Hubo al menos un error en el borrado' : 'No se detectaron errores en el borrado',
        ], 200);

    }
}
