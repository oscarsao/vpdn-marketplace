<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\Business;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Traits\PreferenceTrait;

use App\Imports\BusinessImport;
use App\Models\BusinessMultimedia;

class BusinessImporterHook implements ShouldQueue { 
    use PreferenceTrait;
    use Dispatchable, Queueable;

    private $apiUrl;
    private $apiKey;
    private $file;

    private $update_image;
    private $update_field;


    /**
     * Create a new job instance.
     *
     * @param string $apiUrl BrightData API endpoint URL
     * @param string $apiKey Your BrightData API token
     * @param array $urls List of URLs to request data for
     * @return void
     */
    public function __construct($file, $update_image, $update_field) {
        $this->apiUrl = "https://api.brightdata.com/dca/trigger?collector=c_li38zq1j27wxnshbb1&queue_next=1";
        $this->apiKey = env('BRIGHT_DATA_KEY');
        $this->file = $file;
        $this->update_image  = $update_image;
        $this->update_field  = $update_field;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $now = Carbon::now();
        $startTime = microtime(true);

        $run_name = date("d-m-Y") . "-" . substr(md5(microtime()), rand(0,26), 5);

        Log::info('=================================');
        Log::info("- Import Webhook $run_name START - " . Carbon::now()->format('H:i:s'));

        $arrReturn = ['loaded_rows' => '', 'unloaded_rows' => '', 'delete_news_images_file'  => '', 'delete_previous_images' => '', 'charged_business' => '', 'total_rows' => 0, 'highest_row' => 0];

        $provinces      = DB::table('provinces'     )->select('id','name')->get();
        $neighborhoods  = DB::table('neighborhoods' )->select('id','name')->get();
        $districts      = DB::table('districts'     )->select('id','name')->get();
        $municipalities = DB::table('municipalities')->select('id','name')->get();

        try {
            $import = (new BusinessImport($arrReturn, [
                "update_image"  => $this->update_image,
                "update_field"  => $this->update_field 
            ], 
            [
                "provinces"      => $provinces     ,
                "municipalities" => $municipalities,
                "districts"      => $districts     ,
                "neighborhoods"  => $neighborhoods 
            ],
            $run_name
            ))->import($this->file); 
        } catch (\Exception $e) {
            Log::info("Exception in WebhookController@ImporterHook - " . (microtime(true)- $startTime) . 's' );
            Log::info($e);
            $this->deleteImagesAfterError($now, $arrReturn['delete_news_images_file']);
            return response()->json(['error' => 'Error no determinado. Revise el archivo', 'rows' => $arrReturn], 400);
        } catch (\Throwable $e) {
            Log::info("Throwable in WebhookController@ImporterHook - " . (microtime(true)- $startTime) . 's');
            Log::info($e);
            $this->deleteImagesAfterError($now, $arrReturn['delete_news_images_file']);
            return response()->json(['error' => 'Error no determinado. Revise el archivo', 'rows' => $arrReturn], 400);
        }
        if($arrReturn['delete_previous_images'] != '') {
            $this->deletePreviousImages($now, $arrReturn['delete_previous_images']);
        }
        $this->updateBusinessListForAllClients();

        Log::info("- Import Webhook $run_name END - " . (microtime(true)- $startTime) . 's');
        Log::info('=================================');
    }

    /**
     * Is this like this?
     */
    private function deletePreviousImages($now, $business) {
        $arr = explode(',', $business);
        foreach ($arr as $idBusiness) {
            $arrBusinessMultimedia = BusinessMultimedia::where('business_id', $idBusiness)->where('created_at', '<', $now)->where('type', 'image')->get();
            foreach($arrBusinessMultimedia as $bM) {
                deleteResourceBusinessMultimedia($bM);
                $bM->delete();
            }
        }
    }
    private function deleteImagesAfterError($now, $files) {
        $arrFiles = explode(',', $files);
        foreach ($arrFiles as $file) {
            if (File::exists(public_path($file))) {
                File::delete(public_path($file));
            } else {
                Log::error("Error - COYAG ->  Error deleteResourceBusinessMultimedia: No borró el archivo $file");
            }
        }
    }
}