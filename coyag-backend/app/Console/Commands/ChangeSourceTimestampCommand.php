<?php

namespace App\Console\Commands;

use App\Models\Business;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ChangeSourceTimestampCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'business:change-source-timestamp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Si el valor de Source Timestamp es un Serial Number lo convierte a una fecha válida';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $today = Carbon::now()->format('d-m-Y H:i:s');

        Log::info('=================================================');
        Log::info('Inicio de Cambio de Serial Number a Fecha válida');
        Log::info('Fecha: ' . $today);
        Log::info('=================================================');

        foreach (Business::all() as $business) {
            Log::info('=================================================');
            Log::info('ID: ' . $business->id . " fecha: " . $business->source_timestamp);

            $sourceTimestamp =  $business->source_timestamp;

            if(!empty($business->source_timestamp)){
                if(ctype_digit($business->source_timestamp)){
                    $sourceTimestamp = is_int((int) $business->source_timestamp) ? Carbon::createFromTimestamp(($business->source_timestamp - 25569) * 86400)->format('Y-m-d')
                                                                            : $business->source_timestamp;
                }
            }

            $business->source_timestamp = $sourceTimestamp;
            $business->save();


            Log::info("Source Timestamp: " . $sourceTimestamp);
            Log::info("Nueva fecha: " . $business->source_timestamp);
            Log::info('=================================================');
        }


        Log::info('=================================================');
        Log::info('Fin de Cambio de Serial Number a Fecha válida');
        Log::info('=================================================');
    }
}
