<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\BusinessMultimedia;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteOldBusinessImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:delete-old-business-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina todas las imágenes, a excepción de las dos primeras, de los negocios cuyo flag_active sea false y tengan dos meses que no se hayan actualizado';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $today = Carbon::now()->format('d-m-Y H:i:s');

        Log::info('=================================================');
        Log::info('Inicio de maintenance:delete-old-business-images');
        Log::info('Fecha: ' . $today);
        Log::info('=================================================');

        $numImgToSave = 2;

        $deadline = Carbon::now()->subMonths(2); // Lo ideal serían dos meses atrás

        $businessesId = Business::where('flag_active', false)
                                ->where('created_at', '<=', $deadline)
                                ->where('source_platform', '<>', 'Manual')
                                ->pluck('id');

        $countBM = BusinessMultimedia::where('type', 'image')
                                ->select('business_id', DB::raw('count(*) as total_bm'))
                                ->whereIn('business_id', $businessesId)
                                ->groupBy('business_id')
                                ->get();



        foreach ($countBM as $key => $value) {
            if($value->total_bm > $numImgToSave) {
                $arrBusinessMultimedia = BusinessMultimedia::where('business_id', $value->business_id)
                                        ->where('type', 'image')
                                        ->orderBy('business_multimedia.id', 'DESC')
                                        ->take($value->total_bm - $numImgToSave)
                                        ->get();

                foreach($arrBusinessMultimedia as $bM)
                {
                    deleteResourceBusinessMultimedia($bM);
                    $bM->delete();
                }
            }

        }

        Log::info('=================================================');
        Log::info('Fin de maintenance:delete-old-business-images');
        Log::info('=================================================');

    }
}
