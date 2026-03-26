<?php

namespace App\Services;

use \stdClass;
use App\Models\Client;
use App\Models\ClientTimeline;
use App\Models\Province;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClientTimelineService
{

    public function getBrowsingHistory(Request $request, $idClient)
    {
        $client = Client::find($idClient);

        $data = new stdClass();

        if(!$client)
        {
            $data->code     = 422;
            $data->status   = 'errors';
            $data->errors   = ['client_id' => ['El Cliente no existe']];
            return $data;
        }

        $browsingHistory = ClientTimeline::leftJoin('businesses', 'businesses.id', '=', 'properties->id')
                                    ->leftJoin('business_types', 'business_types.id', '=', 'businesses.business_type_id')
                                    ->where('client_timelines.client_id', $client->id)
                                    ->where('client_timelines.module_eng', 'Business')
                                    ->where('client_timelines.type_crud_eng', 'show')
                                    ->select('businesses.id as id_business', 'businesses.id_code as id_code_business', 'businesses.name as name_business', 'businesses.flag_active as flag_active_business', 'businesses.business_type_id as id_business_type', 'business_types.name as name_business_type', 'client_timelines.id as id_client_timeline', 'client_timelines.created_at as date_of_visit')
                                    ->orderBy('date_of_visit', 'desc')
                                    ->get();

        $data->code     = 200;
        $data->status   = 'success';
        $data->browsingHistory = $browsingHistory;

        return $data;
    }

    public function getSearchHistory(Request $request, $idClient)
    {
        $client = Client::find($idClient);

        $data = new stdClass();

        if(!$client)
        {
            $data->code     = 422;
            $data->status   = 'errors';
            $data->errors   = ['client_id' => ['El Cliente no existe']];
            return $data;
        }

        $searchHistory = ClientTimeline::where('client_timelines.client_id', $client->id)
                                        ->where('client_timelines.module_eng', 'Business')
                                        ->where('client_timelines.type_crud_eng', 'list')
                                        ->select('client_timelines.id as id_client_timeline', 'client_timelines.properties as properties', 'client_timelines.created_at as consultation_date')
                                        ->orderBy('consultation_date', 'desc')
                                        ->get();


        $sectors = Sector::pluck('name', 'id')->toArray();

        foreach ($searchHistory as $key => $searchH)
        {
            $properties = $searchH->properties;

            if(empty($properties['sectors']) && empty($properties['province_id']))
                continue;

            if(!empty($properties['sectors']))
            {
                $sectorName = '';

                foreach (explode(',', $properties['sectors']) as $id) {
                    $sectorName = $sectorName == '' ? "{$id}-{$sectors[$id]}"
                                                    : "{$sectorName},{$id}-{$sectors[$id]}";
                }

                $properties['sector_name'] = $sectorName;
            }

            if(!empty($properties['province_id']))
            {
                $province = Province::find($properties['province_id']);
                if($province)
                    $properties['province_name'] = $province->name;
            }


            $searchH->properties = $properties;
        }


        $data->code     = 200;
        $data->status   = 'success';
        $data->searchHistory = $searchHistory;

        return $data;
    }

    public function getAuthHistory(Request $request, $idClient)
    {
        $client = Client::find($idClient);

        $data = new stdClass();

        if(!$client)
        {
            $data->code     = 422;
            $data->status   = 'errors';
            $data->errors   = ['client_id' => ['El Cliente no existe']];
            return $data;
        }

        $authHistory = ClientTimeline::where('client_timelines.client_id', $client->id)
                                    ->where('client_timelines.module_eng', 'Auth')
                                    ->where('client_timelines.type_crud_eng', 'create')
                                    ->select('client_timelines.id as id_client_timeline', 'client_timelines.created_at as auth_date')
                                    ->orderBy('auth_date', 'desc')
                                    ->get();

        $data->code     = 200;
        $data->status   = 'success';
        $data->authHistory = $authHistory;

        return $data;
    }

}
