<?php

namespace App\Http\Controllers;

use App\Exports\AuthenticatedClientsByPlanExport;
use App\Exports\ClientsByPlanExport;
use App\Exports\MostVisitedBusinessesExport;
use App\Services\StatisticService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StatisticsController extends Controller
{

    private StatisticService $statisticService;

    public function __construct()
    {
        $this->statisticService = new StatisticService();
    }

    public function totalClientsByPlan()
    {
        return response()->json([
            'status'        =>  'success',
            'statistics'    =>  $this->statisticService->totalClientsByPlan()
        ], 200);

    }

    public function getListClientsByPlan(Request $request)
    {
        /**
         * Solo aplica para Planes de Videoportal
         */

        $data =  $this->statisticService->getListClientsByPlan($request);

        if($data->status == 'success')
            return response()->json([
                'status'             => $data->status,
                'clients_by_plan'    => $data->clients
            ], $data->code);
        else
            return response()->json([
                'errors'   =>  $data->errors
            ], $data->code);

    }

    public function getListClientsByPlanExcel(Request $request)
    {
        /**
         * Solo aplica para Planes de Videoportal
         */

        $data =  $this->statisticService->getListClientsByPlan($request);

        if($data->status == 'success')
            return Excel::download(new ClientsByPlanExport($data->clients), 'clientes.xlsx');
        else
            return response()->json([
                'errors'   =>  $data->errors
            ], $data->code);

    }

    public function getMostVisitedBusinesses(Request $request)
    {
        $data =  $this->statisticService->getMostVisitedBusinesses($request);

        if($data->status == 'success')
            return response()->json([
                'status'                    => $data->status,
                'most_visited_businesses'   => $data->businesses
            ], $data->code);
        else
            return response()->json([
                'errors'   =>  $data->errors
            ], $data->code);
    }

    public function getMostVisitedBusinessesExcel(Request $request)
    {
        $data =  $this->statisticService->getMostVisitedBusinesses($request);

        if($data->status == 'success')
            return Excel::download(new MostVisitedBusinessesExport($data->businesses), 'negocios_más_visitados.xlsx');
        else
            return response()->json([
                'errors'   =>  $data->errors
            ], $data->code);
    }

    public function getTotalAuthenticatedClientsByPlan(Request $request)
    {
        /**
         * Solo aplica para Planes de Videoportal
         */

         $data =  $this->statisticService->getTotalAuthenticatedClientsByPlan($request);

        if($data->status == 'success')
            return response()->json([
                'status'                => $data->status,
                'auth_clients_by_plan'  => $data->clients
            ], $data->code);
        else
            return response()->json([
                'errors'   =>  $data->errors
            ], $data->code);

    }

    public function getTotalAuthenticatedClientsByPlanExcel(Request $request)
    {
        $data =  $this->statisticService->getTotalAuthenticatedClientsByPlan($request);

        if($data->status == 'success')
            return Excel::download(new AuthenticatedClientsByPlanExport($data->clients), 'autenticaciones_clientes.xlsx');
        else
            return response()->json([
                'errors'   =>  $data->errors
            ], $data->code);
    }
}
