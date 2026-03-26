<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientTimeline;
use App\Services\ClientTimelineService;
use Illuminate\Http\Request;

class ClientTimelineController extends Controller
{
    private $clientTimelineService;

    public function __construct() {
        $this->clientTimelineService = new ClientTimelineService();
    }

    public function getBrowsingHistory(Request $request, $idClient)
    {
        $data = $this->clientTimelineService->getBrowsingHistory($request, $idClient);

        if($data->status == 'success')
            return response()->json([
                'status'            => $data->status,
                'browsing_history'  => $data->browsingHistory
            ], $data->code);
        else
            return response()->json([
                'errors'   =>  $data->errors
            ], $data->code);
    }

    public function getSearchHistory(Request $request, $idClient)
    {
        $data = $this->clientTimelineService->getSearchHistory($request, $idClient);

        if($data->status == 'success')
            return response()->json([
                'status'            => $data->status,
                'search_history'    => $data->searchHistory
            ], $data->code);
        else
            return response()->json([
                'errors'   =>  $data->errors
            ], $data->code);
    }

    public function getAuthHistory(Request $request, $idClient)
    {
        $data = $this->clientTimelineService->getAuthHistory($request, $idClient);

        if($data->status == 'success')
            return response()->json([
                'status'        => $data->status,
                'auth_history'  => $data->authHistory
            ], $data->code);
        else
            return response()->json([
                'errors'   =>  $data->errors
            ], $data->code);
    }
}
