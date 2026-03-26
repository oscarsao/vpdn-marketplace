<?php

namespace App\Services;

use \stdClass;
use App\Models\AddedService;
use App\Models\Business;
use App\Models\BusinessClient;
use App\Models\Client;
use App\Models\ClientTimeline;
use App\Models\Province;
use App\Models\Sector;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StatisticService
{
    public function totalClientsByPlan()
    {
        return Service::leftJoin('added_services', 'added_services.service_id', '=', 'services.id')
                                ->where('services.type', 'Plan')
                                ->groupBy('name')
                                ->select('services.id as id_service', 'services.name as name_service', 'services.flag_active as flag_active_service', 'services.type as type_service', DB::raw('COUNT(added_services.client_id) as total_clients'), DB::raw('(SELECT COUNT(*) from services LEFT JOIN added_services ON added_services.service_id = services.id WHERE added_services.flag_active_plan = 1 and services.id = id_service) as total_active_clients'), DB::raw('(SELECT COUNT(*) from services LEFT JOIN added_services ON added_services.service_id = services.id WHERE added_services.flag_active_plan = 0 and services.id = id_service) as total_inactive_clients'))
                                ->get();

    }

    public function getListClientsByPlan(Request $request)
    {
        $validator = $this->validatorListClientsByPlan($request);

        $data = new stdClass();

        if(!empty($validator))
        {
            $data->code     = 422;
            $data->status   = 'errors';
            $data->errors   = $validator;
            return $data;
        }

        $data->code     = 200;
        $data->status   = 'success';

        $data->clients  = $this->listClientsByPlan($request);

        return $data;
    }

    private function validatorListClientsByPlan(Request $request) : array
    {
        $validator = Validator::make($request->all(),
        [
            'service_id'            =>  'exists:services,id'
        ],
        [
            'service_id.exists'     =>  'El ID del Servicio no existe'
        ]);

        if($validator->fails())
            return $validator->errors()->toArray();

        if($request->exists('service_id'))
        {
            $service = Service::find($request->service_id);

            if($service->type != 'Plan')
                return ['service_id' => ['El ID de Servicio no es del tipo Plan']];
        }

        return [];
    }

    private function listClientsByPlan(Request $request)
    {
        $clients = AddedService::leftJoin('services', 'services.id', '=', 'added_services.service_id')
                                ->leftJoin('clients', 'clients.id', '=', 'added_services.client_id')
                                ->leftJoin('countries', 'countries.id', '=', 'clients.first_nationality_id')
                                ->leftJoin('users', 'users.id', '=', 'clients.user_id')
                                ->where('services.type', 'Plan');

        if($request->exists('service_id'))
            $clients = $clients->where('added_services.service_id', $request->service_id);

        $clients = $clients->orderBy('clients.id', 'asc')
                            ->select("clients.id as id_client", "users.email as email", "clients.names as names", "clients.surnames as surnames",
                                        "clients.phone_mobile as phone_mobile","services.name as name_service",
                                        "countries.name as country",
                                        DB::raw("CASE WHEN added_services.flag_active_plan = 1 THEN 'Si' ELSE 'No' END as flag_active_plan"),
                                        "clients.registration_method as registration_method", "added_services.created_at as created_at",
                                        "added_services.plan_deactivated_at as plan_deactivated_at");

        $provincesList = Province::get()
                                ->map->only(['name'])
                                ->values();

        $sectorsList = Sector::get()
                            ->map->only(['name'])
                            ->values();

        $clients = $clients->get();


        foreach ($clients as $key => $client) {

            $numAuth = ClientTimeline::where('client_id', $client['id_client'])
                            ->where('module_eng', 'Auth')
                            ->where('type_crud_eng', 'create')
                            ->count();

            $client['num_auth'] = empty($numAuth)   ? '0'
                                                    : $numAuth;

            $businessList = ClientTimeline::where('client_id', $client['id_client'])
                            ->where('module_eng', 'Business')
                            ->where('type_crud_eng', 'show')
                            ->pluck('properties->id_code as total')
                            ->toArray();

            $client['id_code_businesses_visited'] = implode(',', $businessList);

            // Preferencia de Negocios
            $businessClient = BusinessClient::where('client_id', $client['id_client'])->first();

            if($businessClient)
            {
                // Pregunta 1 -  Provinces
                $client['question_1'] = '';

                if($businessClient->provinces_list)
                {
                    foreach (explode(',', $businessClient->provinces_list) as $key => $value) {
                        $client['question_1'] .= empty($client['question_1'])
                                                    ? "{$value}-{$provincesList[$value - 1]['name']}"
                                                    : ",{$value}-{$provincesList[$value - 1]['name']}";
                    }
                }

                // Pregunta 2 - Sectors
                $client['question_2'] = '';

                if($businessClient->sectors_list)
                {
                    foreach (explode(',', $businessClient->sectors_list) as $key => $value) {
                        $client['question_2'] .= empty($client['question_2'])
                                                    ? "{$value}-{$sectorsList[$value - 1]['name']}"
                                                    : ",{$value}-{$sectorsList[$value - 1]['name']}";
                    }
                }

                // Pregunta 3 - Operar negocio
                $client['question_3'] = $businessClient->prefered_operation;

                // Pregunta 4 - Fecha inicio de negocio
                $client['question_4'] =  $businessClient->estimated_date;
                if(!empty($businessClient->estimated_date))
                    $client['question_4'] =  Carbon::parse($businessClient->estimated_date)->format('d/m/Y');

                // Pregunta 5 - Inversión
                $client['question_5_min_investment'] = $businessClient->min_investment;
                $client['question_5_max_investment'] = $businessClient->max_investment;
            }

        }

        return $clients;

    }

    public function getMostVisitedBusinesses(Request $request)
    {
        $validator = $this->validatorMostVisitedBusinesses($request);

        $data = new stdClass();

        if(!empty($validator))
        {
            $data->code     = 422;
            $data->status   = 'errors';
            $data->errors   = $validator;
            return $data;
        }

        $data->code     = 200;
        $data->status   = 'success';

        $data->businesses  = $this->listMostVisitedBusinesses($request);

        return $data;
    }

    private function validatorMostVisitedBusinesses(Request $request) : array
    {
        $validator = Validator::make($request->all(),
        [
            'flag_active'           =>  'boolean'
        ],
        [
            'flag_active.boolean'   =>  'Debe enviar 1 para true y 0 para false'
        ]);

        if($validator->fails())
            return $validator->errors()->toArray();

        return [];
    }

    private function listMostVisitedBusinesses(Request $request)
    {

        $businesses= Business::leftJoin('municipalities', 'municipalities.id', '=', 'businesses.municipality_id')
                                ->leftJoin('provinces', 'provinces.id', '=', 'municipalities.province_id')
                                ->leftJoin('districts', 'districts.id', '=', 'businesses.district_id')
                                ->leftJoin('neighborhoods', 'neighborhoods.id', '=', 'businesses.neighborhood_id')
                                ->select('businesses.id as id_business', 'businesses.id_code as id_code', 'businesses.name as name', 'businesses.investment as investment', 'businesses.rental as rental', 'businesses.size as size', DB::raw("CASE WHEN businesses.flag_smoke_outlet = 1 THEN 'Si' ELSE 'No' END as flag_smoke_outlet"), DB::raw("CASE WHEN businesses.flag_terrace = 1 THEN 'Si' ELSE 'No' END as flag_terrace"), 'businesses.contact_name AS contact_name', 'businesses.contact_landline AS contact_landline', 'provinces.name as name_province', 'municipalities.name as name_municipality', 'districts.name as name_district', 'neighborhoods.name as name_neighborhood', 'businesses.times_viewed as times_viewed', DB::raw("CASE WHEN businesses.flag_active = 1 THEN 'Si' ELSE 'No' END as flag_active"), 'businesses.source_platform as source_platform', 'businesses.source_url as source_url', DB::raw('DATE_FORMAT(businesses.created_at, "%d/%m/%Y") as created_at_format'));


        if($request->exists('flag_active') && $request->flag_active)
            $businesses = $businesses->where('businesses.flag_active', true);

        $businesses = $businesses->orderBy('businesses.times_viewed', 'desc')
                                    ->limit(100)
                                    ->get();


        foreach ($businesses as $key => $business)
        {
            $sectors = Sector::leftJoin('business_sector', 'business_sector.sector_id', '=', 'sectors.id')
                                ->where('business_sector.business_id', $business->id_business)
                                ->groupBy('business_sector.business_id')
                                ->select(DB::raw("GROUP_CONCAT(sectors.id, '-', sectors.name) as sector"))
                                ->first();

            $business->sectors = empty($sectors)    ?   null
                                                    :   $sectors->sector;
        }

        return $businesses;
    }

    public function getTotalAuthenticatedClientsByPlan(Request $request)
    {
        $validator = $this->validatorTotalAuthenticatedClientsByPlan($request);

        $data = new stdClass();

        if(!empty($validator))
        {
            $data->code     = 422;
            $data->status   = 'errors';
            $data->errors   = $validator;
            return $data;
        }

        $data->code     = 200;
        $data->status   = 'success';

        $data->clients  = $this->totalAuthenticatedClientsByPlan($request);

        return $data;

    }

    private function validatorTotalAuthenticatedClientsByPlan(Request $request) : array
    {
        $validator = Validator::make($request->all(),
        [
            'service_id'            =>  'exists:services,id'
        ],
        [
            'service_id.exists'     =>  'El ID del Servicio no existe'
        ]);

        if($validator->fails())
            return $validator->errors()->toArray();

        if($request->exists('service_id'))
        {
            $service = Service::find($request->service_id);

            if($service->type != 'Plan')
                return ['service_id' => ['El ID de Servicio no es del tipo Plan']];
        }

        return [];
    }

    private function totalAuthenticatedClientsByPlan(Request $request)
    {
        $clients = Client::leftJoin('users', 'users.id', '=', 'clients.user_id')
                        ->leftJoin('added_services', 'added_services.client_id', '=', 'clients.id')
                        ->leftJoin('client_timelines', 'client_timelines.client_id', '=', 'clients.id')
                        ->leftJoin('services', 'services.id', '=', 'added_services.service_id')
                        ->where('client_timelines.module_eng', 'Auth')
                        ->where('client_timelines.type_crud_eng', 'create')
                        ->where('services.type', 'Plan')
                        ->select('clients.id', 'users.email', 'clients.names', 'clients.surnames', 'clients.phone_mobile', 'services.name as name_service', DB::raw("COUNT(*) as num_auth"));

        if($request->exists('service_id'))
            $clients = $clients->where('added_services.service_id', $request->service_id);

        $clients = $clients->groupBy('clients.id', 'services.name')
                    ->get();

        return $clients;

    }

}
