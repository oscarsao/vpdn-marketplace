<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;
use App\Models\Business;
use App\Models\Collector;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BusinessToolsController extends Controller {
    private $apiUrl;
    private $apiKey;
    public function __construct() {
        $this->apiUrl = "https://api.brightdata.com/dca/trigger?collector=c_ljnn8hh414jav283q6&queue_next=1";
        $this->apiKey = env('BRIGHT_DATA_KEY');
    }

    

    public function linker(Request $request) {
        // get array of urls 
        $urls = $request->urls;
        $urls_code = [];
           
        foreach ($urls as $website) {
            $idBusinessPlatform = getCodeFromUrl($website);
            if (isset($idBusinessPlatform)) {
                $urls_code[] = $idBusinessPlatform;
            }
        }

        $ids = [];
        $businesses = Business::whereIn('id_business_platform', $urls_code)->get();
        foreach ($businesses as $business) {
            $ids[] = $business->id;
        }

        $notfound = array_diff($urls_code, $businesses->pluck('id_business_platform')->toArray());

        $notfoundUrls = [];
        foreach ($urls as $url) {
            $code = getCodeFromUrl($url);
            if (in_array($code, $notfound)) {
                $notfoundUrls[] = $url;
            }
        }
        return response()->json(['ids' => $ids, 'notfound' => $notfoundUrls], 200);
    }

    
    public function onDemand (Request $request) {
        Log::info('=================================');
        Log::info('- SCRAPPING ON DEMAND ' . Carbon::now()->format('d-m-Y H:i:s'));

        $urls = [];
        if ($request->exists('urls')) {
            $urls = $request->urls;
        }
        $properties_send = [];
        if ($request->exists('properties')) {
            $properties = $request->properties;
            foreach ($properties as $website) {
                $idBusinessPlatform = makeCodeFromUrl($website);
                if (isset($idBusinessPlatform) || $idBusinessPlatform) {
                    $properties_send[] = $idBusinessPlatform;
                }
            }
        }
        $arguments = [ 'urls' => $urls, 'properties' => $properties_send];
        if( $request->exists('max_items') ) {
            $arguments['max_items'] = $request->max_items;
        } else {
            $arguments['max_items'] = 5000;
        }

        $response = Http::withToken($this->apiKey, 'Bearer')->post(
            $this->apiUrl . "?queue=CYA-Import-" . date("Ymd") . '&override_incompatible_schema=1',
            $arguments,
            [ 'headers' => [ 'Content-Type' => 'application/json' ] ]
        );
        $complete_response = $response->json();

        if ($response->ok()) {

            Log::info($response);
            Log::info($complete_response);
            

            $collector = Collector::updateOrCreate(
                ['collector_id' => $complete_response['collection_id']], [
                'owner' => $request->user()->id,
                'inputs' => json_encode($arguments)
            ]);
            $collector->save();

            Log::info('Brightdata Caller - EXITO');
            Log::info($response);
            Log::info($complete_response);
            return response()->json(['message' => 'Success', ...$response->json()], 200);
        } else {
            Log::info('Brightdata Caller - FALLIDO');
            Log::info($response);
            if (is_null($this->apiKey)) Log::info('No API KEY');
            return response()->json(['message' => 'Failure', ...$response->json()], 500);
        }
        Log::info('=================================');
    }
}

function getCodeFromUrl ($website) {
    if (strpos($website, "idealista") !== false) {
        if (substr($website, -1) == '/') $website =  substr($website, 0, strlen($website) - 1);
        $arrwebsite = explode('/', $website);
        $idIdealista = end($arrwebsite);
        if (is_numeric($idIdealista)) $idBusinessPlatform = $idIdealista;
    } else if (strpos($website, "milanuncios") !== false) {
        $auxArr = explode('-', $website);
        $idMilanuncios = end($auxArr);
        $idMilanuncios = str_replace('.htm', '', $idMilanuncios);
        if (is_numeric($idMilanuncios)) $idBusinessPlatform = $idMilanuncios;
    } else if (strpos($website, "belbex") !== false) {
        if (substr($website, -1) == '/') $website =  substr($website, 0, strlen($website) - 1);
        $auxArr = explode('/', $website);
        $idBelbex = $auxArr[count($auxArr) - 2];
        if (strpos($idBelbex, "-") === false) $idBusinessPlatform = $idBelbex;
    } else if (strpos($website, "fotocasa") !== false) {
        $auxArr = explode('/', $website);
        $idFotocasa = $auxArr[count($auxArr) - 2];
        if (is_numeric($idFotocasa)) $idBusinessPlatform = $idFotocasa;
    }
    return $idBusinessPlatform;
}
function makeCodeFromUrl ($url) {
    if(strpos($url, "idealista") !== false) {
        $idcode = strpos($url, "obra-nueva") !== false ? '#io#' : '#ia#';
        if(substr($url, -1) == '/') $url =  substr($url, 0, strlen($url) -1);
        $arrwebsite = explode('/', $url);
        $idIdealista = end($arrwebsite);
        if(is_numeric($idIdealista)) {
            $idBusinessPlatform = $idcode . $idIdealista;
        }
    } else if(strpos($url, "milanuncios") !== false) {
        $auxArr = explode('-', $url);
        $idMilanuncios = end($auxArr);
        $idMilanuncios = str_replace('.htm', '', $idMilanuncios);
        if(is_numeric($idMilanuncios)) {
            $idBusinessPlatform = '#ma#' . $idMilanuncios;
        }
    } else if(strpos($url, "belbex") !== false) {
        if(substr($url, -1) == '/') $url =  substr($url, 0, strlen($url) -1);
        $auxArr = explode('/', $url);
        $idBelbex = $auxArr[count($auxArr) - 2];
        if(strpos($idBelbex, "-") === false) {
            $idBusinessPlatform = '#bx#' . $idBelbex;
        }
    } else if(strpos($url, "fotocasa") !== false) {
        $idcode = strpos($url, "comprar/viviendas") !== false ? '#fa#' : '#fc#';
        $auxArr = explode('/', $url);
        $idFotocasa = $auxArr[count($auxArr) - 2];
        if(is_numeric($idFotocasa)) {
            $idBusinessPlatform = $idcode . $idFotocasa;
        }
    }
    if (isset($idBusinessPlatform)) {
        return $idBusinessPlatform;
    }
    return false;
}