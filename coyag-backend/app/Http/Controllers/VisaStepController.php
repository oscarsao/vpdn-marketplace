<?php

namespace App\Http\Controllers;

use App\Models\AddedService;
use App\Models\Client;
use App\Models\VisaStep;
use App\Models\VisaStepType;
use App\Traits\EmailManagementTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class VisaStepController extends Controller
{
    use EmailManagementTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $visaSteps = VisaStep::leftJoin('added_services', 'added_services.id', '=', 'visa_steps.added_service_id')
                                ->leftJoin('clients', 'clients.id', '=', 'visa_steps.client_id');

        if($request->exists('added_service_id'))
            $visaSteps = $visaSteps->where('visa_steps.added_service_id', $request->added_service_id);

        if(isset(Auth::user()->employee->id))
        {
            if($request->exists('client_id'))
                $visaSteps = $visaSteps->where('visa_steps.client_id', $request->client_id);
        }
        else
        {
            $visaSteps = $visaSteps->where('visa_steps.client_id', Auth::user()->client->id);
        }

        if($request->exists('number_client'))
            $visaSteps = $visaSteps->where('visa_steps.number_client', $request->number_client);

        if($request->exists('number_advisor'))
            $visaSteps = $visaSteps->where('visa_steps.number_advisor', $request->number_advisor);

        if(isset(Auth::user()->client->id))
        {
            $visaSteps = $visaSteps->select('visa_steps.id as id_visa_step', 'visa_steps.number_client as number_client_visa_step', 'visa_steps.name as name_visa_step', 'visa_steps.client_description as client_description_visa_step', 'visa_steps.status as status_visa_step', 'visa_steps.date_completed as date_completed_visa_step', 'visa_steps.created_at as created_at_visa_step', 'visa_steps.updated_at as updated_at_visa_step', 'visa_steps.added_service_id as id_added_service', 'visa_steps.client_id as id_client', DB::raw('CONCAT(clients.names, " ", clients.surnames) as full_name_client'))
                                    ->orderBy('visa_steps.number_client', 'asc');
        }
        else
        {
            $visaSteps = $visaSteps->select('visa_steps.id as id_visa_step', 'visa_steps.number_client as number_client_visa_step', 'visa_steps.number_advisor as number_advisor_visa_step', 'visa_steps.name as name_visa_step', 'visa_steps.client_description as client_description_visa_step', 'visa_steps.advisor_description as advisor_description_visa_step', 'visa_steps.date_completed as date_completed_visa_step', 'visa_steps.status as status_visa_step', 'visa_steps.created_at as created_at_visa_step', 'visa_steps.updated_at as updated_at_visa_step', 'visa_steps.added_service_id as id_added_service', 'visa_steps.client_id as id_client', DB::raw('CONCAT(clients.names, " ", clients.surnames) as full_name_client'))
                                    ->orderBy('visa_steps.number_advisor', 'asc');
        }


        $visaSteps = $visaSteps->get();


        return response()->json([
            'status'        => 'success',
            'visaSteps'     =>  $visaSteps
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
        $validator = Validator::make($request->all(),
        [
            'added_service_id'      =>  'required|exists:added_services,id',
            'name'                  =>  'required|max:128',
            'number_client'         =>  'numeric',
            'number_advisor'        =>  'numeric',
        ],
        [
            'added_service_id.required'     =>  'El ID de AddedService es Requerido',
            'added_service_id.exists'       =>  'El ID de AddedService no existe en el Sistema',
            'name.required'                 =>  'El Nombre es Requerido',
            'name.max'                      =>  'El Nombre solo puede tener hasta 128 caracteres',
            'number_client.numeric'         =>  'El Número de Paso del cliente debe ser numérico',
            'number_advisor.numeric'        =>  'El Número de Paso del Asesor debe ser numérico',
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        $addedService = AddedService::find($request->added_service_id);

        if($addedService->flag_active_plan == false)
            return response()->json(['errors'   => 'El AddedService no se encuentra activo'], 422);

        if($addedService->service->type != 'Inmigration')
            return response()->json(['errors'   => 'El AddedService no está asociado a un Servicio de Extranjería'], 422);


        $visaStep = new VisaStep();

        $visaStep->client_id = $addedService->client_id;
        $visaStep->added_service_id = $addedService->id;
        $visaStep->name = $request->name;

        if($request->exists('client_description'))
            $visaStep->client_description = $request->client_description;

        if($request->exists('advisor_description'))
            $visaStep->advisor_description = $request->advisor_description;

        if($request->exists('number_client'))
        {
            if(VisaStep::where('client_id', $addedService->client_id)->where('number_client', $request->number_client)->count() > 0)
                return response()->json(['errors'   => 'El Cliente ya posee un Paso con dicho Número de Cliente'], 422);

            $visaStep->number_client = $request->number_client;
        }

        if($request->exists('number_advisor'))
        {
            if(VisaStep::where('client_id', $addedService->client_id)->where('number_advisor', $request->number_advisor)->count() > 0)
                return response()->json(['errors'   => 'El Cliente ya posee un Paso con dicho Número de Asesor'], 422);

            $visaStep->number_advisor = $request->number_advisor;
        }

        if($visaStep->save())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors'   =>  'No se pudo Crear el Paso'], 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VisaStep  $visaStep
     * @return \Illuminate\Http\Response
     */
    public function show($idVisaStep)
    {
        $visaStep = VisaStep::find($idVisaStep);

        if(!$visaStep)
            return response()->json(['errors'   => 'El Paso no existe'], 422);

        $visaStep = VisaStep::leftJoin('added_services', 'added_services.id', '=', 'visa_steps.added_service_id')
                                ->leftJoin('clients', 'clients.id', '=', 'visa_steps.client_id')
                                ->where('visa_steps.id', $idVisaStep);

        if(isset(Auth::user()->client->id))
        {
            $visaStep = $visaStep->where('visa_steps.client_id', Auth::user()->client->id)
                                            ->select('visa_steps.id as id_visa_step', 'visa_steps.number_client as number_client_visa_step', 'visa_steps.name as name_visa_step', 'visa_steps.client_description as client_description_visa_step', 'visa_steps.status as status_visa_step', 'visa_steps.date_completed as date_completed_visa_step', 'visa_steps.created_at as created_at_visa_step', 'visa_steps.updated_at as updated_at_visa_step', 'visa_steps.added_service_id as id_added_service', 'visa_steps.client_id as id_client', DB::raw('CONCAT(clients.names, " ", clients.surnames) as full_name_client'));
        }
        else
        {
            $visaStep = $visaStep->select('visa_steps.id as id_visa_step', 'visa_steps.number_client as number_client_visa_step', 'visa_steps.number_advisor as number_advisor_visa_step', 'visa_steps.name as name_visa_step', 'visa_steps.client_description as client_description_visa_step', 'visa_steps.advisor_description as advisor_description_visa_step', 'visa_steps.date_completed as date_completed_visa_step', 'visa_steps.status as status_visa_step', 'visa_steps.created_at as created_at_visa_step', 'visa_steps.updated_at as updated_at_visa_step', 'visa_steps.added_service_id as id_added_service', 'visa_steps.client_id as id_client', DB::raw('CONCAT(clients.names, " ", clients.surnames) as full_name_client'));
        }


        $visaStep = $visaStep->first();


        return response()->json([
            'status'        => 'success',
            'visaStep'     =>  $visaStep
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\VisaStep  $visaStep
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idVisaStep)
    {
        $visaStep = VisaStep::find($idVisaStep);

        if(!$visaStep)
            return response()->json(['errors'   => 'El Paso no existe'], 422);

        $validator = Validator::make($request->all(),
        [
            'name'                  =>  'max:128',
            'number_client'         =>  'numeric',
            'number_advisor'        =>  'numeric',
        ],
        [
            'name.max'                      =>  'El Nombre solo puede tener hasta 128 caracteres',
            'number_client.numeric'         =>  'El Número de Paso del Cliente debe ser numérico',
            'number_advisor.numeric'        =>  'El Número de Paso del Asesor debe ser numérico',
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);


        if($request->exists('name'))
            $visaStep->name = $request->name;


        if($request->exists('client_description'))
            $visaStep->client_description = $request->client_description;

        if($request->exists('advisor_description'))
            $visaStep->advisor_description = $request->advisor_description;

        if($request->exists('number_client'))
        {
            if(VisaStep::where('client_id', $visaStep->client_id)->where('number_client', $request->number_client)->count() > 0)
                return response()->json(['errors'   => 'El Cliente ya posee un Paso con dicho Número de Cliente'], 422);

            $visaStep->number_client = $request->number_client;
        }

        if($request->exists('number_advisor'))
        {
            if(VisaStep::where('client_id', $visaStep->client_id)->where('number_advisor', $request->number_advisor)->count() > 0)
                return response()->json(['errors'   => 'El Cliente ya posee un Paso con dicho Número de Asesor'], 422);

            $visaStep->number_advisor = $request->number_advisor;
        }

        if($visaStep->save())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors'   =>  'No se pudo Actualizar el Paso'], 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VisaStep  $visaStep
     * @return \Illuminate\Http\Response
     */
    public function destroy($idVisaStep)
    {
        $visaStep = VisaStep::find($idVisaStep);

        if(!$visaStep)
            return response()->json(['errors'   => 'El Paso no existe'], 422);

        if($visaStep->delete())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors'   =>  'No se pudo Eliminar el Paso'], 422);
    }

    public function updateStatus(Request $request, $idVisaStep)
    {
        $visaStep = VisaStep::find($idVisaStep);

        if(!$visaStep)
            return response()->json(['errors'   => 'El Paso no existe'], 422);

        $validator = Validator($request->all(),
        [
            'status'            =>  'required|in:"Sin Iniciar","En Proceso","Completado"'
        ],
        [
            'status.required'   =>  'El Estatus es requerido',
            'status.in'         =>  'Los valores que acepta Status son: Sin Iniciar, En Proceso y Completado'
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        $visaStep->status = $request->status;

        if($request->status == 'Completado')
            $visaStep->date_completed = Carbon::now()->toDateTimeString();
        else
            $visaStep->date_completed = null;

        if($visaStep->save())
        {
            if(in_array($visaStep->status, ['Completado', 'En Proceso']) && $visaStep->number_client != null)
                $this->visaStepUpdate($visaStep->client);

            return response()->json(['status' => 'success'], 200);
        }

        return response()->json(['errors'   =>  'No se pudo actualizar el Estatus del Paso'], 422);

    }

    public function storeWithType(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'added_service_id'      =>  'required|exists:added_services,id',
            'number_client'         =>  'numeric',
            'number_advisor'        =>  'numeric',
            'visa_step_type_id'     =>  'required|exists:visa_step_types,id'
        ],
        [
            'added_service_id.required'     =>  'El ID de AddedService es Requerido',
            'added_service_id.exists'       =>  'El ID de AddedService no existe en el Sistema',
            'number_client.numeric'         =>  'El Número de Paso del cliente debe ser numérico',
            'number_advisor.numeric'        =>  'El Número de Paso del Asesor debe ser numérico',
            'visa_step_type_id.required'    =>  'El ID de Tipo de Paso es requerido',
            'visa_step_type_id.exists'      =>  'El Tipo de Paso no existe en el Sistema',
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        $addedService = AddedService::find($request->added_service_id);

        if($addedService->flag_active_plan == false)
            return response()->json(['errors'   => 'El AddedService no se encuentra activo'], 422);

        if($addedService->service->type != 'Inmigration')
            return response()->json(['errors'   => 'El AddedService no está asociado a un Servicio de Extranjería'], 422);


        $visaStepType = VisaStepType::find($request->visa_step_type_id);

        $visaStep = new VisaStep();

        $visaStep->client_id = $addedService->client_id;
        $visaStep->added_service_id = $addedService->id;

        $visaStep->name = $visaStepType->name;
        $visaStep->client_description = $visaStepType->client_description;
        $visaStep->advisor_description = $visaStepType->advisor_description;

        if($request->exists('number_client'))
        {
            if(VisaStep::where('client_id', $addedService->client_id)->where('number_client', $request->number_client)->count() > 0)
                return response()->json(['errors'   => 'El Cliente ya posee un Paso con dicho Número de Cliente'], 422);

            $visaStep->number_client = $request->number_client;
        }

        if($request->exists('number_advisor'))
        {
            if(VisaStep::where('client_id', $addedService->client_id)->where('number_advisor', $request->number_advisor)->count() > 0)
                return response()->json(['errors'   => 'El Cliente ya posee un Paso con dicho Número de Asesor'], 422);

            $visaStep->number_advisor = $request->number_advisor;
        }

        if($visaStep->save())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors'   =>  'No se pudo Crear el Paso'], 422);
    }
}
