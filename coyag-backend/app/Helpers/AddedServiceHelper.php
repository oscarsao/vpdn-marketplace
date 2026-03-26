<?php

use App\Models\AddedService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

function storeAddedService($clientId, $serviceId, $typeCrud, $paymentId=null) : AddedService
{
    $addedService = new AddedService();
    $addedService->client_id = $clientId;
    $addedService->service_id = $serviceId;
    $addedService->flag_active_plan = true;

    if($addedService->save())
    {
        if($paymentId)
            $addedService->payments()->attach($paymentId);

        $employeeID = 1;

        if(isset(Auth::user()->employee->id))
            $employeeID = Auth::user()->employee->id;

        addClientTimeline($clientId, $employeeID, 'AddedService', $typeCrud, false);

        return $addedService;
    }
    else
    {
        Log::error("Error - COYAG -> No se guardó AddedService ClientId: $clientId - ServiceId: $serviceId");
        return null;
    }

}
