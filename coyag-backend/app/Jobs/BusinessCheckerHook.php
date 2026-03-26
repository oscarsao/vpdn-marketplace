<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\Business;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

use App\Imports\ActivateBusinessURLImport;

use App\Models\Collector;

class BusinessCheckerHook implements ShouldQueue { 
    use Dispatchable, Queueable;

    private $apiUrl;
    private $apiKey;
    private $file;
    private $property_type;

    /**
     * Create a new job instance.
     *
     * @param string $apiUrl BrightData API endpoint URL
     * @param string $apiKey Your BrightData API token
     * @param array $urls List of URLs to request data for
     * @return void
     */
    public function __construct($file, $property_type) {
        $this->apiUrl = "https://api.brightdata.com/dca/trigger?collector=c_ljnn8hh414jav283q6&queue_next=1";
        $this->apiKey = env('BRIGHT_DATA_KEY');
        $this->file = $file;
        $this->property_type = $property_type;
    }        

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $startTime = microtime(true);
        $today = Carbon::now()->format('d-m-Y H:i:s');
        $arrReturn = ['loaded_rows' => '', 'unloaded_rows' => '', 'update_business_id' => '' , 'update_business_bdid' => '', 'highest_row' => 0];
        
        Log::info('=================================');
        Log::info('- Check Webhook START - ' . $today);

        $import = (new ActivateBusinessURLImport($arrReturn))->import($this->file);

        $query = Business::select('source_url')->whereNotNull('source_url')->whereNotNull('source_platform')->where('source_platform', '!=', "Manual")->where('source_platform', '!=', "mundoFranquicia")
        ->where('business_type_id', $this->property_type);

        foreach ($query->get()->pluck('source_url')->toArray() as $url) {
            if(strpos($url, "idealista") !== false) {
                $idcode = strpos($url, "obra-nueva") !== false ? '#io#' : '#ia#';
                if(substr($url, -1) == '/') $url =  substr($url, 0, strlen($url) -1);
                $arrwebsite = explode('/', $url);
                $idIdealista = end($arrwebsite);
                if(is_numeric($idIdealista)) {
                    $database_ids[] = $idIdealista;
                    $database_bdids[] = $idcode . $idIdealista;
                }
            } else if(strpos($url, "milanuncios") !== false) {
                $auxArr = explode('-', $url);
                $idMilanuncios = end($auxArr);
                $idMilanuncios = str_replace('.htm', '', $idMilanuncios);
                if(is_numeric($idMilanuncios)) {
                    $database_ids[] = $idMilanuncios;
                    $database_bdids[] = '#ma#' . $idMilanuncios;
                }
            } else if(strpos($url, "belbex") !== false) {
                if(substr($url, -1) == '/') $url =  substr($url, 0, strlen($url) -1);
                $auxArr = explode('/', $url);
                $idBelbex = $auxArr[count($auxArr) - 2];
                if(strpos($idBelbex, "-") === false) {
                    $database_ids[] = $idBelbex;
                    $database_bdids[] = '#bx#' . $idBelbex;
                }
            } else if(strpos($url, "fotocasa") !== false) {
                $idcode = strpos($url, "comprar/viviendas") !== false ? '#fa#' : '#fc#';
                $auxArr = explode('/', $url);
                $idFotocasa = $auxArr[count($auxArr) - 2];
                if(is_numeric($idFotocasa)) {
                    $database_ids[] = $idFotocasa;
                    $database_bdids[] = $idcode . $idFotocasa;
                }
            }
        }
        $xlxsfile_ids   = explode(',', $arrReturn['update_business_id']);
        $xlxsfile_bdids = explode(',', $arrReturn['update_business_bdid']);

        // Set Deactivate flag_active to false
        foreach (array_chunk(array_diff($database_ids, $xlxsfile_ids), 10000) as $chunk) {
            Business::whereIn('id_business_platform', $chunk)->where('business_type_id', $this->property_type)->update(['flag_active' => false]);
        }
        Log::info("- Updating DB - Deactivate flag_active - " . (microtime(true)- $startTime) . 's');
        
        // Set Remain flag_active to true
        foreach (array_chunk(array_intersect($database_ids, $xlxsfile_ids), 10000) as $chunk) {
            Business::whereIn('id_business_platform', $chunk)->where('business_type_id', $this->property_type)->update(['flag_active' => true]);
        }
        Log::info("- Updating DB - Remain flag_active - " . (microtime(true)- $startTime) . 's');

        // Set Import as true and run ImporterCaller
        $ToImport = array_diff($xlxsfile_bdids, $database_bdids);
        $ToImportChunks = array_chunk($ToImport, 750);
        
        Log::info("- Calling Brightdata");
        foreach ($ToImportChunks as $key => $chunk) {
            $response = Http::withToken($this->apiKey, 'Bearer')->post(
                $this->apiUrl . "?queue=CYA-Import-" . $key . '-' . date("Ymd") . '&override_incompatible_schema=1',
                [ 'urls' => [], 'properties' => $chunk ],
                [ 'headers' => [ 'Content-Type' => 'application/json' ] ]
            );
            if ($response->ok()) {
                $complete_response = $response->json();
                $collector = Collector::updateOrCreate(
                    ['collector_id' => $complete_response['collection_id']],
                    [
                        'owner' => null,
                        'inputs' => json_encode(['urls' => [], 'properties' => $chunk])
                    ]
                );
                $collector->save();

                Log::info("Brightdata Checker - $key EXITO");
                Log::info($response);
            } else {
                Log::info("Brightdata Checker - $key FALLIDO");
                Log::info($response);
            }
        }

        Log::info('- Total in DB:    '   . count($database_ids) );
        Log::info('- Total in File:  '   . count($xlxsfile_ids) );
        
        Log::info('- Currently Active:   ' . count(array_intersect($database_ids, $xlxsfile_ids)) );
        Log::info('- Currently Inactive: ' . count(array_diff($database_ids, $xlxsfile_ids)) );

        Log::info('- To be Imported: ' . count(array_diff($xlxsfile_ids, $database_ids)) );
        Log::info('- Will update to: ' . ( count(array_diff($xlxsfile_ids, $database_ids)) + count(array_intersect($database_ids, $xlxsfile_ids)) ) );

        Log::info('- Check Webhook END - ' . (microtime(true)- $startTime) . 's');
        Log::info('=================================');
    }
}