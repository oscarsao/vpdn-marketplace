<?php

namespace App\Console\Commands;

use App\Models\AddedService;
use App\Models\Business;
use App\Models\BusinessMultimedia;
use App\Models\Client;
use App\Notifications\NewBusinessVideoNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Notification;



class SendBusinessVideoEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:new-business-video';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía una Notificación (Email) a los clientes activos, indicándole que se ha carga un nuevo Video Análisis de Negocio';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {

        $businessMultimedia = BusinessMultimedia::whereDate('created_at', Carbon::yesterday()->format('Y-m-d'))
                                                    ->where('type', 'video')
                                                    ->whereIn('type_client', ['all', 'usuario.free'])
                                                    ->get();

        //Obtener clientes con planes activos

        $clients = AddedService::leftJoin('services', 'services.id', '=', 'added_services.service_id')
                                    ->leftJoin('clients', 'clients.id', '=', 'added_services.client_id')
                                    ->leftJoin('users', 'users.id', '=', 'clients.user_id')
                                    ->where('services.type', 'Plan')
                                    ->where('added_services.flag_active_plan', true)
                                    ->select('clients.id as id_client', 'users.id as id_user' , 'users.email as email_user')
                                    ->get();


        foreach ($businessMultimedia as $bm) {
            $business = Business::find($bm->business_id);
            foreach ($clients as $client) {
                try {
                    Notification::route('mail', $client->email_user)
                                ->notify(new NewBusinessVideoNotification($business, Client::find($client->id_client)));
                }
                catch (Exception $e) {
                    Log::error("Error - COYAG -> Excepción capturada en SendBusinessVideoEmailCommand: $e");
                }
            }
        }

    }
}
