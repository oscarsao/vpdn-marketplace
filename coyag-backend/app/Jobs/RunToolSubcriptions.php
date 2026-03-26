<?php 
namespace App\Jobs;

use App\Traits\EmailManagementTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\ToolSubscription;
use App\Services\BrightDataScrapper;
use App\Mail\ToolNacionalidadEspanola;
use App\Mail\ToolProteccionInternacional;
class RunToolSubcriptions implements ShouldQueue
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
    public function handle()
    {
        Log::info('--------------------------------------------------');
        Log::info('----------Empezando scrapping y Emails------------');
        Log::info('Clientes de Extranjería Subscritos a 2Herramientas');
        Log::info('--------------------------------------------------');
        // LOOP 
        foreach (ToolSubscription::all() as $sub) {
            $sub->data = json_decode($sub->data, true);

            if ($sub->tool === 'ExpedienteNacionalidad') {
                /* ON HOLD */
                /*
                $response = BrightDataScrapper::ExpedienteNacionalidad($sub->data);
                if ($response->resultType === 'success') $response->resultType = '#28C76F';
                if ($response->resultType === 'warning') $response->resultType = '#FF9F43';
                if ($response->resultType === 'danger' ) $response->resultType = '#EA5455';
                else $response->resultType = "#666666";
                $data = array(
                    'email' => $sub->email,
                    'nie' => $sub->data['nie'],
                    'number' => $sub->data['number'],
                    'resultType' => $response->resultType,
                    'result' => $response->result,
                );
               Mail::to($sub->email)->send(new ToolNacionalidadEspanola($data));
               */
               /* ON HOLD */
            } else if ($sub->tool === 'ProteccionInternacional') {
                $response = BrightDataScrapper::ProteccionInternacional($sub->data);
                if ($response->resultType === 'success') $response->resultType = '#28C76F';
                if ($response->resultType === 'warning') $response->resultType = '#FF9F43';
                if ($response->resultType === 'danger' ) $response->resultType = '#EA5455';
                else $response->resultType = "#666666";
                
                $data = array(
                    'email' => $sub->email,
                    'nie' => $sub->data['nie'],
                    'resultType' => $response->resultType,
                    'document' => $response->document,
                    'result' => $response->result,
                );
                Mail::to($sub->email)->send(new ToolProteccionInternacional($data));
            } 
        }
        Log::info('--------------------------------------------------');
        Log::info('----------Terminando scrapping y Emails-----------');
        Log::info('Clientes de Extranjería Subscritos a 2Herramientas');
        Log::info('--------------------------------------------------');
    }
}