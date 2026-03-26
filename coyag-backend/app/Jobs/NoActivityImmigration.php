<?php
namespace App\Jobs;

use App\Models\AddedService;
use App\Models\VisaRequirement;
use App\Models\VisaStep;
use App\Traits\EmailManagementTrait;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NoActivityImmigration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, EmailManagementTrait;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Log::info('--------------------------------------------------');
        Log::info('-----------Empezando envío de emails a------------');
        Log::info('Clientes de Extranjería Sin Actividad en la Semana');
        Log::info('--------------------------------------------------');

        $activeAS = AddedService::leftJoin('services', 'services.id', '=', 'added_services.service_id')
                                        ->where('services.type', 'Inmigration')
                                        ->where('added_services.flag_active_plan', true)
                                        ->where('added_services.created_at', '>', '2023-01-01 00:00:00')
                                        ->select('added_services.*')
                                        ->get();

        foreach ($activeAS as $addServ)
        {
            $totalVS = VisaStep::where('added_service_id', $addServ->id)
                                        ->count();

            $completeVS = VisaStep::where('added_service_id', $addServ->id)
                                        ->where('status', 'Completado')
                                        ->count();

            if($totalVS == $completeVS)
                continue;


            $visaStepCount = VisaStep::where('added_service_id', $addServ->id)
                                        ->where('updated_at', '>', Carbon::now()->subDays(7))
                                        ->count();

            $visaRequirementCount = VisaRequirement::where('added_service_id', $addServ->id)
                                                    ->where('updated_at', '>', Carbon::now()->subDays(7))
                                                    ->count();


            $vRCount = VisaRequirement::where('added_service_id', $addServ->id)
                                                ->whereIn('status', ['Sin Cargar', 'Rechazado'])
                                                ->count();


            if($visaStepCount == 0 && $visaRequirementCount == 0)
                $this->noActivityImmigration($addServ->client, $addServ->visaType->name, $totalVS, $completeVS, $vRCount);

        }

        Log::info('--------------------------------------------------');
        Log::info('-----------Terminado envío de emails a------------');
        Log::info('Clientes de Extranjería Sin Actividad en la Semana');
        Log::info('--------------------------------------------------');
    }
}
