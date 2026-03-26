<?php

namespace App\Http\Controllers;

use App\Models\AddedService;
use App\Models\Client;
use App\Models\FinancialAnalysis;
use App\Models\Payment;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FinancialAnalysisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'flag_active'           =>  'boolean'
        ],
        [
            'flag_active.boolean'   =>  'El campo flag_active debe tener los valores de 1 o 0'
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);


        $financialAnalyses = FinancialAnalysis::leftJoin('added_services', 'financial_analyses.added_service_id', '=', 'added_services.id')
                                                ->leftJoin('services', 'added_services.service_id', '=', 'services.id');


        if($request->exists('flag_active'))
            $financialAnalyses = $financialAnalyses->where('financial_analyses.flag_active', $request->flag_active);


        $financialAnalyses = $financialAnalyses->select('financial_analyses.id as id_financial_analysis', 'added_services.client_id as id_client', 'services.financial_analysis_available as hired_financial_analysis', 'financial_analyses.accomplished as accomplished_financial_analysis', DB::raw("CASE WHEN services.financial_analysis_available = -1 THEN 'inf' ELSE services.financial_analysis_available - financial_analyses.accomplished END as available_financial_analysis"), 'financial_analyses.flag_active as flag_active_financial_analysis', 'financial_analyses.added_service_id as id_added_service', 'financial_analyses.created_at as created_at_financial_analysis', 'financial_analyses.updated_at as updated_at_financial_analysis', 'services.id as id_service', 'services.name as name_service', 'services.description as description_service')
                                                ->get();


        return response()->json([
            'status'    =>  'success',
            'financialAnalyses'    =>  $financialAnalyses
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'client_id'     =>  'required|numeric|exists:clients,id',
            'service_id'    =>  'required|numeric|exists:services,id',
            'payment_id'                =>  'numeric|exists:payments,id'
        ],
        [
            'client_id.required'    =>  'El ID del Cliente es Requerido',
            'client_id.numeric'     =>  'El ID del Cliente debe ser Numérico',
            'client_id.exists'      =>  'El Cliente No Existe',
            'service_id.required'   =>  'El ID del Cliente es Requerido',
            'service_id.numeric'    =>  'El ID del Cliente debe ser Numérico',
            'service_id.exists'     =>  'El Cliente No Existe',
            'payment_id.numeric'        =>  'El ID del Pago debe ser numérico',
            'payment_id.exists'         =>  'El Pago No Existe en la BD',
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $idPayment = null;
        if($request->exists('payment_id'))
        {
            $idPayment = $request->payment_id;
            $payment = Payment::find($idPayment);
            if($payment->client_id != $request->client_id)
                return response()->json(['errors'   =>  'El Pago no pertence a este Cliente'], 422);
        }

        $service = Service::find($request->service_id);
        $client = Client::find($request->client_id);

        if($service->type == 'Plan')
            return response()->json(['errors'   =>  'El Servicio enviado es del tipo Plan'], 422);

        if($service->type == 'Inmigration')
            return response()->json(['errors'   =>  'El Servicio enviado es del tipo Extranjería'], 422);

        if($service->flag_active == false)
            return response()->json(['errors'   =>  'El Servicio enviado no se encuentra activo'], 422);

        if(!checkIfTheUserHasTheRole($service->roles_slug, $client->user->getRoles(), 'slug'))
            return response()->json(['errors'   =>  'El Servicio seleccionado no está disponible para el rol del cliente'], 422);

        $addedService = storeAddedService($client->id, $service->id, 'create', $idPayment);
        if(is_null($addedService))
            return response()->json(['errors'    =>  'No se pudo crear el Análisis Financiero'], 422);

        if(storeFinancialAnalysis($addedService))
            return response()->json(['status'    =>  'success'], 200);

        $addedService->delete();

        return response()->json(['errors'    =>  'No se pudo crear el Análisis Financiero'], 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FinancialAnalysis  $financialAnalysis
     * @return \Illuminate\Http\Response
     */
    public function show($idFinancialAnalysis)
    {
        if(FinancialAnalysis::where('id', $idFinancialAnalysis)->count() > 0)
        {
            $financialAnalysis = FinancialAnalysis::leftJoin('added_services', 'financial_analyses.added_service_id', '=', 'added_services.id')
                                                    ->leftJoin('services', 'added_services.service_id', '=', 'services.id')
                                                    ->where('financial_analyses.id', $idFinancialAnalysis)
                                                    ->select('financial_analyses.id as id_financial_analysis', 'added_services.client_id as id_client', 'services.financial_analysis_available as hired_financial_analysis', 'financial_analyses.accomplished as accomplished_financial_analysis', DB::raw("CASE WHEN services.financial_analysis_available = -1 THEN 'inf' ELSE services.financial_analysis_available - financial_analyses.accomplished END as available_financial_analysis"), 'financial_analyses.flag_active as flag_active_financial_analysis', 'financial_analyses.added_service_id as id_added_service', 'financial_analyses.created_at as created_at_financial_analysis', 'financial_analyses.updated_at as updated_at_financial_analysis', 'services.id as id_service', 'services.name as name_service', 'services.description as description_service')
                                                    ->first();

            return response()->json([
                'status'    =>  'success',
                'financialAnalysis'    =>  $financialAnalysis
            ]);
        }

        return response()->json(['errors' => 'No existe el cliente'], 422);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FinancialAnalysis  $financialAnalysis
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FinancialAnalysis $financialAnalysis)
    {
        // En esta etapa no está disponible
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FinancialAnalysis  $financialAnalysis
     * @return \Illuminate\Http\Response
     */
    public function destroy($idFinancialAnalysis)
    {
        $financialAnalysis = FinancialAnalysis::find($idFinancialAnalysis);

        if($financialAnalysis)
        {
            if($financialAnalysis->addedService->service->type == 'Plan')
                return response()->json(['errors' => 'No se puede borrar el Análisis Financiero porque pertenece a un Plan'], 422);

            $addedService = AddedService::find($financialAnalysis->added_service_id);

            $financialAnalysis->delete();
            $addedService->delete();

            return response()->json(['status' => 'sucess'], 200);
        }

        return response()->json(['errors' => 'No existe el cliente'], 422);
    }

    public function addOne($idFinancialAnalysis)
    {
        if(FinancialAnalysis::where('id', $idFinancialAnalysis)->count() > 0)
        {
            $financialAnalysis = FinancialAnalysis::find($idFinancialAnalysis);

            if($financialAnalysis->flag_active == false)
                return response()->json(['errors' => 'Ya se ha completado todos los análisis financieros'], 422);

            $financialAnalysis->accomplished = $financialAnalysis->accomplished + 1;

            $available = $financialAnalysis->addedService->service->financial_analysis_available - $financialAnalysis->accomplished;

            if($available == 0 && $financialAnalysis->addedService->service->available != -1)
                $financialAnalysis->flag_active = false;

            if($financialAnalysis->save())
                return response()->json(['status' => 'sucess'], 200);

            return response()->json(['errors' => 'No se pudo agregar la cuenta de un Análisis Financiero realizado'], 422);
        }

        return response()->json(['errors' => 'No existe el Análisis Financiero'], 422);
    }

    public function financialAnalysesSummaryAboutAClient($idClient)
    {
        if(Client::where('id', $idClient)->count() > 0)
        {
            $financialAnalyses = FinancialAnalysis::leftJoin('added_services', 'financial_analyses.added_service_id', '=', 'added_services.id')
                                                    ->leftJoin('services', 'added_services.service_id', '=', 'services.id')
                                                    ->where('added_services.client_id', $idClient)
                                                    ->select('financial_analyses.id as id_financial_analysis', 'added_services.client_id as id_client', 'services.financial_analysis_available as hired_financial_analysis', 'financial_analyses.accomplished as accomplished_financial_analysis', DB::raw("CASE WHEN services.financial_analysis_available = -1 THEN 'inf' ELSE services.financial_analysis_available - financial_analyses.accomplished END as available_financial_analysis"), 'financial_analyses.flag_active as flag_active_financial_analysis', 'financial_analyses.added_service_id as id_added_service', 'financial_analyses.created_at as created_at_financial_analysis', 'financial_analyses.updated_at as updated_at_financial_analysis', 'services.id as id_service', 'services.name as name_service', 'services.description as description_service')
                                                    ->get();

            return response()->json([
                'status'    =>  'success',
                'financialAnalyses'    =>  $financialAnalyses
            ]);
        }

        return response()->json(['errors' => 'No existe el cliente'], 422);
    }

}
