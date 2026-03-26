<?php

use App\Models\AddedService;
use App\Models\VisaStep;
use App\Models\VisaStepType;
use App\Models\VisaType;

function automaticallyGenerateVisaStep(AddedService $addedService, $withoutVisaType = false) {

    VisaStep::where('client_id', $addedService->client_id)
                ->where('status', 'Sin Iniciar')
                ->delete();

    if(!$withoutVisaType) {

        $visaType = VisaType::find($addedService->visa_type_id);

        for($i = 0; $i < count($visaType->visaStepType); $i++)
        {
            $visaStep = new VisaStep();

            $visaStep->client_id = $addedService->client_id;
            $visaStep->added_service_id = $addedService->id;

            $visaStep->name = $visaType->visaStepType[$i]->name;
            $visaStep->client_description = $visaType->visaStepType[$i]->client_description;
            $visaStep->advisor_description = $visaType->visaStepType[$i]->advisor_description;

            $visaStep->number_client = $visaType->visaStepType[$i]->pivot->number_client;
            $visaStep->number_advisor = $visaType->visaStepType[$i]->pivot->number_advisor;

            $visaStep->save();
        }
    }

}


