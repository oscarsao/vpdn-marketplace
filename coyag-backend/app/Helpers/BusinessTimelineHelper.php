<?php

use App\Models\Business;
use App\Models\BusinessMultimedia;
use App\Models\BusinessTimeline;
use Illuminate\Support\Facades\Auth;

function addBusinessTimeline($idBusiness, $module, $typeCrud, $idBusinessMultimedia = Null)
{
    /*
     * Esto es un timeline de las operaciones sobre un Negocio (Business)
     */

    $auxModule = "";

    switch($module) {

        case "Business":    $auxModule = "Negocio";
                                break;

        case "BusinessMultimedia": $auxModule = "Recurso Multimedia de Negocio";
                                break;

        default: $auxModule = $module;
    }

    $action = "";

    switch($typeCrud) {
        case "create":  $action = "creado";
                        break;

        case "update":  $action = "actualizado";
                        break;

        case "delete":  $action = "borrado";
                        break;

        case "associate":   $action = "asociado";
                            break;

        default:        $action = $typeCrud;
    }

    $employee = Auth::user()->employee;

    $businessTimeline = new BusinessTimeline();
    $businessTimeline->business_id = $idBusiness;
    $businessTimeline->employee_id = $employee->id;
    $businessTimeline->module = $module;
    $businessTimeline->type_crud = $typeCrud;

    $business = Business::withTrashed()->find($idBusiness);
    $departmentName = $employee->department->name;

    $msgBusinessMultimedia = "";

    if($idBusinessMultimedia == Null)
    {
        $businessTimeline->message = "$employee->name $employee->surname (ID: $employee->id) del departamento $departmentName ha $action el $auxModule $business->name (ID: $business->id)";
    }
    else
    {
        $auxBM = BusinessMultimedia::find($idBusinessMultimedia);

        $auxTxtBM = "";
        if ($auxBM->type == 'video')
        {
            $businessTimeline->business_multimedia = $auxBM->link_video;
            $auxTxtBM = "tipo $auxBM->type con el enlace $auxBM->link_video";
        }
        else
        {
            $businessTimeline->business_multimedia = $auxBM->name;
            $auxTxtBM = "$auxBM->name (ID: $auxBM->id) del tipo $auxBM->type con ruta relativa: $auxBM->path";
        }

        $businessTimeline->message = "$employee->name $employee->surname (ID: $employee->id) del departamento $departmentName ha $action el $auxModule $auxTxtBM perteneciente al Negocio $business->name (ID: $business->id)";
    }

    $businessTimeline->save();
}
