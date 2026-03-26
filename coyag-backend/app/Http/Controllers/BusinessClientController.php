<?php

namespace App\Http\Controllers;

use App\Models\BusinessClient;
use App\Models\BusinessType;
use App\Models\Client;
use App\Models\Province;
use App\Models\Sector;
use App\Traits\PreferenceTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BusinessClientController extends Controller
{
    use PreferenceTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$auxBC = BusinessClient::all();

        $businessClient = [];

        foreach(BusinessClient::all() as $bC)
            array_push($businessClient, $this->completeBusinessClient($bC));

        return response()->json([
            'status'            =>  'success',
            'businessClients'   =>  $businessClient,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $auxArr = $this->createOrUpdateBusinessClient($request);

        if($auxArr['errors'] == '')
            return response()->json(['status'    =>  'success'], 200);

        return response()->json($auxArr, 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BusinessClient  $businessClient
     * @return \Illuminate\Http\Response
     */
    public function show($idBusinessClient)
    {
        $businessClient;

        if(isset(Auth::user()->client->id))
            $businessClient = BusinessClient::where('client_id', Auth::user()->client->id)->first();
        else
            $businessClient = BusinessClient::find($idBusinessClient);

        if(!$businessClient)
            return response()->json(['errors'   =>  'Las Preferencias de Negocios del Cliente no existen'], 422);

        if(isset(Auth::user()->client->id))
            if($businessClient->client_id != Auth::user()->client->id)
                return response()->json(['errors'   =>  'Estas Preferencias de Negocio no le pertenece'], 422);

        return response()->json([
            'status'            =>  'success',
            'businessClient'    =>  $this->completeBusinessClient($businessClient),
        ], 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BusinessClient  $businessClient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idBusinessClient)
    {
        $businessClient;

        if(isset(Auth::user()->client->id))
            $businessClient = BusinessClient::where('client_id', Auth::user()->client->id)->first();
        else
            $businessClient = BusinessClient::find($idBusinessClient);

        if(!$businessClient)
            return response()->json(['errors'   =>  'Las Preferencias de Negocios del Cliente no existen'], 422);

        $auxArr = $this->createOrUpdateBusinessClient($request,  $businessClient);

        if($auxArr['errors'] == '')
            return response()->json(['status'    =>  'success'], 200);

        return response()->json($auxArr, 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BusinessClient  $businessClient
     * @return \Illuminate\Http\Response
     */
    public function destroy($idBusinessClient)
    {
        $businessClient;

        if(isset(Auth::user()->client->id))
            $businessClient = BusinessClient::where('client_id', Auth::user()->client->id)->first();
        else
            $businessClient = BusinessClient::find($idBusinessClient);

        if(!$businessClient)
            return response()->json(['errors'   =>  'Las Preferencias de Negocios del Cliente no existen'], 422);

        if(isset(Auth::user()->client->id))
            if($businessClient->client_id != Auth::user()->client->id)
                return response()->json(['errors'   =>  'Estas Preferencias de Negocio no le pertenecen'], 422);

        if($businessClient->delete())
            return response()->json(['status'    =>  'success'], 200);

        return response()->json(['errors'    =>  'No se pudo eliminar las Preferencias de Negocios del Cliente'], 422);
    }

    public function mine()
    {
        $businessClient = BusinessClient::where('client_id', Auth::user()->client->id)->first();

        if(!$businessClient)
            return response()->json(['errors'   =>  'Las Preferencias de Negocios del Cliente no existen'], 422);

        return response()->json([
            'status'            =>  'success',
            'businessClient'    =>  $this->completeBusinessClient($businessClient),
        ], 200);

    }

    private function completeBusinessClient(BusinessClient $businessClient) : BusinessClient
    {
        if($businessClient->provinces_list != null) {

            $provinceArr = explode(',', $businessClient->provinces_list);

            $provinces = Province::whereIn('id', $provinceArr)
                                    ->select('id', 'name')
                                    ->get();

            $businessClient['provinces'] = $provinces;
        }

        if($businessClient->sectors_list != null) {

            $sectorArr = explode(',', $businessClient->sectors_list);

            $sectors = Sector::whereIn('id', $sectorArr)
                                    ->select('id', 'name')
                                    ->get();

            $businessClient->sectors = $sectors;

        }

        if($businessClient->business_types_list != null) {

            $businessTypeArr = explode(',', $businessClient->business_types_list);

            $businessTypes = Sector::whereIn('id', $businessTypeArr)
                                    ->select('id', 'name')
                                    ->get();

            $businessClient->business_types = $businessTypes;
        }

        return $businessClient;
    }


    private function createOrUpdateBusinessClient(Request $request, BusinessClient $auxBusinessClient = null)
    {
        $validator = Validator::make($request->all(),
        [
            'provinces_list'        =>  'string|max:255',
            'min_investment'        =>  'numeric|between:-1,100000000',
            'max_investment'        =>  'numeric|between:-1,100000000',
            'sectors_list'          =>  'string|max:255',
            'business_types_list'   =>  'string|max:255',
            'prefered_operation'    =>  'string|max:255',
            'current_businesses'    =>  'string|max:255',
            'estimated_date'        =>  'date'
        ],
        [
            'provinces_list.string'         =>  'La Lista de Provincias debe ser un String',
            'provinces_list.max'            =>  'La Lista de Provincias no debe exceder los 255 caracteres',
            'min_investment.numeric'        =>  'El Mínimo de Inversión debe ser numérico',
            'min_investment.between'        =>  'El valor de Mínimo de Inversión debe ser entre -1 y 100000000',
            'max_investment.numeric'        =>  'El Máximo de Inversión debe ser numérico',
            'max_investment.between'        =>  'El valor de Máximo de Inversión debe ser entre -1 y 100000000',
            'sectors_list.string'           =>  'La Lista de Sectores debe ser un String',
            'sectors_list.max'              =>  'La Lista de Sectores no debe exceder los 255 caracteres',
            'business_types_list.string'    =>  'La Lista de Tipos de Negocios debe ser un String',
            'business_types_list.max'       =>  'La Lista de Tipos de Negocios no debe exceder los 255 caracteres',
            'prefered_operation.string'     =>  'El metodo de operacion preferido debe ser un String',
            'prefered_operation.max'        =>  'El metodo de operacion preferido no debe exceder los 255 caracteres',
            'current_businesses.string'     =>  'Los Negocios poseidos actualmente debe ser un String',
            'current_businesses.max'        =>  'Los Negocios poseidos actualmente no deben exceder los 255 caracteres',
            'estimated_date.date'           =>  'La Fecha estimada de inversion tiene un formato inválido',
        ]);

        if($validator->fails())
            return ['errors' => $validator->errors()];

        $clientId;

        if(isset(Auth::user()->client->id)) {
            $clientId = Auth::user()->client->id;

            if($auxBusinessClient != null)
                if($auxBusinessClient->client_id != $clientId)
                    return ['errors' => 'Estas Preferencias de Negocio no le pertenecen'];
        }
        else {
            if($auxBusinessClient == null)
            {
                $validator = Validator::make($request->all(),
                [
                    'client_id'                     =>  'required|exists:clients,id',
                ],
                [
                    'client_id.required'            =>  'El ID del Cliente es Requerido',
                    'client_id.exists'              =>  'El Cliente no existe en el Sistema',
                ]);

                if($validator->fails())
                    return ['errors' => $validator->errors()];

                $clientId = $request->client_id;
            }
            else
                $clientId = $auxBusinessClient->client_id;

        }

        $role = getRoleOfClientIndicatingTheTypeOfService(Client::find($clientId), 'Plan');
        if($role->slug == 'cliente.registrado')
            return ['errors' => 'El Cliente no tiene un plan de VideoPortal asociado'];

        $businessClient;

        if(BusinessClient::where('client_id', $clientId)->count() == 0)
            $businessClient = new BusinessClient();
        else {
            if($auxBusinessClient != null)
                $businessClient = $auxBusinessClient;
            else
                $businessClient = BusinessClient::where('client_id', $clientId)->first();
        }

        $businessClient->client_id = $clientId;

        if($request->exists('estimated_date'))
            $businessClient->estimated_date = $request->estimated_date;
        if($request->exists('prefered_operation'))
            $businessClient->prefered_operation = $request->prefered_operation;
        if($request->exists('current_businesses'))
            $businessClient->current_businesses = $request->current_businesses;

        if($request->exists('provinces_list'))
            if($request->provinces_list == 'null')
                $businessClient->provinces_list = null;
            else
            {
                $provinceArr = explode(',', $request->provinces_list);

                for($i = 0; $i < count($provinceArr); $i++) {
                    $province = Province::find($provinceArr[$i]);
                    if(!$province)
                        return ['errors' => "La Provincia con ID ${provinceArr[$i]} no existe" ];
                }


                $businessClient->provinces_list = $request->provinces_list;
            }

        if($request->exists('min_investment'))
            if($request->min_investment == -1)
                $businessClient->min_investment = null;
            else
                $businessClient->min_investment = $request->min_investment;

        if($request->exists('max_investment'))
            if($request->max_investment == -1)
                $businessClient->max_investment = null;
            else
                $businessClient->max_investment = $request->max_investment;

        if($request->exists('sectors_list'))
            if($request->sectors_list == 'null')
                $businessClient->sectors_list = null;
            else
            {
                $sectorArr = explode(',', $request->sectors_list);

                for($i = 0; $i < count($sectorArr); $i++) {
                    $sector = Sector::find($sectorArr[$i]);
                    if(!$sector)
                        return ['errors' => "El Sector con ID ${sectorArr[$i]} no existe" ];
                }

                $businessClient->sectors_list = $request->sectors_list;
            }

        if($request->exists('business_types_list'))
            if($request->business_types_list == 'null')
                $businessClient->business_types_list = null;
            else {

                $businessTypeArr = explode(',', $request->business_types_list);

                for($i = 0; $i < count($businessTypeArr); $i++) {
                    $businessType = BusinessType::find($businessTypeArr[$i]);
                    if(!$businessType)
                        return ['errors' => "El Tipo de Negocio con ID ${businessTypeArr[$i]} no existe" ];
                }

                $businessClient->business_types_list = $request->business_types_list;
            }

        if(($businessClient->min_investment > $businessClient->max_investment) &&
            ($businessClient->min_investment != null) &&
            ($businessClient->max_investment != null))
            return ['errors'    =>  'El Mínimo de Inversión no puede ser mayor a El Máximo de Inversión'];

        if($businessClient->save())
        {
            $this->updateBusinessListForAClient($clientId);
            return ['errors' => ''];
        }

        return ['errors'    =>  'No se pudo guardar las Preferencias de Negocios del Cliente'];
    }

}
