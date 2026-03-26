<?php

use App\Models\VisaDocumentType;
use App\Models\VisaType;

function createVisaDocumentType($nameVisaDocumentType, $visaTypes = null) : int
{
    $visaDocumentType = new VisaDocumentType();
    $visaDocumentType->name = $nameVisaDocumentType;

    if($visaDocumentType->save())
    {
        if($visaTypes != null)
        {
            $auxVisaTypes = explode(',', $visaTypes);
            for($i = 0; $i < count($auxVisaTypes); $i++)
            {
                $visaDocumentType->visaType()->attach($auxVisaTypes[$i]);
            }
        }

        return $visaDocumentType->id;
    }
    else
        return 0;
}

function checkVisaType($visaTypes) : array
{
    $auxReturn = ['status' => 'OK'];

    $auxVisaTypes = explode(',', $visaTypes);
    for($i = 0; $i < count($auxVisaTypes); $i++)
    {
        if(VisaType::where('id', $auxVisaTypes[$i])->count() == 0)
            $auxReturn = ['status' => 'FAIL', 'errors' => "El Tipo de Visa con ID $auxVisaTypes[$i] no existe"];
    }

    return $auxReturn;
}
