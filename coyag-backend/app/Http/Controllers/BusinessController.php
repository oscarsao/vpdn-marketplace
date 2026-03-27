<?php
namespace App\Http\Controllers;

use App\Models\Busiest;
use App\Models\Business;
use App\Models\BusinessMultimedia;
use App\Models\BusinessTimeline;
use App\Models\Client;
use App\Models\ClientTimeline;
use App\Models\Dossier;
use App\Models\Recommendation;
use App\Models\Sector;
use App\Imports\ActivateBusinessImport;
use App\Imports\ActivateBusinessURLImport;
use App\Traits\PreferenceTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use Exception;

use App\Models\Smartlink;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BusinessExport;

class BusinessController extends Controller
{
    use PreferenceTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        // Creating Sector Map to prevent calling the DB Infinite times
        $sector_map = array();
        $sectors = DB::table("sectors")->get();
        foreach ($sectors as $sector) {
            $sector_map[$sector->id] = $sector->name;
        }

        $businesses = Business::leftJoin('business_types', 'business_types.id', '=', 'businesses.business_type_id')
            ->leftJoin('neighborhoods', 'neighborhoods.id', '=', 'businesses.neighborhood_id')
            ->leftJoin('districts', 'districts.id', '=', 'businesses.district_id')
            ->leftJoin('municipalities', 'municipalities.id', '=', 'businesses.municipality_id')
            ->leftJoin('provinces', 'provinces.id', '=', 'municipalities.province_id')
            ->leftJoin('autonomous_communities', 'autonomous_communities.id', '=', 'provinces.autonomous_community_id');

        /**
         * CONDITIONS
         */
            if ($request->exists('condition')) {
                switch ($request->condition) {
                    case 'mistery_option':
                        $businesses = $businesses->whereNotIn('businesses.id', BusinessMultimedia::where('type', 'image')->distinct()->pluck('business_id'));
                        break;
                    case 'with_analysis':
                        $businesses = $businesses->whereIn('businesses.id', BusinessMultimedia::where('type', 'video')->distinct()->whereIn('type_client', ['usuario.free', 'all'])->pluck('business_id'));
                        break;
                    case 'franchise':
                        $businesses = $businesses->where('businesses.flag_active', true)->whereIn('businesses.business_type_id', [2, 13]);
                        break;
                    case 'inmuebles':
                        $businesses = $businesses->where('businesses.flag_active', true)->whereIn('businesses.business_type_id', [3]);
                        break;
                    default: 
                        $businesses = $businesses->where('businesses.flag_active', true)->whereIn('businesses.business_type_id', [1]);
                        // Default: Negocios
                }
            } else {
                $businesses = $businesses->where('businesses.flag_active', true)->whereIn('businesses.business_type_id', [1]);
            }

            if ($request->exists('id_code'))
                $businesses = $businesses->where('businesses.id_code', $request->id_code);

            if ($request->exists('flag_sold')) {
                if (in_array(strval($request->flag_sold), ['0', '1']))
                    $businesses = $businesses->where('businesses.flag_sold', $request->flag_sold);
            }

            if ($request->exists('province_id'))
                $businesses = $businesses->where('provinces.id', $request->province_id);

            if ($request->exists('municipality_id'))
                $businesses = $businesses->where('municipalities.id', $request->municipality_id);

            if ($request->exists('district_id'))
                $businesses = $businesses->where('districts.id', $request->district_id);

            if ($request->exists('neighborhood_id'))
                $businesses = $businesses->where('neighborhoods.id', $request->neighborhood_id);

            if ($request->exists('neighborhood'))
                $businesses = $businesses->where('neighborhoods.name', 'like', '%' . $request->neighborhood . '%');

            if ($request->exists('autonomous_community_id'))
                $businesses = $businesses->where('autonomous_communities.id', $request->autonomous_community_id);

            if (!$request->exists('min_investment') && $request->exists('hide_priceless_biz') && $request->hide_priceless_biz == 1)
                $businesses = $businesses->where('businesses.investment', '>=', 100);

            if ($request->exists('min_investment'))
                $businesses = $businesses->where('businesses.investment', '>=', $request->min_investment);

            if ($request->exists('max_investment'))
                $businesses = $businesses->where('businesses.investment', '<=', $request->max_investment);

            if ($request->exists('min_rental'))
                $businesses = $businesses->where('businesses.rental', '>=', $request->min_rental);

            if ($request->exists('max_rental'))
                $businesses = $businesses->where('businesses.rental', '<=', $request->max_rental);

            if ($request->exists('only_with_gcs')) {
                if ($request->only_with_gcs == 1)
                    $businesses = $businesses->whereNotNull('businesses.lat') ->whereNotNull('businesses.lng');
            }
            if ($request->exists('flag_smoke_outlet')) {
                if (in_array(strval($request->flag_smoke_outlet), ['0', '1']))
                    $businesses = $businesses->where('businesses.flag_smoke_outlet', $request->flag_smoke_outlet);
            }
            if ($request->exists('flag_terrace')) {
                if (in_array(strval($request->flag_terrace), ['0', '1']))
                    $businesses = $businesses->where('businesses.flag_terrace', $request->flag_terrace);
            }

            if ($request->exists('flag_outstanding')) {
                if (in_array(strval($request->flag_outstanding), ['0', '1']))
                    $businesses = $businesses->where('businesses.flag_outstanding', $request->flag_outstanding);
            }

            if ($request->exists('name') && $request->name != '') {
                $search = $request->name;
                $businesses = $businesses->where(function ($query) use ($search) {
                    $query->where('businesses.name', 'like', "%$search%")->orWhere('businesses.description', 'like', "%$search%");
                });
            }

            if ($request->exists('garage'))
                $businesses = $businesses->where('businesses.garage', 'si');

            if ($request->exists('storage'))
                $businesses = $businesses->where('businesses.storage', 'si');

            if ($request->exists('pool'))
                $businesses = $businesses->where('businesses.pool', 'si');

            if ($request->exists('minrooms'))
                $businesses = $businesses->where('businesses.rooms', '>=', $request->minrooms);

            if ($request->exists('maxrooms'))
                $businesses = $businesses->where('businesses.rooms', '<=', $request->maxrooms);

            if ($request->exists('minbathrooms'))
                $businesses = $businesses->where('businesses.bathrooms', '>=', $request->minbathrooms);

            if ($request->exists('maxbathrooms'))
                $businesses = $businesses->where('businesses.bathrooms', '<=', $request->maxbathrooms);

            if ($request->exists('courtyard'))
                $businesses = $businesses->where('businesses.courtyard', $request->courtyard); //interior exterior
            if ($request->exists('elevator'))
                $businesses = $businesses->where('businesses.elevator', 'Con ascensor');

            if ($request->exists('new_or_used'))
                $businesses = $businesses->where('businesses.new_or_used', $request->new_or_used);

            if ($request->exists('order_by')) {
                if (in_array($request->order_by, ['investment_desc', 'investment_asc', 'date_desc', 'date_asc', 'most_relevant', 'investment_sqm_asc', 'investment_sqm_desc', 'size_asc', 'size_desc'])) {
                    switch ($request->order_by) {
                        case 'investment_asc':
                            $businesses = $businesses->orderBy('businesses.investment', 'asc');
                            break;
                        case 'investment_desc':
                            $businesses = $businesses->orderBy('businesses.investment', 'desc');
                            break;
                        case 'size_asc':
                            $businesses = $businesses->orderBy('businesses.size', 'asc');
                            $businesses = $businesses->where('businesses.size', '<', 10000);
                            $businesses = $businesses->where('businesses.size', '>', 10);
                            break;
                        case 'size_desc':
                            $businesses = $businesses->orderBy('businesses.size', 'desc');
                            $businesses = $businesses->where('businesses.size', '<', 10000);
                            $businesses = $businesses->where('businesses.size', '>', 10);
                            break;
                        case 'investment_sqm_asc':
                            $businesses = $businesses->orderBy(DB::raw('CEIL(investment / size)'), 'asc');
                            $businesses = $businesses->where('businesses.size', '<', 10000);
                            $businesses = $businesses->where('businesses.size', '>', 10);
                            break;
                        case 'investment_sqm_desc':
                            $businesses = $businesses->orderBy(DB::raw('CEIL(investment / size)'), 'desc');
                            $businesses = $businesses->where('businesses.size', '<', 10000);
                            $businesses = $businesses->where('businesses.size', '>', 10);
                            break;
                        case 'date_asc':
                            $businesses = $businesses->orderBy('businesses.created_at', 'asc');
                            break;
                        case 'date_desc':
                            $businesses = $businesses->orderBy('businesses.created_at', 'desc');
                            break;
                        case 'most_relevant':
                            $businesses = $businesses->orderBy('businesses.flag_outstanding', 'desc')->orderBy('businesses.flag_exclusive', 'desc');
                            break;
                    }
                }
            }

            if ($request->exists('sectors')) {
                $businesses = $businesses->whereIn('businesses.id', function ($query) use ($request) {
                    $query->select('business_id')->from('business_sector')->whereIn('business_sector.sector_id', explode(',', $request->sectors));
                });
            }

            

        if ($request->exists('filterby')) {
            if ($request->filterby === 'VENTA'){
                $businesses = $businesses->whereNull('businesses.rental');
                $businesses = $businesses->whereNotNull('businesses.investment');
            } else if ($request->filterby === 'ALQUILER') {
                $businesses = $businesses->whereNull('businesses.investment');
                $businesses = $businesses->whereNotNull('businesses.rental');
            }
        }
        /**
         * CONDITIONS
         */
        $businesses = $businesses->select('businesses.id as id_business', 'businesses.id_code as id_code_business', 'businesses.name as name_business', 'businesses.lat as lat_business', 'businesses.lng as lng_business', 'businesses.times_viewed as times_viewed_business', 'businesses.investment as investment_business', 'businesses.created_at as created_at_business', 'businesses.updated_at as updated_at_business', 'neighborhoods.id as id_neighborhood', 'neighborhoods.name as name_neighborhood', 'districts.id as id_district', 'districts.name as name_district', 'municipalities.id as id_municipality', 'municipalities.name as name_municipality', 'provinces.id as id_province', 'provinces.name as name_province', 'autonomous_communities.id as id_autonomous_community', 'autonomous_communities.name as name_autonomous_community', 'business_types.id as id_business_type', 'business_types.name as name_business_type', 'businesses.recommendation_finished_at as recommendation_finished_at', 'businesses.flag_sold as sold', 'businesses.rental as rental', 'businesses.flag_outstanding as flag_outstanding', 'businesses.source_platform as source_platform', 'subquery.sector_ids', 'businesses.business_images_string', 'businesses.business_videos_string', 'businesses.garage', 'businesses.storage', 'businesses.pool', 'businesses.rooms', 'businesses.bathrooms', 'businesses.floors', 'businesses.floor', 'businesses.year_built', 'businesses.courtyard', 'businesses.elevator', 'businesses.new_or_used', 'businesses.size as size_business', 'businesses.profit_sale', 'businesses.profit_rent', 'businesses.reform_price', 'businesses.source_timestamp', 'businesses.fecha_de_entrega', DB::raw('CEIL(investment / size) as investment_sqm')   )
            ->leftJoinSub(function ($query) {
                $query->select('business_id', DB::raw('GROUP_CONCAT(sector_id) as sector_ids'))->from('business_sector')->groupBy('business_id');
            }, 'subquery', 'subquery.business_id', '=', 'businesses.id');

        if (isset(Auth::user()->client->id)) {
            $businesses = $businesses->addSelect([
                'favorite' =>
                DB::table('favorites')->whereColumn('business_id', 'businesses.id')->where('client_id', Auth::user()->client->id)->select(DB::raw("COUNT(client_id) AS count_favorite"))
            ]);
            if ($request->exists('my_favorite')) {
                if ($request->my_favorite)
                    $businesses = $businesses->leftJoin('favorites', 'favorites.business_id', '=', 'businesses.id')
                        ->where('favorites.client_id', Auth::user()->client->id);
            }
            if ($request->exists('only_recommendations')) {
                if ($request->only_recommendations) {
                    $businesses = $businesses->leftJoin('recommendations', 'recommendations.business_id', '=', 'businesses.id')
                        ->where('recommendations.client_id', Auth::user()->client->id);
                }
            }
            if ($request->exists('my_preference')) {
                $businesses = $businesses->whereIn('businesses.id', $this->getBusinessListofAClient(Auth::user()->client->id));
            }
        } else {
            $businesses = $businesses->addSelect([
                'favorite' =>
                DB::table('favorites')->whereColumn('business_id', 'businesses.id')->where('client_id', -1)->select(DB::raw("COUNT(client_id) AS count_favorite"))
            ]);
        }

        $total = $businesses->count();
        
        $page = $request->exists('page') ? (int) $request->page : 1; // Initial page, cast to int
        if ($page <= 1) $page = 1;
        $perPage = $request->exists('perPage') ? (int) $request->perPage : 12; // Default lowest per page, cast to int
        $offset = ($page - 1) * $perPage;
        
        $businesses = $businesses->offset($offset)->limit($perPage)->get();
        
        for ($i = 0; $i < count($businesses); $i++) {
            // CHECK IF IT RETURNS AND ARRAY
            if (is_null($businesses[$i]["sector_ids"])) {
                $business_sectors = [];
            } else {
                $business_sectors = explode(",", $businesses[$i]["sector_ids"]);
            }

            $sectorTotal = count($business_sectors);
            $sectorsArray = [];
            for ($j = 0; $j < $sectorTotal; $j++) {
                if (isset($sector_map[$business_sectors[$j]]))
                    $sectorsArray[] = $sector_map[$business_sectors[$j]];
            }
            $businesses[$i]['sector'] = implode(", ", $sectorsArray);

            $auxAdd = false;
            $businesses[$i]['recommended'] = 0; // Por default los negocios no están recomendados

            if ($businesses[$i]->recommendation_finished_at == null) {
                $auxAdd = true;
            } else {
                $fineshedR = Carbon::createFromFormat('Y-m-d', $businesses[$i]->recommendation_finished_at);
                $today = Carbon::now()->format('Y-m-d');
                if ($today > $fineshedR) {
                    $auxAdd = true;
                } else {
                    if (isset(Auth::user()->client->id)) {
                        if ($this->businessClientRecommendation($businesses[$i]->id_business, Auth::user()->client->id)) {
                            $auxAdd = true;
                        }
                    }
                }
            }
            if (isset(Auth::user()->client->id) && $auxAdd) {
                if ($this->businessClientRecommendation($businesses[$i]->id_business, Auth::user()->client->id))
                    $businesses[$i]['recommended'] = 1;
            }
        }

        $response = array(
            'page' => $page,
            'total' => $total,
            'items' => count($businesses),
            'businesses' => $businesses,
            'status' => 'success'
        );

        if (isset(Auth::user()->client->id) && $request->exists('add_timeline') && $request->add_timeline == 1)
            addClientTimeline(Auth::user()->client->id, 1, 'Business', 'list', true, $request->all());

        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make(
            $request->all(),
            [
                'municipality_id'       =>  'required|numeric|exists:municipalities,id',
                'business_type_id'      =>  'required|numeric|exists:business_types,id',
                'name'                  =>  'required',
                'neighborhood_id'       =>  'numeric|exists:neighborhoods,id',
                'district_id'           =>  'numeric|exists:districts,id',

            ],
            [
                'municipality_id.required'      =>  'El ID del Municipio es requerido',
                'municipality_id.numeric'       =>  'El ID del Municipio debe ser del tipo numérico',
                'municipality_id.exists'        =>  'El ID del Municipio no existe en la BD',
                'business_type_id.required'     =>  'El ID del Tipo de Negocio es requerido',
                'business_type_id.numeric'      =>  'El ID del Tipo de Negocio debe ser del tipo numérico',
                'business_type_id.exists'       =>  'El ID del Tipo de Negocio no existe en la BD',
                'name.required'                 =>  'El Nombre del Negocio es requerido',
                'neighborhood_id.numeric'       =>  'El ID del Barrio debe ser del tipo numérico',
                'neighborhood_id.exists'        =>  'El ID del Barrio no existe en la BD',
                'district_id.numeric'           =>  'El ID del Distrito debe ser del tipo numérico',
                'district_id.exists'            =>  'El ID del Distrito no existe en la BD',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $business = new Business();

        $business->municipality_id = $request->municipality_id;
        $business->business_type_id = $request->business_type_id;
        $business->employee_id = Auth::user()->employee->id;
        $business->name = $request->name;
        $business->id_code  = Business::max('id_code') + 1; // ID (público)

        if ($request->exists('sectors')) {
            $sectors = explode(',', $request->sectors);
            for ($i = 0; $i < count($sectors); $i++) {
                if (Sector::where('id', $sectors[$i])->count() == 0)
                    return response()->json(['errors' => "El Sector con ID $sectors[$i] no existe"], 422);
            }
        }
        if ($request->exists('neighborhood_id')) $business->neighborhood_id = $request->neighborhood_id;
        if ($request->exists('district_id')) $business->district_id = $request->district_id;
        if ($request->exists('description')) $business->description = $request->description;
        if ($request->exists('address')) $business->address = $request->address;
        if ($request->exists('lat')) $business->lat = $request->lat;
        if ($request->exists('lng')) $business->lng = $request->lng;
        if ($request->exists('link_map')) $business->link_map = $request->link_map;
        if ($request->exists('zip_code')) $business->zip_code = $request->zip_code;
        if ($request->exists('size')) $business->size = $request->size;
        if ($request->exists('age')) $business->age = $request->age;
        if ($request->exists('website')) $business->website = $request->website;
        if ($request->exists('link')) $business->link = $request->link;
        if ($request->exists('private_comment')) $business->private_comment = $request->private_comment;
        if ($request->exists('data_of_interest')) $business->data_of_interest = $request->data_of_interest;
        if ($request->exists('relevant_advantages')) $business->relevant_advantages = $request->relevant_advantages;
        if ($request->exists('monthly_billing')) $business->monthly_billing = $request->monthly_billing;
        if ($request->exists('contact_name')) $business->contact_name = $request->contact_name;
        if ($request->exists('contact_landline')) $business->contact_landline = $request->contact_landline;
        if ($request->exists('contact_mobile_phone')) $business->contact_mobile_phone = $request->contact_mobile_phone;
        if ($request->exists('contact_email')) $business->contact_email = $request->contact_email;
        if ($request->exists('amount_requested_by_seller')) $business->amount_requested_by_seller = $request->amount_requested_by_seller;
        if ($request->exists('amount_offered_by_us')) $business->amount_offered_by_us = $request->amount_offered_by_us;
        if ($request->exists('investment')) $business->investment = $request->investment;
        if ($request->exists('royalty')) $business->royalty = $request->royalty;
        if ($request->exists('rental')) $business->rental = $request->rental;
        if ($request->exists('contract')) $business->contract = $request->contract;
        if ($request->exists('rental_contract_years')) $business->rental_contract_years = $request->rental_contract_years;
        if ($request->exists('franchise_contract_years')) $business->franchise_contract_years = $request->franchise_contract_years;
        if ($request->exists('rental_contract_years_left')) $business->rental_contract_years_left = $request->rental_contract_years_left;
        if ($request->exists('franchise_contract_years_left')) $business->franchise_contract_years_left = $request->franchise_contract_years_left;
        if ($request->exists('minimum_population')) $business->minimum_population = $request->minimum_population;
        if ($request->exists('canon_of_advertising')) $business->canon_of_advertising = $request->canon_of_advertising;
        if ($request->exists('canon_of_entrance')) $business->canon_of_entrance = $request->canon_of_entrance;
        if ($request->exists('flag_exclusive')) $business->flag_exclusive = $request->flag_exclusive;
        if ($request->exists('flag_active')) $business->flag_active = $request->flag_active;
        if ($request->exists('flag_sold')) $business->flag_sold = $request->flag_sold;
        if ($request->exists('flag_outstanding')) $business->flag_outstanding = $request->flag_outstanding;

        if ($request->exists('return_of_investment')) $business->return_of_investment = $request->return_of_investment;

        if ($request->exists('working_hours')) $business->working_hours = $request->working_hours;
        if ($request->exists('full_time_employees')) $business->full_time_employees = $request->full_time_employees;
        if ($request->exists('employees_part_time')) $business->employees_part_time = $request->employees_part_time;
        if ($request->exists('managers')) $business->managers = $request->managers;
        if ($request->exists('gross_revenue')) $business->gross_revenue = $request->gross_revenue;
        if ($request->exists('gross_profit')) $business->gross_profit = $request->gross_profit;
        if ($request->exists('expenses')) $business->expenses = $request->expenses;
        if ($request->exists('net')) $business->net = $request->net;
        if ($request->exists('owner_salary')) $business->owner_salary = $request->owner_salary;
        if ($request->exists('good_month_revenue')) $business->good_month_revenue = $request->good_month_revenue;
        if ($request->exists('bad_month_revenue')) $business->bad_month_revenue = $request->bad_month_revenue;
        if (!(empty($business->rental) || empty($business->size)))
            $business->price_per_sqm = round($business->rental / $business->size, 2);
        if ($request->exists('advertiser_owner_type')) $business->advertiser_owner_type = $request->advertiser_owner_type;
        if ($request->exists('facade_size')) $business->facade_size = $request->facade_size;
        if ($request->exists('flag_smoke_outlet')) $business->flag_smoke_outlet = $request->flag_smoke_outlet;
        if ($request->exists('flag_terrace')) $business->flag_terrace = $request->flag_terrace;

        if ($request->exists('profit_sale')) $business->profit_sale = $request->profit_sale;
        if ($request->exists('profit_rent')) $business->profit_rent = $request->profit_rent;
        if ($request->exists('reform_price')) $business->reform_price = $request->reform_price;
        if ($request->exists('source_timestamp')) $business->source_timestamp = $request->source_timestamp;

        if ($request->exists('business_images_string')) $business->business_images_string = $request->business_images_string;
        if ($request->exists('business_videos_string')) $business->business_videos_string = $request->business_videos_string;
        
        if ($business->save()) {

            $errorsMultimedia = addResourceBusinessMultimedia($business, $request);

            $errorsDossiers = addDossierInBusiness($business->id, $request);

            $errorsBusiest = addBusiestInBusiness($business->id, $request);

            addBusinessTimeline($business->id, 'Business', 'create');

            if ($request->exists('sectors')) {
                $sectors = explode(',', $request->sectors);
                for ($i = 0; $i < count($sectors); $i++) {
                    $business->sector()->attach($sectors[$i]);
                }
            }

            if ($request->exists('recommendation_clients'))
                $this->addRecomendationClients($request->recommendation_clients, $business);


            if (count($errorsMultimedia) == 0 && $errorsDossiers == null && $errorsBusiest == null)
                return response()->json(['status' => 'success'], 200);
            else
                return response()->json(['status' => 'success', 'errorsMultimedia' => $errorsMultimedia, 'errorsDossier' => $errorsDossiers, 'errorsBusiest' => $errorsBusiest], 200);
        }

        return response()->json(['errors' => 'No se pudo crear el Negocio'], 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function show($idBusiness) {
        if (Business::where('id', $idBusiness)->count() > 0) {
            $business = Business::where('id', $idBusiness)
                ->with(array('neighborhood' => function ($query) {
                    $query->select('id', 'name');
                }))
                ->with(array('district' => function ($query) {
                    $query->select('id', 'name');
                }))
                ->with(array('municipality' => function ($query) {
                    $query->select('id', 'name', 'province_id')->with(array('province' => function ($query) {
                        $query->select('id', 'name');
                    }));
                }))
                ->with(array('sector' => function ($query) {
                    $query->select('sectors.id as id_sector', 'name as name_sector');
                }))
                ->with(array('business_type' => function ($query) {
                    $query->select('id', 'name');
                }))
                ->with(array('employee' => function ($query) {
                    $query->select('id', 'name', 'surname');
                }))
                ->with('business_multimedia')
                ->with(array('dossiers' => function ($query) {
                    $query->join('files', 'files.id', '=', 'dossiers.file_id')
                        ->select('dossiers.id', 'business_id', 'file_id', 'original_name', 'extension', 'size', 'mime_type', 'path', 'full_path');
                }))
                ->with(array('busiests' => function ($query) {
                    $query->join('files', 'files.id', '=', 'busiests.file_id')
                        ->select('busiests.id', 'business_id', 'file_id', 'original_name', 'extension', 'size', 'mime_type', 'path', 'full_path');
                }))
                ->with(array('recommendations' => function ($query) {
                    $query->with(array('client' => function ($query2) {
                        $query2->select('id', 'names', 'surnames');
                    }));
                }))
                ->first();
            return response()->json([
                'status'    =>  'success',
                'business'    => $business
            ], 200);
        }

        return response()->json(['error' => 'El Negocio no existe'], 422);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idBusiness) {
        if (Business::where('id', $idBusiness)->count() > 0) {
            $validator = Validator::make(
                $request->all(),
                [
                    'municipality_id'       =>  'numeric|exists:municipalities,id',
                    'business_type_id'      =>  'numeric|exists:business_types,id'
                ],
                [
                    'municipality_id.numeric'       =>  'El ID del Municipio debe ser del tipo numérico',
                    'municipality_id.exists'        =>  'El ID del Municipio no existe en la BD',
                    'business_type_id.numeric'      =>  'El ID del Tipo de Negocio debe ser del tipo numérico',
                    'business_type_id.exists'       =>  'El ID del Tipo de Negocio no existe en la BD'
                ]
            );

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $business = Business::find($idBusiness);

            if ($request->exists('municipality_id')) {
                $business->municipality_id = $request->municipality_id;
            }

            if ($request->exists('district_id')) {
                if ($request->district_id == 'null')
                    $business->district_id = $request->district_id = null;
                else {

                    $validator = Validator::make(
                        $request->all(),
                        [
                            'district_id'           =>  'numeric|exists:districts,id',
                        ],
                        [
                            'district_id.numeric'           =>  'El ID del Distrito debe ser del tipo numérico',
                            'district_id.exists'            =>  'El ID del Distrito no existe en la BD',
                        ]
                    );

                    if ($validator->fails())
                        return response()->json(['errors' => $validator->errors()], 422);

                    $business->district_id = $request->district_id;
                }
            }

            if ($request->exists('neighborhood_id')) {
                if ($request->neighborhood_id == 'null')
                    $business->neighborhood_id = $request->neighborhood_id = null;
                else {
                    $validator = Validator::make(
                        $request->all(),
                        [
                            'neighborhood_id'       =>  'numeric|exists:neighborhoods,id',
                        ],
                        [
                            'neighborhood_id.numeric'       =>  'El ID del Barrio debe ser del tipo numérico',
                            'neighborhood_id.exists'        =>  'El ID del Barrio no existe en la BD'
                        ]
                    );

                    if ($validator->fails())
                        return response()->json(['errors' => $validator->errors()], 422);

                    $business->neighborhood_id = $request->neighborhood_id;
                }
            }

            if ($request->exists('business_type_id')) {
                $business->business_type_id = $request->business_type_id;
            }

            if ($request->exists('sectors')) {
                $sectors = explode(',', $request->sectors);

                for ($i = 0; $i < count($sectors); $i++) {
                    if (Sector::where('id', $sectors[$i])->count() == 0)
                        return response()->json(['errors' => "El Sector con ID $sectors[$i] no existe"], 422);
                }

                $business->sector()->detach();

                for ($i = 0; $i < count($sectors); $i++) {
                    $business->sector()->attach($sectors[$i]);
                }
            }

            if ($request->exists('name')) $business->name = $request->name;
            if ($request->exists('description')) $business->description = $request->description;
            if ($request->exists('address')) $business->address = $request->address;
            if ($request->exists('lat')) $business->lat = $request->lat;
            if ($request->exists('lng')) $business->lng = $request->lng;
            if ($request->exists('link_map')) $business->link_map = $request->link_map;
            if ($request->exists('zip_code')) $business->zip_code = $request->zip_code;
            if ($request->exists('size')) $business->size = $request->size;
            if ($request->exists('age')) $business->age = $request->age;
            if ($request->exists('website')) $business->website = $request->website;
            if ($request->exists('link')) $business->link = $request->link;
            if ($request->exists('private_comment')) $business->private_comment = $request->private_comment;
            if ($request->exists('data_of_interest')) $business->data_of_interest = $request->data_of_interest;
            if ($request->exists('relevant_advantages')) $business->relevant_advantages = $request->relevant_advantages;
            if ($request->exists('monthly_billing')) $business->monthly_billing = $request->monthly_billing;
            if ($request->exists('contact_name')) $business->contact_name = $request->contact_name;
            if ($request->exists('contact_landline')) $business->contact_landline = $request->contact_landline;
            if ($request->exists('contact_mobile_phone')) $business->contact_mobile_phone = $request->contact_mobile_phone;
            if ($request->exists('contact_email')) $business->contact_email = $request->contact_email;
            if ($request->exists('amount_requested_by_seller')) $business->amount_requested_by_seller = $request->amount_requested_by_seller;
            if ($request->exists('amount_offered_by_us')) $business->amount_offered_by_us = $request->amount_offered_by_us;
            if ($request->exists('investment')) $business->investment = $request->investment;
            if ($request->exists('royalty')) $business->royalty = $request->royalty;
            if ($request->exists('rental')) $business->rental = $request->rental;
            if ($request->exists('contract')) $business->contract = $request->contract;
            if ($request->exists('rental_contract_years')) $business->rental_contract_years = $request->rental_contract_years;
            if ($request->exists('franchise_contract_years')) $business->franchise_contract_years = $request->franchise_contract_years;
            if ($request->exists('rental_contract_years_left')) $business->rental_contract_years_left = $request->rental_contract_years_left;
            if ($request->exists('franchise_contract_years_left')) $business->franchise_contract_years_left = $request->franchise_contract_years_left;
            if ($request->exists('minimum_population')) $business->minimum_population = $request->minimum_population;
            if ($request->exists('canon_of_advertising')) $business->canon_of_advertising = $request->canon_of_advertising;
            if ($request->exists('canon_of_entrance')) $business->canon_of_entrance = $request->canon_of_entrance;
            if ($request->exists('flag_exclusive')) $business->flag_exclusive = $request->flag_exclusive;
            if ($request->exists('flag_active')) $business->flag_active = $request->flag_active;
            if ($request->exists('flag_sold')) $business->flag_sold = $request->flag_sold;
            if ($request->exists('flag_outstanding')) $business->flag_outstanding = $request->flag_outstanding;

            if ($request->exists('return_of_investment')) {
                if ($request->return_of_investment == 'null')
                    $business->return_of_investment = null;
                else
                    $business->return_of_investment = $request->return_of_investment;
            }

            if ($request->exists('working_hours')) $business->working_hours = $request->working_hours;
            if ($request->exists('full_time_employees')) $business->full_time_employees = $request->full_time_employees;
            if ($request->exists('employees_part_time')) $business->employees_part_time = $request->employees_part_time;
            if ($request->exists('managers')) $business->managers = $request->managers;
            if ($request->exists('gross_revenue')) $business->gross_revenue = $request->gross_revenue;
            if ($request->exists('gross_profit')) $business->gross_profit = $request->gross_profit;
            if ($request->exists('expenses')) $business->expenses = $request->expenses;
            if ($request->exists('net')) $business->net = $request->net;
            if ($request->exists('owner_salary')) $business->owner_salary = $request->owner_salary;
            if ($request->exists('good_month_revenue')) $business->good_month_revenue = $request->good_month_revenue;
            if ($request->exists('bad_month_revenue')) $business->bad_month_revenue = $request->bad_month_revenue;
            if (!(empty($business->rental) || empty($business->size)))
                $business->price_per_sqm = round($business->rental / $business->size, 2);
            if ($request->exists('advertiser_owner_type')) $business->advertiser_owner_type = $request->advertiser_owner_type;
            if ($request->exists('facade_size')) $business->facade_size = $request->facade_size;
            if ($request->exists('flag_smoke_outlet')) $business->flag_smoke_outlet = $request->flag_smoke_outlet;
            if ($request->exists('flag_terrace')) $business->flag_terrace = $request->flag_terrace;

            if ($request->exists('profit_sale')) $business->profit_sale = $request->profit_sale;
            if ($request->exists('profit_rent')) $business->profit_rent = $request->profit_rent;
            if ($request->exists('reform_price')) $business->reform_price = $request->reform_price;
            if ($request->exists('source_timestamp')) $business->source_timestamp = $request->source_timestamp;

            if ($request->exists('business_images_string')) $business->business_images_string = $request->business_images_string;
            if ($request->exists('business_videos_string')) $business->business_videos_string = $request->business_videos_string;
       

            if ($business->save()) {
                addBusinessTimeline($business->id, 'Business', 'update');
                return response()->json(['status' => 'success'], 200);
            }

            return response()->json(['errors' => 'No se pudo actualizar el Negocio'], 422);
        }

        return response()->json(['errors' => 'El Negocio no existe'], 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function destroy($idBusiness) {
        if (Business::where('id', $idBusiness)->count() > 0) {
            $business = Business::find($idBusiness);

            if (!$business->delete())
                return response()->json(['errors' => 'No se pudo borrar el negocio'], 422);

            if (BusinessMultimedia::where('business_id', $idBusiness)->delete())
                File::deleteDirectory(public_path("files/business-multimedia/$idBusiness"));

            /* Se borrará, sin embargo el delete quedará en BD en dado caso de hacer una inspección */
            addBusinessTimeline($idBusiness, 'Business', 'delete');

            BusinessTimeline::where('business_id', $idBusiness)->delete();

            return response()->json(['status' => 'success'], 200);
        }

        return response()->json(['errors' => 'El Negocio no existe'], 422);
    }


    /**
     * Display a listing of only the urls of the resource.
     *
     * @param  \App\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function listUrls(Request $request) {
        $query = Business::select('source_url')->whereNotNull('source_url');
        if ($request->exists('source_platform')) {
            $query->where('source_platform', 'LIKE', '%' . $request->source_platform . '%');
        }
        $urls = $query->get()->pluck('source_url')->toArray();
        return response()->json([
            'status'    =>  'success',
            'urls'      =>  $urls
        ], 200);
    }

    /** MÉTODOS PARA USUARIOS */
    public function publicIndex(Request $request) {
        // Creating Sector Map to prevent calling the DB Infinite times
        $sector_map = array();
        $sectors = DB::table("sectors")->get();
        foreach ($sectors as $sector) {
            $sector_map[$sector->id] = $sector->name;
        }

        $businesses = Business::leftJoin('business_types', 'business_types.id', '=', 'businesses.business_type_id')
            ->leftJoin('neighborhoods', 'neighborhoods.id', '=', 'businesses.neighborhood_id')
            ->leftJoin('districts', 'districts.id', '=', 'businesses.district_id')
            ->leftJoin('municipalities', 'municipalities.id', '=', 'businesses.municipality_id')
            ->leftJoin('provinces', 'provinces.id', '=', 'municipalities.province_id')
            ->leftJoin('autonomous_communities', 'autonomous_communities.id', '=', 'provinces.autonomous_community_id');

        /**
         * CONDITIONS
         */
            if ($request->exists('condition')) {
                switch ($request->condition) {
                    case 'mistery_option':
                        $businesses = $businesses->whereNotIn('businesses.id', BusinessMultimedia::where('type', 'image')->distinct()->pluck('business_id'));
                        break;
                    case 'with_analysis':
                        $businesses = $businesses->whereIn('businesses.id', BusinessMultimedia::where('type', 'video')->distinct()->whereIn('type_client', ['usuario.free', 'all'])->pluck('business_id'));
                        break;
                    case 'franchise':
                        $businesses = $businesses->where('businesses.flag_active', true)->whereIn('businesses.business_type_id', [2, 13]);
                        break;
                    case 'inmuebles':
                        $businesses = $businesses->where('businesses.flag_active', true)->whereIn('businesses.business_type_id', [3]);
                        break;
                    default: 
                        $businesses = $businesses->where('businesses.flag_active', true)->whereIn('businesses.business_type_id', [1]);
                        // Default: Negocios
                }
            } else {
                $businesses = $businesses->where('businesses.flag_active', true)->whereIn('businesses.business_type_id', [1]);
            }

            if ($request->exists('id_code'))
                $businesses = $businesses->where('businesses.id_code', $request->id_code);

            if ($request->exists('flag_sold')) {
                if (in_array(strval($request->flag_sold), ['0', '1']))
                    $businesses = $businesses->where('businesses.flag_sold', $request->flag_sold);
            }

            if ($request->exists('province_id'))
                $businesses = $businesses->where('provinces.id', $request->province_id);

            if ($request->exists('municipality_id'))
                $businesses = $businesses->where('municipalities.id', $request->municipality_id);

            if ($request->exists('district_id'))
                $businesses = $businesses->where('districts.id', $request->district_id);

            if ($request->exists('neighborhood_id'))
                $businesses = $businesses->where('neighborhoods.id', $request->neighborhood_id);

            if ($request->exists('neighborhood'))
                $businesses = $businesses->where('neighborhoods.name', 'like', '%' . $request->neighborhood . '%');

            if ($request->exists('autonomous_community_id'))
                $businesses = $businesses->where('autonomous_communities.id', $request->autonomous_community_id);

            if (!$request->exists('min_investment') && $request->exists('hide_priceless_biz') && $request->hide_priceless_biz == 1)
                $businesses = $businesses->where('businesses.investment', '>=', 100);

            if ($request->exists('min_investment'))
                $businesses = $businesses->where('businesses.investment', '>=', $request->min_investment);

            if ($request->exists('max_investment'))
                $businesses = $businesses->where('businesses.investment', '<=', $request->max_investment);

            if ($request->exists('min_rental'))
                $businesses = $businesses->where('businesses.rental', '>=', $request->min_rental);

            if ($request->exists('max_rental'))
                $businesses = $businesses->where('businesses.rental', '<=', $request->max_rental);

            if ($request->exists('only_with_gcs')) {
                if ($request->only_with_gcs == 1)
                    $businesses = $businesses->whereNotNull('businesses.lat') ->whereNotNull('businesses.lng');
            }
            if ($request->exists('flag_smoke_outlet')) {
                if (in_array(strval($request->flag_smoke_outlet), ['0', '1']))
                    $businesses = $businesses->where('businesses.flag_smoke_outlet', $request->flag_smoke_outlet);
            }
            if ($request->exists('flag_terrace')) {
                if (in_array(strval($request->flag_terrace), ['0', '1']))
                    $businesses = $businesses->where('businesses.flag_terrace', $request->flag_terrace);
            }

            if ($request->exists('flag_outstanding')) {
                if (in_array(strval($request->flag_outstanding), ['0', '1']))
                    $businesses = $businesses->where('businesses.flag_outstanding', $request->flag_outstanding);
            }

            if ($request->exists('name') && $request->name != '') {
                $search = $request->name;
                $businesses = $businesses->where(function ($query) use ($search) {
                    $query->where('businesses.name', 'like', "%$search%")->orWhere('businesses.description', 'like', "%$search%");
                });
            }

            if ($request->exists('garage'))
                $businesses = $businesses->where('businesses.garage', 'si');

            if ($request->exists('storage'))
                $businesses = $businesses->where('businesses.storage', 'si');

            if ($request->exists('pool'))
                $businesses = $businesses->where('businesses.pool', 'si');

            if ($request->exists('minrooms'))
                $businesses = $businesses->where('businesses.rooms', '>=', $request->minrooms);

            if ($request->exists('maxrooms'))
                $businesses = $businesses->where('businesses.rooms', '<=', $request->maxrooms);

            if ($request->exists('minbathrooms'))
                $businesses = $businesses->where('businesses.bathrooms', '>=', $request->minbathrooms);

            if ($request->exists('maxbathrooms'))
                $businesses = $businesses->where('businesses.bathrooms', '<=', $request->maxbathrooms);

            if ($request->exists('courtyard'))
                $businesses = $businesses->where('businesses.courtyard', $request->courtyard); //interior exterior
            if ($request->exists('elevator'))
                $businesses = $businesses->where('businesses.elevator', 'Con ascensor');

            if ($request->exists('new_or_used'))
                $businesses = $businesses->where('businesses.new_or_used', $request->new_or_used);

            if ($request->exists('order_by')) {
                if (in_array($request->order_by, ['investment_desc', 'investment_asc', 'date_desc', 'date_asc', 'most_relevant', 'investment_sqm_asc', 'investment_sqm_desc', 'size_asc', 'size_desc'])) {
                    switch ($request->order_by) {
                        case 'investment_asc':
                            $businesses = $businesses->orderBy('businesses.investment', 'asc');
                            break;
                        case 'investment_desc':
                            $businesses = $businesses->orderBy('businesses.investment', 'desc');
                            break;
                        case 'size_asc':
                            $businesses = $businesses->orderBy('businesses.size', 'asc');
                            $businesses = $businesses->where('businesses.size', '<', 10000);
                            $businesses = $businesses->where('businesses.size', '>', 10);
                            break;
                        case 'size_desc':
                            $businesses = $businesses->orderBy('businesses.size', 'desc');
                            $businesses = $businesses->where('businesses.size', '<', 10000);
                            $businesses = $businesses->where('businesses.size', '>', 10);
                            break;
                        case 'investment_sqm_asc':
                            $businesses = $businesses->orderBy(DB::raw('CEIL(investment / size)'), 'asc');
                            $businesses = $businesses->where('businesses.size', '<', 10000);
                            $businesses = $businesses->where('businesses.size', '>', 10);
                            break;
                        case 'investment_sqm_desc':
                            $businesses = $businesses->orderBy(DB::raw('CEIL(investment / size)'), 'desc');
                            $businesses = $businesses->where('businesses.size', '<', 10000);
                            $businesses = $businesses->where('businesses.size', '>', 10);
                            break;
                        case 'date_asc':
                            $businesses = $businesses->orderBy('businesses.created_at', 'asc');
                            break;
                        case 'date_desc':
                            $businesses = $businesses->orderBy('businesses.created_at', 'desc');
                            break;
                        case 'most_relevant':
                            $businesses = $businesses->orderBy('businesses.flag_outstanding', 'desc')->orderBy('businesses.flag_exclusive', 'desc');
                            break;
                    }
                }
            }

            if ($request->exists('sectors')) {
                $businesses = $businesses->whereIn('businesses.id', function ($query) use ($request) {
                    $query->select('business_id')->from('business_sector')->whereIn('business_sector.sector_id', explode(',', $request->sectors));
                });
            }

            

        if ($request->exists('filterby')) {
            if ($request->filterby === 'VENTA'){
                $businesses = $businesses->whereNull('businesses.rental');
                $businesses = $businesses->whereNotNull('businesses.investment');
            } else if ($request->filterby === 'ALQUILER') {
                $businesses = $businesses->whereNull('businesses.investment');
                $businesses = $businesses->whereNotNull('businesses.rental');
            }
        }
        /**
         * CONDITIONS
         */
        $businesses = $businesses->select('businesses.id as id_business', 'businesses.id_code as id_code_business', 'businesses.name as name_business', 'businesses.lat as lat_business', 'businesses.lng as lng_business', 'businesses.times_viewed as times_viewed_business', 'businesses.investment as investment_business', 'businesses.created_at as created_at_business', 'businesses.updated_at as updated_at_business', 'neighborhoods.id as id_neighborhood', 'neighborhoods.name as name_neighborhood', 'districts.id as id_district', 'districts.name as name_district', 'municipalities.id as id_municipality', 'municipalities.name as name_municipality', 'provinces.id as id_province', 'provinces.name as name_province', 'autonomous_communities.id as id_autonomous_community', 'autonomous_communities.name as name_autonomous_community', 'business_types.id as id_business_type', 'business_types.name as name_business_type', 'businesses.recommendation_finished_at as recommendation_finished_at', 'businesses.flag_sold as sold', 'businesses.rental as rental', 'businesses.flag_outstanding as flag_outstanding', 'businesses.source_platform as source_platform', 'subquery.sector_ids', 'businesses.business_images_string', 'businesses.business_videos_string', 'businesses.garage', 'businesses.storage', 'businesses.pool', 'businesses.rooms', 'businesses.bathrooms', 'businesses.floors', 'businesses.floor', 'businesses.year_built', 'businesses.courtyard', 'businesses.elevator', 'businesses.new_or_used', 'businesses.size as size_business', 'businesses.profit_sale', 'businesses.profit_rent', 'businesses.reform_price', 'businesses.source_timestamp', 'businesses.fecha_de_entrega', DB::raw('CEIL(investment / size) as investment_sqm')   )
            ->leftJoinSub(function ($query) {
                $query->select('business_id', DB::raw('GROUP_CONCAT(sector_id) as sector_ids'))->from('business_sector')->groupBy('business_id');
            }, 'subquery', 'subquery.business_id', '=', 'businesses.id');

        if (isset(Auth::user()->client->id)) {
            $businesses = $businesses->addSelect([
                'favorite' =>
                DB::table('favorites')->whereColumn('business_id', 'businesses.id')->where('client_id', Auth::user()->client->id)->select(DB::raw("COUNT(client_id) AS count_favorite"))
            ]);
            if ($request->exists('my_favorite')) {
                if ($request->my_favorite)
                    $businesses = $businesses->leftJoin('favorites', 'favorites.business_id', '=', 'businesses.id')
                        ->where('favorites.client_id', Auth::user()->client->id);
            }
            if ($request->exists('only_recommendations')) {
                if ($request->only_recommendations) {
                    $businesses = $businesses->leftJoin('recommendations', 'recommendations.business_id', '=', 'businesses.id')
                        ->where('recommendations.client_id', Auth::user()->client->id);
                }
            }
            if ($request->exists('my_preference')) {
                $businesses = $businesses->whereIn('businesses.id', $this->getBusinessListofAClient(Auth::user()->client->id));
            }
        } else {
            $businesses = $businesses->addSelect([
                'favorite' =>
                DB::table('favorites')->whereColumn('business_id', 'businesses.id')->where('client_id', -1)->select(DB::raw("COUNT(client_id) AS count_favorite"))
            ]);
        }

        $total = $businesses->count();
        
        $page = $request->exists('page') ? (int) $request->page : 1; // Initial page, cast to int
        if ($page <= 1) $page = 1;
        $perPage = $request->exists('perPage') ? (int) $request->perPage : 12; // Default lowest per page, cast to int
        $offset = ($page - 1) * $perPage;
        
        $businesses = $businesses->offset($offset)->limit($perPage)->get();
        
        for ($i = 0; $i < count($businesses); $i++) {
            // CHECK IF IT RETURNS AND ARRAY
            if (is_null($businesses[$i]["sector_ids"])) {
                $business_sectors = [];
            } else {
                $business_sectors = explode(",", $businesses[$i]["sector_ids"]);
            }

            $sectorTotal = count($business_sectors);
            $sectorsArray = [];
            for ($j = 0; $j < $sectorTotal; $j++) {
                if (isset($sector_map[$business_sectors[$j]]))
                    $sectorsArray[] = $sector_map[$business_sectors[$j]];
            }
            $businesses[$i]['sector'] = implode(", ", $sectorsArray);

            $auxAdd = false;
            $businesses[$i]['recommended'] = 0; // Por default los negocios no están recomendados

            if ($businesses[$i]->recommendation_finished_at == null) {
                $auxAdd = true;
            } else {
                $fineshedR = Carbon::createFromFormat('Y-m-d', $businesses[$i]->recommendation_finished_at);
                $today = Carbon::now()->format('Y-m-d');
                if ($today > $fineshedR) {
                    $auxAdd = true;
                } else {
                    if (isset(Auth::user()->client->id)) {
                        if ($this->businessClientRecommendation($businesses[$i]->id_business, Auth::user()->client->id)) {
                            $auxAdd = true;
                        }
                    }
                }
            }
            if (isset(Auth::user()->client->id) && $auxAdd) {
                if ($this->businessClientRecommendation($businesses[$i]->id_business, Auth::user()->client->id))
                    $businesses[$i]['recommended'] = 1;
            }
        }

        $response = array(
            'page' => $page,
            'total' => $total,
            'items' => count($businesses),
            'businesses' => $businesses,
            'status' => 'success'
        );

        if (isset(Auth::user()->client->id) && $request->exists('add_timeline') && $request->add_timeline == 1)
            addClientTimeline(Auth::user()->client->id, 1, 'Business', 'list', true, $request->all());

        return response()->json($response, 200);
    }


    public function publicWordPress(Request $request) {
        $request->request->remove('name');
        $request->request->remove('order_by');
        $request->request->add(['order_by' => 'most_relevant']);

        $businesses = Business::leftJoin('business_types', 'business_types.id', '=', 'businesses.business_type_id')
            ->leftJoin('municipalities', 'municipalities.id', '=', 'businesses.municipality_id')
            ->leftJoin('provinces', 'provinces.id', '=', 'municipalities.province_id')
            ->leftJoin('autonomous_communities', 'autonomous_communities.id', '=', 'provinces.autonomous_community_id');

        $businesses = $businesses->where('businesses.flag_active', true)->whereIn('businesses.business_type_id', [1]);

        if ($request->exists('province_id'))
            $businesses = $businesses->where('provinces.id', $request->province_id);

        if (!$request->exists('min_investment') && $request->exists('hide_priceless_biz') && $request->hide_priceless_biz == 1)
            $businesses = $businesses->where('businesses.investment', '>=', 100);

        if ($request->exists('min_investment'))
            $businesses = $businesses->where('businesses.investment', '>=', $request->min_investment);

        if ($request->exists('max_investment'))
            $businesses = $businesses->where('businesses.investment', '<=', $request->max_investment);

        if ($request->exists('min_rental'))
            $businesses = $businesses->where('businesses.rental', '>=', $request->min_rental);

        if ($request->exists('max_rental'))
            $businesses = $businesses->where('businesses.rental', '<=', $request->max_rental);

        $businesses = $businesses->select('businesses.id as id_business', 'businesses.id_code as id_code_business', 'businesses.name as name_business', 'businesses.lat as lat_business', 'businesses.lng as lng_business', 'businesses.times_viewed as times_viewed_business', 'businesses.investment as investment_business', 'businesses.created_at as created_at_business', 'businesses.updated_at as updated_at_business', 'municipalities.id as id_municipality', 'municipalities.name as name_municipality', 'provinces.id as id_province', 'provinces.name as name_province', 'autonomous_communities.id as id_autonomous_community', 'autonomous_communities.name as    name_autonomous_community', 'business_types.id as id_business_type', 'business_types.name as name_business_type', 'businesses.flag_sold as sold', 'businesses.rental as rental', 'businesses.flag_outstanding as flag_outstanding', 'businesses.source_platform as source_platform', 'subquery.sector_ids', 'businesses.business_images_string', 'businesses.business_videos_string', 'businesses.garage', 'businesses.storage', 'businesses.pool', 'businesses.rooms', 'businesses.bathrooms', 'businesses.floors', 'businesses.floor', 'businesses.year_built', 'businesses.courtyard', 'businesses.elevator', 'businesses.new_or_used', 'businesses.profit_sale', 'businesses.profit_rent', 'businesses.reform_price', 'businesses.source_timestamp', 'businesses.fecha_de_entrega')
            ->leftJoinSub(function ($query) {
                $query->select('business_id', DB::raw('GROUP_CONCAT(sector_id) as sector_ids'))->from('business_sector')->groupBy('business_id');
            }, 'subquery', 'subquery.business_id', '=', 'businesses.id');

        // Apply sector filter at DB level when possible
        if ($request->exists('sectors')) {
            $sectorIds = explode(',', str_replace(' ', '', $request->sectors));
            $businesses = $businesses->whereIn('subquery.sector_ids', $sectorIds);
        }

        $total = $businesses->count();

        $page = $request->exists('page') ? (int) $request->page : 1;
        if ($page <= 1) $page = 1;
        $perPage = $request->exists('perPage') ? (int) $request->perPage : 12;
        $offset = ($page - 1) * $perPage;

        $businesses = $businesses->offset($offset)->limit($perPage)->get();

        $sector_map = array();
        $sectors = DB::table("sectors")->get();
        foreach ($sectors as $sector) {
            $sector_map[$sector->id] = $sector->name;
        }

        $arrBR = [];
        for ($i = 0; $i < count($businesses); $i++) {
            // CHECK IF IT RETURNS AND ARRAY
            if (is_null($businesses[$i]["sector_ids"])) {
                $business_sectors = [];
            } else {
                $business_sectors = explode(",", $businesses[$i]["sector_ids"]);
            }

            $sectorsStr = '';
            $sectorTotal = count($business_sectors);

            for ($j = 0; $j < $sectorTotal; $j++) {
                // CHECK IF business' sector's ID, exists on $sector_map
                if (isset($sector_map[$business_sectors[$j]]))
                    $sectorsStr =  $sectorsStr . $sector_map[$business_sectors[$j]]; //Retiré el id_sector
                if ($j != ($sectorTotal - 1))
                    $sectorsStr = $sectorsStr . ',';
            }

            $businesses[$i]['sector'] = $sectorsStr;

            $businesses[$i]['business_images_string'] = explode(';', $businesses[$i]['business_images_string'])[0];
            $businesses[$i]['business_videos_string'] = explode(';', $businesses[$i]['business_videos_string'])[0];

            $businesses[$i]['recommended'] = 0; // Por default los negocios no están recomendados
            $auxAdd = false;

            if ($businesses[$i]->recommendation_finished_at == null) {
                array_push($arrBR, $businesses[$i]);
                $auxAdd = true;
            } else {
                $fineshedR = Carbon::createFromFormat('Y-m-d', $businesses[$i]->recommendation_finished_at);
                $today = Carbon::now()->format('Y-m-d');
                if ($today > $fineshedR) {
                    array_push($arrBR, $businesses[$i]);
                    $auxAdd = true;
                } else {
                    if (isset(Auth::user()->client->id)) {
                        if ($this->businessClientRecommendation($businesses[$i]->id_business, Auth::user()->client->id)) {
                            array_push($arrBR, $businesses[$i]);
                            $auxAdd = true;
                        }
                    }
                }
            }

            if (isset(Auth::user()->client->id) && $auxAdd) {
                $currentIndex = count($arrBR) - 1;
                if ($this->businessClientRecommendation($businesses[$i]->id_business, Auth::user()->client->id))
                    $arrBR[$currentIndex]['recommended'] = 1;
            }
        }

        $response = array('total' => $total, 'status' => 'success', 'page' => $page, 'items' => count($arrBR));
        $response['businesses'] = $arrBR;

        if (isset(Auth::user()->client->id) && $request->exists('add_timeline') && $request->add_timeline == 1)
            addClientTimeline(Auth::user()->client->id, 1, 'Business', 'list', true, $request->all());

        return response()->json($response, 200);
    }

    public function publicShow(Request $request, $idCodeBusiness) {
        Business::where('id_code', $idCodeBusiness)->increment('times_viewed');
        $business = Business::leftJoin('business_types', 'business_types.id', '=', 'businesses.business_type_id')
            ->leftJoin('neighborhoods', 'neighborhoods.id', '=', 'businesses.neighborhood_id')
            ->leftJoin('districts', 'districts.id', '=', 'businesses.district_id')
            ->leftJoin('municipalities', 'municipalities.id', '=', 'businesses.municipality_id')
            ->leftJoin('provinces', 'provinces.id', '=', 'municipalities.province_id')
            ->leftJoin('autonomous_communities', 'autonomous_communities.id', '=', 'provinces.autonomous_community_id')
            ->where('businesses.id_code', $idCodeBusiness)
            ->select('businesses.id as id_business', 'businesses.id_code as id_code_business', 'businesses.name as name_business', 'businesses.times_viewed as times_viewed_business', 'businesses.investment as investment_business', 'businesses.description as description_business', 'businesses.size as size_business', 'businesses.age as age_business', 'businesses.data_of_interest as data_of_interest_business', 'businesses.relevant_advantages as relevant_advantages_business', 'businesses.royalty as royalty_business', 'businesses.canon_of_advertising as canon_of_advertising_business', 'businesses.canon_of_entrance as canon_of_entrance_business', 'businesses.rental_contract_years as rental_contract_years_business', 'businesses.franchise_contract_years as franchise_contract_years_business', 'businesses.rental_contract_years_left as rental_contract_years_left_business', 'businesses.franchise_contract_years_left as franchise_contract_years_left_business', 'businesses.return_of_investment as return_of_investment_business', 'businesses.created_at as created_at_business', 'businesses.updated_at as updated_at_business', 'neighborhoods.id as id_neighborhood', 'neighborhoods.name as name_neighborhood', 'districts.id as id_district', 'districts.name as name_district', 'municipalities.id as id_municipality', 'municipalities.name as name_municipality', 'provinces.id as id_province', 'provinces.name as name_province', 'autonomous_communities.id as id_autonomous_community', 'autonomous_communities.name as name_autonomous_community', 'business_types.id as id_business_type', 'business_types.name as name_business_type', 'businesses.rental as rental', 'businesses.lat as lat', 'businesses.lng as lng', 'businesses.link_map as link_map', 'businesses.working_hours as working_hours', 'businesses.full_time_employees', 'businesses.employees_part_time', 'businesses.managers', 'businesses.gross_revenue', 'businesses.gross_profit', 'businesses.expenses', 'businesses.net', 'businesses.owner_salary', 'businesses.good_month_revenue', 'businesses.bad_month_revenue', 'businesses.flag_sold as sold', 'businesses.address as address', 'businesses.contact_name as contact_name', 'businesses.contact_landline as contact_landline', 'businesses.contact_mobile_phone as contact_mobile_phone', 'businesses.contact_email as contact_email', 'businesses.source_timestamp as source_timestamp', 'businesses.price_per_sqm as price_per_sqm', 'businesses.facade_size as facade_size', 'businesses.flag_smoke_outlet as flag_smoke_outlet', 'businesses.flag_terrace as flag_terrace', 'businesses.flag_outstanding as flag_outstanding', 'businesses.source_platform as source_platform', 'businesses.business_images_string', 'businesses.business_videos_string', 'businesses.garage', 'businesses.storage', 'businesses.pool', 'businesses.rooms', 'businesses.bathrooms', 'businesses.floors', 'businesses.floor', 'businesses.year_built', 'businesses.courtyard', 'businesses.elevator', 'businesses.new_or_used', 'businesses.profit_sale', 'businesses.profit_rent', 'businesses.reform_price', 'businesses.source_timestamp', 'businesses.fecha_de_entrega');


        $today = Carbon::now()->format('Y-m-d');

        if ($request->exists('active'))
            if ($request->active == 'ignore')
                $business = $business->whereIn('businesses.flag_active', [true, false]);
            else
                $business = $business->where('businesses.flag_active', true);
        else
            $business = $business->where('businesses.flag_active', true);

        if (isset(Auth::user()->client->id)) {
            $business = $business->addSelect([
                'favorite' =>
                DB::table('favorites')->whereColumn('business_id', 'businesses.id')->where('client_id', Auth::user()->client->id)->select(DB::raw("COUNT(client_id) AS count_favorite"))
            ]);

            $business = $business->leftJoin('recommendations', 'recommendations.business_id', '=', 'businesses.id')
                ->where(function ($query) use ($today) {
                    $query->where('businesses.recommendation_finished_at', '<', $today)
                        ->orWhereNull('businesses.recommendation_finished_at')
                        ->orWhere('recommendations.client_id', Auth::user()->client->id);
                });
        } else {
            $business = $business->addSelect([
                'favorite' =>
                DB::table('favorites')->whereColumn('business_id', 'businesses.id')->where('client_id', -1)->select(DB::raw("COUNT(client_id) AS count_favorite"))
            ]);

            $business = $business->where(function ($query) use ($today) {
                $query->where('businesses.recommendation_finished_at', '<', $today)
                    ->orWhereNull('businesses.recommendation_finished_at');
            });
        }

        $business = $business->first();

        if ($business) {
            $sector = Sector::leftJoin('business_sector', 'business_sector.sector_id', '=', 'sectors.id')
                ->where('business_sector.business_id', $business->id_business)
                ->select('sectors.id as id', 'sectors.name as name')
                ->get();

            $businessMultimedia = BusinessMultimedia::whereIn('type', ['image', 'video'])
                ->where('business_id', $business->id_business)
                ->select('id', 'type', 'link_video', 'medium_image_path', 'type_client')
                ->get();

            $dossiers = Dossier::leftJoin('files', 'files.id', '=', 'dossiers.file_id')
                ->where('dossiers.business_id', $business->id_business)
                ->select('dossiers.id', 'business_id', 'file_id', 'original_name', 'extension', 'size', 'mime_type', 'path', 'full_path')
                ->get();

            $busiests = Busiest::leftJoin('files', 'files.id', '=', 'busiests.file_id')
                ->where('busiests.business_id', $business->id_business)
                ->select('busiests.id', 'business_id', 'file_id', 'original_name', 'extension', 'size', 'mime_type', 'path', 'full_path')
                ->get();

            if (isset(Auth::user()->client->id)) {
                $business['recommended'] = 0;

                if ($this->businessClientRecommendation($business->id_business, Auth::user()->client->id))
                    $business['recommended'] = 1;
            } else
                $business['recommended'] = 0;
        }

        $showPopUp = false;

        if (isset(Auth::user()->client->id)) {
            addClientTimeline(Auth::user()->client->id, 1, 'Business', 'show', true, ['id' => $business->id_business, 'id_code' => $business->id_code_business]);

            if (checkIfTheUserHasTheRole('cliente.free.trial', Auth::user()->getRoles(), 'slug')) {
                $auxCount = ClientTimeline::where('client_id', Auth::user()->client->id)
                    ->where('module_eng', 'Business')
                    ->where('type_crud_eng', 'show')
                    ->count();

                if ($auxCount % 5 == 0)
                    $showPopUp = true;
            }
        }


        return response()->json([
            'status'                => 'success',
            'business'              => $business,
            'sector'                => (isset($sector)) ? $sector : [],
            'businessMultimedia'    => (isset($businessMultimedia)) ? $businessMultimedia : [],
            'dossiers'              => (isset($dossiers)) ? $dossiers : [],
            'busiests'              => (isset($busiests)) ? $busiests : [],
            'show_pop_up'           => $showPopUp,
        ], 200);

        return response()->json(['errors' => 'No existe el Negocio']);
    }

    private function businessClientRecommendation($idBusiness, $idClient): bool {
        return Recommendation::where('business_id', $idBusiness)
            ->where('client_id', $idClient)
            ->exists();
    }

    public function addRecommendation(Request $request, $idBusiness) {
        $business = Business::find($idBusiness);

        if (!$business)
            return response()->json(['errors' => 'No existe el Negocio']);

        if ($request->exists('recommendation_clients')) {
            $this->addRecomendationClients($request->recommendation_clients, $business);
        }

        return response()->json([
            'status'                => 'success',
        ], 200);
    }

    private function addRecomendationClients($recommendationClients, $business) {
        if ($recommendationClients == '' || is_null($recommendationClients))
            return;

        $deletedRows = Recommendation::where('business_id', $business->id)->delete();

        $endDate = Carbon::now()->addDays(7)->format('Y-m-d');
        $business->recommendation_finished_at = $endDate;
        $auxClients = explode(',', $recommendationClients);
        for ($i = 0; $i < count($auxClients); $i++) {
            $client = Client::find($auxClients[$i]);
            if ($client) {
                if (checkIfTheUserHasTheRole(['cliente.fase.evaluacion', 'cliente.fase.analisis', 'cliente.fase.ejecucion', 'cliente.fase.asesoramiento.integral', 'usuario.premium.menor', 'usuario.premium.mayor', 'cliente.anual', 'cliente.mensual', 'cliente.free.trial'], $client->user->getRoles(), 'slug')) {
                    $recommendation = new Recommendation();
                    $recommendation->client_id = $auxClients[$i];
                    $recommendation->business_id = $business->id;
                    $recommendation->save();

                    notificationNegocioCumpleConPreferenciasDelCliente($auxClients[$i], $business);
                }
            }
        }

        $business->save();
    }

    public function activateFromExcel(Request $request) {
        $validator = Validator::make(
            $request->all(),
            [
                'file'                  =>  'required|file|max:4096|mimes:xls,xlsx',
            ],
            [
                'file.required'         =>  'El Archivo es requerido',
                'file.file'             =>  'El Archivo debe ser del tipo binario',
                'file.max'              =>  'El Archivo debe tener un peso máximo de 4MB',
                'file.mimes'            =>  'El Archivo debe ser xls o xlsx',
            ]
        );

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $today = Carbon::now()->format('d-m-Y H:i:s');

        try {
            Log::info('==========================================');
            Log::info('Inicio Activación de Negocios');
            Log::info('Fecha: ' . $today);
            Log::info('==========================================');

            $arrReturn = ['loaded_rows' => '', 'unloaded_rows' => '', 'update_business_id_crm' => '', 'highest_row' => 0];

            $import = (new ActivateBusinessImport($arrReturn))->import($request->file('file'));
        } catch (\Exception $e) {
            Log::info("Exception in activateFromExcel-BusinessController - $today");
            Log::info($e);
            return response()->json(['error' => 'Error no determinado. Revise el archivo', 'rows' => $arrReturn], 400);
        } catch (\Throwable $e) {
            Log::info("Throwable in activateFromExcel-BusinessController - $today");
            Log::info($e);
            return response()->json(['error' => 'Error no determinado. Revise el archivo', 'rows' => $arrReturn], 400);
        }

        Log::info('==========================================');
        Log::info('Fin de Activación de Negocios');
        Log::info('==========================================');

        return response()->json(['status' => 'success', 'rows' => $arrReturn], 200);
    }

    public function deactivateAndActivateFromExcel(Request $request) {
        /**
         * Solo quedarán activos los negocios que se encuentren en este excel (flag_active = true)
         * La única excepción son aquellos negocios donde source_platform sea Manual
         * Se utiliza la URL del negocio en la plataforma de origen (source_url)
         */
        $validator = Validator::make(
            $request->all(),
            [
                'file'                  =>  'required|file|max:4096|mimes:xls,xlsx',
                'type'                  =>  'required|in:"completa", "parcial"',
            ],
            [
                'file.required'         =>  'El Archivo es requerido',
                'file.file'             =>  'El Archivo debe ser del tipo binario',
                'file.max'              =>  'El Archivo debe tener un peso máximo de 4MB',
                'file.mimes'            =>  'El Archivo debe ser xls o xlsx',
                'type.required'         =>  'Debe indicar el tipo de carga',
                'type.in'               =>  'El tipo de carga solo puede ser completa o parcial',
            ]
        );

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $today = Carbon::now()->format('d-m-Y H:i:s');

        $OriginalActiveBusinessId = Business::where('flag_active', true)->whereNotIn('source_platform', ['Manual', 'mundoFranquicia'])->pluck('id');

        $startTime = microtime(true);
        try {
            Log::info('==============================================');
            Log::info('Inicio Desactivación y Activación de Negocios');
            Log::info('Fecha: ' . $today);
            Log::info('==============================================');

            // CORRECTOR OF IDS 
            // IDEALISTA
            DB::statement("UPDATE businesses SET id_business_platform = SUBSTRING_INDEX( CASE WHEN SUBSTR(source_url, -1) = '/' THEN SUBSTRING(source_url, 1, LENGTH(source_url) - 1) ELSE source_url END, '/', -1) WHERE source_platform = 'Idealista' AND id_business_platform IS NULL;");
            // BELBEX
            DB::statement("UPDATE businesses SET id_business_platform = REPLACE(SUBSTRING_INDEX(CASE WHEN SUBSTR(source_url, -1) = '/' THEN SUBSTRING(source_url, 1, LENGTH(source_url) - 1) ELSE source_url END, '/', -2), '/alquiler', '') WHERE source_platform = 'Belbex' AND id_business_platform IS NULL;");
            // FOTOCASA
            DB::statement("UPDATE businesses SET id_business_platform = SUBSTRING_INDEX(REPLACE(REPLACE(SUBSTRING_INDEX(source_url, '/', -2), '/d?from=list', ''), '/d', ''), '/', 1) WHERE source_platform = 'Fotocasa' AND id_business_platform IS NULL;");
            // MILANUNCIOS
            DB::statement("UPDATE businesses SET id_business_platform = SUBSTRING_INDEX(REPLACE(source_url, '.htm', ''), '-', -1) WHERE source_platform = 'Milanuncios' AND id_business_platform IS NULL;");

            $arrReturn = ['loaded_rows' => '', 'unloaded_rows' => '', 'update_business_id' => '', 'update_business_bdid' => '', 'highest_row' => 0];

            $import = (new ActivateBusinessURLImport($arrReturn))->import($request->file('file'));

            if ($request->type == 'completa') {
                Business::whereIn('id', $OriginalActiveBusinessId)->update(['flag_active' => false]);
            }
            $NewActiveBusinessesId = explode(',', $arrReturn['update_business_id']);
            Business::whereIn('id_business_platform', $NewActiveBusinessesId)->update(['flag_active' => true]);
        } catch (\Exception $e) {
            Business::whereIn('id', $OriginalActiveBusinessId)->update(['flag_active' => true]);
            Log::info("Exception in deactivateAndActivateFromExcel-BusinessController - $today");
            Log::info($e);
            return response()->json(['error' => 'Error no determinado. Revise el archivo', 'rows' => $arrReturn], 400);
        } catch (\Throwable $e) {
            Business::whereIn('id', $OriginalActiveBusinessId)->update(['flag_active' => true]);
            Log::info("Throwable in deactivateAndActivateFromExcel-BusinessController - $today");
            Log::info($e);
            return response()->json(['error' => 'Error no determinado. Revise el archivo', 'rows' => $arrReturn], 400);
        }

        Log::info('==============================================');
        Log::info('Fin de Desactivación y Activación de Negocios');
        Log::info('Tiempo: ' . (microtime(true) - $startTime) . 's');
        Log::info('==============================================');
        return response()->json(['status' => 'success', 'rows' => $arrReturn], 200);
    }

    public function indexforshare(Request $request) {
        /**
         * Este método solo debe ser usado por los empleados
         * ya que es el listado para las "tablas" de la parte administrativas
         */
        try {
            $businesses = Business::leftJoin('business_types', 'business_types.id', '=', 'businesses.business_type_id')
                ->leftJoin('neighborhoods', 'neighborhoods.id', '=', 'businesses.neighborhood_id')
                ->leftJoin('districts', 'districts.id', '=', 'businesses.district_id')
                ->leftJoin('municipalities', 'municipalities.id', '=', 'businesses.municipality_id')
                ->leftJoin('provinces', 'provinces.id', '=', 'municipalities.province_id');

            $businesses = $businesses->where('businesses.flag_active', true);

            if ($request->exists('municipality'))
                $businesses = $businesses->where('municipalities.id', $request->municipality);
            if ($request->exists('district'))
                $businesses = $businesses->where('districts.id', $request->district);
            if ($request->exists('neighborhood'))
                $businesses = $businesses->where('neighborhoods.id', $request->neighborhood);
            if ($request->exists('province'))
                $businesses = $businesses->where('provinces.id', $request->province);

            // Negocios & Traspasos | Viviendas — Venta | Viviendas — Alquiler | Viviendas — Obra Nueva

            if ($request->exists('collector_id')) {
                $businesses = $businesses->where('businesses.collector_id', $request->collector_id);
            } else if ($request->exists('smartlink_id')) {
                $smartlink = Smartlink::find( $request->smartlink_id );
                $smartlink_businesses = explode( ';', $smartlink->businesses );
                $businesses = $businesses->whereIn('businesses.id', $smartlink_businesses);
            } else {
                if ($request->type == 0) {
                    $businesses = $businesses->where('businesses.business_type_id', 1);
                } else if ($request->type == 1) {
                    $businesses = $businesses->where('businesses.business_type_id', 3);
                    $businesses = $businesses->whereNotNull('businesses.investment');
                } else if ($request->type == 2) {
                    $businesses = $businesses->where('businesses.business_type_id', 3);
                    $businesses = $businesses->whereNotNull('businesses.rental');
                } else if ($request->type == 3) {
                    $businesses = $businesses->where('businesses.business_type_id', 3);
                    $businesses = $businesses->where('businesses.new_or_used', "Nuevo");
                } else {
                    $businesses = $businesses->where('businesses.business_type_id', 1);
                }
            }

            if ($request->type == 0 || $request->type == 1 || $request->type == 3) {
                if ($request->exists('min_investment'))
                    $businesses = $businesses->where('businesses.investment', '>=', $request->min_investment);
                if ($request->exists('max_investment'))
                    $businesses = $businesses->where('businesses.investment', '<=', $request->max_investment);
            }
            if ($request->type == 0 || $request->type == 2 || $request->type == 3) {
                if ($request->exists('min_rental'))
                    $businesses = $businesses->where('businesses.rental', '>=', $request->min_rental);
                if ($request->exists('max_rental')) 
                    $businesses = $businesses->where('businesses.rental', '<=', $request->max_rental);
            }
            if ($request->type !== 0) {
                if ($request->exists('min_rooms'))
                    $businesses = $businesses->where('businesses.rooms', '>=', $request->min_rooms);
                if ($request->exists('max_rooms'))
                    $businesses = $businesses->where('businesses.rooms', '<=', $request->max_rooms);
                if ($request->exists('min_bathrooms'))
                    $businesses = $businesses->where('businesses.bathrooms', '>=', $request->min_bathrooms);
                if ($request->exists('max_bathrooms'))
                    $businesses = $businesses->where('businesses.bathrooms', '<=', $request->max_bathrooms);
            }

            if ($request->order_by === 'Recientes') {
                $businesses = $businesses->orderBy('businesses.source_timestamp', 'desc');
            } else if ($request->order_by === 'Precio Asc') {
                $businesses = $businesses->orderBy('businesses.investment', 'asc')->orderBy('businesses.rental', 'asc');
            } else if ($request->order_by === 'Precio Desc') {
                $businesses = $businesses->orderBy('businesses.investment', 'desc')->orderBy('businesses.rental', 'desc');
            } else {
                $businesses = $businesses->orderBy('businesses.source_timestamp', 'desc');
            }

            

            $businesses = $businesses->select('businesses.id', 'businesses.name', 'businesses.investment', 'businesses.rental', 'businesses.contact_name', 'businesses.contact_landline', 'provinces.name as province', 'municipalities.name as municipality', 'districts.name as district', 'neighborhoods.name as neighborhood', 'business_types.name as type', 'businesses.rooms', 'businesses.bathrooms', 'sector_ids', 'source_timestamp', 'business_images_string')->leftJoinSub(function ($query) {
                $query->select('business_id', DB::raw('GROUP_CONCAT(sector_id) as sector_ids'))
                    ->from('business_sector')->groupBy('business_id');
            }, 'subquery', 'subquery.business_id', '=', 'businesses.id');

            // Apply sector filter at DB level when possible
            if ($request->exists('sector')) {
                $sectorIds = explode(',', str_replace(' ', '', $request->sector));
                $businesses = $businesses->whereIn('subquery.sector_ids', $sectorIds);
            }

            $total = $businesses->count();

            $page = $request->exists('page') ? (int) $request->page : 1;
            if ($page <= 1) $page = 1;
            $perPage = $request->exists('perPage') ? (int) $request->perPage : 12;
            $offset = ($page - 1) * $perPage;

            $businesses = $businesses->offset($offset)->limit($perPage)->get();

            $sector_map = array();
            $sectors = DB::table("sectors")->get();
            foreach ($sectors as $sector) {
                $sector_map[$sector->id] = $sector->name;
            }

            for ($i = 0; $i < count($businesses); $i++) {
                // CHECK IF IT RETURNS AND ARRAY
                if (is_null($businesses[$i]["sector_ids"])) {
                    $business_sectors = [];
                } else {
                    $business_sectors = explode(",", $businesses[$i]["sector_ids"]);
                }

                $sectorsStr = '';
                $sectorTotal = count($business_sectors);

                for ($j = 0; $j < $sectorTotal; $j++) {
                    // CHECK IF business' sector's ID, exists on $sector_map
                    if (isset($sector_map[$business_sectors[$j]]))
                        $sectorsStr =  $sectorsStr . $sector_map[$business_sectors[$j]]; //Retiré el id_sector

                    if ($j != ($sectorTotal - 1))
                        $sectorsStr = $sectorsStr . ',';
                }

                $businesses[$i]['sectors'] = $sectorsStr;

                $listofImages = explode(';', $businesses[$i]['business_images_string']);
                if (count($listofImages) > 0) {
                    $businesses[$i]['image'] = $listofImages[0];
                } else {
                    $businesses[$i]['image'] = '';
                }
                unset($businesses[$i]['business_images_string']);
            }

            $response = array('total' => $total, 'status' => 'success', 'page' => $page, 'items' => count($businesses));
            $response['businesses'] = $businesses;
            return response()->json($response, 200);

        } catch (Exception $error) {
            return response()->json(['error' => $error], 422);
        }
    }

    public function exportexcel (Request $request) {
        try {
            $name_excel = 'Properties';

            $businesses = Business::leftJoin('business_types', 'business_types.id', '=', 'businesses.business_type_id')
                ->leftJoin('neighborhoods', 'neighborhoods.id', '=', 'businesses.neighborhood_id')
                ->leftJoin('districts', 'districts.id', '=', 'businesses.district_id')
                ->leftJoin('municipalities', 'municipalities.id', '=', 'businesses.municipality_id')
                ->leftJoin('provinces', 'provinces.id', '=', 'municipalities.province_id');

            $businesses = $businesses->where('businesses.flag_active', true);

            if ($request->exists('municipality'))
                $businesses = $businesses->where('municipalities.id', $request->municipality);
            if ($request->exists('district'))
                $businesses = $businesses->where('districts.id', $request->district);
            if ($request->exists('neighborhood'))
                $businesses = $businesses->where('neighborhoods.id', $request->neighborhood);
            if ($request->exists('province'))
                $businesses = $businesses->where('provinces.id', $request->province);

            // Negocios & Traspasos | Viviendas — Venta | Viviendas — Alquiler | Viviendas — Obra Nueva

            if ($request->exists('collector_id')) {
                $businesses = $businesses->where('businesses.collector_id', $request->collector_id);
            } else if ($request->exists('smartlink_id')) {
                $smartlink = Smartlink::find( $request->smartlink_id );
                $smartlink_businesses = explode( ';', $smartlink->businesses );
                $businesses = $businesses->whereIn('businesses.id', $smartlink_businesses);
            } else {
                if ($request->type == 0) {
                    $name_excel = 'Businesses';
                    $businesses = $businesses->where('businesses.business_type_id', 1);
                } else if ($request->type == 1) {
                    $name_excel = 'HomeBuy';
                    $businesses = $businesses->where('businesses.business_type_id', 3);
                    $businesses = $businesses->whereNotNull('businesses.investment');
                } else if ($request->type == 2) {
                    $name_excel = 'HomeRent';
                    $businesses = $businesses->where('businesses.business_type_id', 3);
                    $businesses = $businesses->whereNotNull('businesses.rental');
                } else if ($request->type == 3) {
                    $name_excel = 'HomeNew';
                    $businesses = $businesses->where('businesses.business_type_id', 3);
                    $businesses = $businesses->where('businesses.new_or_used', "Nuevo");
                } else {
                    $name_excel = 'Businesses';
                    $businesses = $businesses->where('businesses.business_type_id', 1);
                }
            }

            if ($request->type == 0 || $request->type == 1 || $request->type == 3) {
                if ($request->exists('min_investment'))
                    $businesses = $businesses->where('businesses.investment', '>=', $request->min_investment);
                if ($request->exists('max_investment'))
                    $businesses = $businesses->where('businesses.investment', '<=', $request->max_investment);
            }
            if ($request->type == 0 || $request->type == 2 || $request->type == 3) {
                if ($request->exists('min_rental'))
                    $businesses = $businesses->where('businesses.rental', '>=', $request->min_rental);
                if ($request->exists('max_rental')) 
                    $businesses = $businesses->where('businesses.rental', '<=', $request->max_rental);
            }
            if ($request->type !== 0) {
                if ($request->exists('min_rooms'))
                    $businesses = $businesses->where('businesses.rooms', '>=', $request->min_rooms);
                if ($request->exists('max_rooms'))
                    $businesses = $businesses->where('businesses.rooms', '<=', $request->max_rooms);
                if ($request->exists('min_bathrooms'))
                    $businesses = $businesses->where('businesses.bathrooms', '>=', $request->min_bathrooms);
                if ($request->exists('max_bathrooms'))
                    $businesses = $businesses->where('businesses.bathrooms', '<=', $request->max_bathrooms);
            }

            if ($request->order_by === 'Recientes') {
                $businesses = $businesses->orderBy('businesses.source_timestamp', 'desc');
            } else if ($request->order_by === 'Precio Asc') {
                $businesses = $businesses->orderBy('businesses.investment', 'asc')->orderBy('businesses.rental', 'asc');
            } else if ($request->order_by === 'Precio Desc') {
                $businesses = $businesses->orderBy('businesses.investment', 'desc')->orderBy('businesses.rental', 'desc');
            } else {
                $businesses = $businesses->orderBy('businesses.source_timestamp', 'desc');
            }

            $businesses = $businesses->select('businesses.id', 'businesses.name', 'businesses.investment', 'businesses.rental', 'businesses.contact_name', 'businesses.contact_landline', 'businesses.source_platform', 'businesses.source_url', 'businesses.source_timestamp', 'provinces.name as province', 'municipalities.name as municipality', 'districts.name as district', 'neighborhoods.name as neighborhood', 'businesses.rooms', 'businesses.bathrooms', 'sector_ids', 'businesses.business_type_id')->leftJoinSub(function ($query) {
                $query->select('business_id', DB::raw('GROUP_CONCAT(sector_id) as sector_ids'))
                    ->from('business_sector')->groupBy('business_id');
            }, 'subquery', 'subquery.business_id', '=', 'businesses.id')->get();
            $sector_map = array();
            $sectors = DB::table("sectors")->get();
            foreach ($sectors as $sector) {
                $sector_map[$sector->id] = $sector->name;
            }
            $arrBusiness = [];

            for ($i = 0; $i < count($businesses); $i++) {
                // CHECK IF IT RETURNS AND ARRAY
                if (is_null($businesses[$i]["sector_ids"])) {
                    $business_sectors = [];
                } else {
                    $business_sectors = explode(",", $businesses[$i]["sector_ids"]);
                }

                $keepBusiness = false;
                $sectorsStr = '';
                $sectorTotal = count($business_sectors);

                for ($j = 0; $j < $sectorTotal; $j++) {
                    // CHECK IF business' sector's ID, exists on $sector_map
                    if (isset($sector_map[$business_sectors[$j]]))
                        $sectorsStr =  $sectorsStr . $sector_map[$business_sectors[$j]]; //Retiré el id_sector
                    if ($j != ($sectorTotal - 1))
                        $sectorsStr = $sectorsStr . ',';
                    if ($request->exists('sector'))
                        $keepBusiness = $keepBusiness || in_array($business_sectors[$j], explode(',', str_replace(' ', '', $request->sector)));
                }

                $businesses[$i]['sectors'] = $sectorsStr;

                if ($request->exists('sector')) {
                    if ($keepBusiness) array_push($arrBusiness, $businesses[$i]);
                } else {
                    array_push($arrBusiness, $businesses[$i]);
                }
            }

            $flattenedBusinesses = [];
            foreach ($arrBusiness as $business) {
                unset($business['id']);
                unset($business['sector_ids']);

                $business['source_url'] = '=HYPERLINK("' . $business['source_url'] . '","' . $business['source_url'] . '")';

                if ($business['business_type_id'] === 1) {
                    $business['rooms'] = '';
                    $business['bathrooms'] = '';
                } else {
                    $business['sectors'] = '';
                }

                unset($business['business_type_id']);

                $flattenedBusiness = array_values($business->toArray());
                $flattenedBusinesses[] = $flattenedBusiness;
            }

            return Excel::download(new BusinessExport($flattenedBusinesses), $name_excel . '_' . date('Y-m-d_H-i-s') . '.xlsx' );

        } catch (Exception $error) {
            return response()->json(['error' => $error], 422);
        }

    }
}