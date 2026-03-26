<?php

use App\Models\AddedService;
use App\Models\VisaRequirement;
use App\Models\VisaType;

function automaticallyGenerateVisaRequirement(AddedService $addedService, $withoutVisaType = false)
{

    VisaRequirement::where('client_id', $addedService->client_id)
                    ->where('status', 'Sin Cargar')
                    ->delete();


    if(!$withoutVisaType) {

        $visaType = VisaType::find($addedService->visa_type_id);

        for($i = 0; $i < count($visaType->visaDocumentType); $i++)
        {
            $visaRequirement = new VisaRequirement();
            $visaRequirement->visa_document_type_id = $visaType->visaDocumentType[$i]->id;
            $visaRequirement->added_service_id = $addedService->id;
            $visaRequirement->client_id = $addedService->client_id;
            $visaRequirement->save();
        }
    }

}
