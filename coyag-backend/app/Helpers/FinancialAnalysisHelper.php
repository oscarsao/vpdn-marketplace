<?php

use App\Models\AddedService;
use App\Models\FinancialAnalysis;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

function storeFinancialAnalysis(AddedService $addedService, bool $flagChangePlan = false) : bool
{

    if($flagChangePlan)
    {
        changeFlagActiveInFA($addedService->client_id);

        //Verifica si el plan actual tiene análisis financieros precargados
        if($addedService->service->financial_analysis_available == 0)
            return false;
    }


    $financialAnalysis = new FinancialAnalysis();
    //Si es un plan esto apuntará al plan. Sino al AddOn
    $financialAnalysis->added_service_id  = $addedService->id;
    $financialAnalysis->accomplished = 0;
    if($flagChangePlan) $financialAnalysis->flag_active = true;

    if(!$financialAnalysis->save())
    {
        Log::error('Error - COYAG -> No se pudo guardar el Análisis financiero con el ID AddedService: ' . $addedService->id);
    }

    addClientTimeline($addedService->client_id, Auth::user()->employee->id, 'Financial Analysis', 'create', false);
    notificationAnalisisFinancieroOtorgado($addedService->client_id,  $financialAnalysis);

    return true;
}

function changeFlagActiveInFA($clientId)
{
    /* Comprueba si el plan anterior (realmente planes anteriores) tenía Análisis Financieros Ilimitados
    * Si es así el flag_active se coloca en false porque ya no estarán disponibles para el cliente
    */

    $financialAnalyses = FinancialAnalysis::leftJoin('added_services', 'added_services.id', '=', 'financial_analyses.added_service_id')
                                            ->leftJoin('services', 'services.id', '=', 'added_services.service_id')
                                            ->where('added_services.client_id', $clientId)
                                            ->where('added_services.flag_active_plan', false)
                                            ->where('services.type', 'Plan')
                                            ->select('financial_analyses.id as id')
                                            ->get();

    for($i = 0; $i < count($financialAnalyses); $i++) {
        $financialAnalysis = FinancialAnalysis::find($financialAnalyses[$i]['id']);
        $financialAnalysis->flag_active = false;
        $financialAnalysis->save();
    }
}
