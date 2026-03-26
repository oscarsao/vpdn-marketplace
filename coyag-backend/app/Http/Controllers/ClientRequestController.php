<?php

namespace App\Http\Controllers;

use App\Models\ClientRequest;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClientRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $clientRequests = ClientRequest::leftJoin('clients', 'client_requests.client_id', '=', 'clients.id')
                                        ->leftJoin('employees', 'client_requests.employee_id', '=', 'employees.id')
                                        ->leftJoin('services', 'client_requests.service_id', 'services.id');

        if($request->exists('client_id'))
            $clientRequests = $clientRequests->where('client_requests.client_id', $request->client_id);

        if($request->exists('employee_id'))
            $clientRequests = $clientRequests->where('client_requests.employee_id', $request->employee_id);

        if($request->exists('service_id'))
            $clientRequests = $clientRequests->where('client_requests.service_id', $request->service_id);

        if($request->exists('flag_attended'))
            $clientRequests = $clientRequests->where('client_requests.flag_attended', $request->flag_attended);

        if($request->exists('min_attended_at'))
            $clientRequests = $clientRequests->where('client_requests.attended_at', '>=', $request->min_attended_at);

        if($request->exists('max_attended_at'))
            $clientRequests = $clientRequests->where('client_requests.attended_at', '<=', $request->max_attended_at);

        if($request->exists('min_created_at'))
            $clientRequests = $clientRequests->where('client_requests.created_at', '>=', $request->min_created_at);

        if($request->exists('max_created_at'))
            $clientRequests = $clientRequests->where('client_requests.created_at', '<=', $request->max_created_at);

        $clientRequests = $clientRequests->select('client_requests.id as id_client_request', 'services.id as id_service', 'services.name as name_service', 'client_requests.flag_attended as flag_attended_client_request', 'client_requests.attended_at as attended_at_client_request', 'client_requests.observation as observation_client_request', 'client_requests.client_comment as client_comment_client_request', 'client_requests.created_at as created_at_client_request', 'clients.id as id_client', DB::raw("CONCAT(clients.names, ' ', clients.surnames) AS full_name_client"), 'employees.id as id_employee', DB::raw("CONCAT(employees.name, ' ', employees.surname) AS full_name_employee"))
                                        ->get();



        return response()->json([
            'status'            =>  'success',
            'clientRequests'    => $clientRequests
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
            'service_id'        =>  'required|numeric|exists:services,id'
        ],
        [
            'service_id.required'      =>  'El ID del Servicio es Requerido',
            'service_id.numeric'       =>  'El ID del Servicio debe ser numérico',
            'service_id.exists'        =>  'El ID del Servicio NO existe'
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $service = Service::find($request->service_id);

        if(!$service->flag_active)
            return response()->json(['errors' => 'El Servicio por el cual desea realizar una petición, está temporalmente desactivado'], 422);

        if( !checkIfTheUserHasTheRole($service->roles_slug, Auth::user()->getRoles(), 'slug')  && ($service->type != 'Plan') && ($service->type != 'Inmigration') )
            return response()->json(['errors'   =>  'El Servicio por el cual desea realizar una petición, no está disponible para su rol'], 422);

        if( checkIfTheUserHasTheRole($service->roles_slug, Auth::user()->getRoles(), 'slug') && (($service->type == 'Plan') || ($service->type == 'Inmigration')) )
            return response()->json(['errors'   =>  'El Servicio por el cual quiere realizar una petición, ya lo tiene asignado'], 422);

        $clientRequest = new ClientRequest();
        $clientRequest->client_id = Auth::user()->client->id;
        $clientRequest->service_id = $request->service_id;
        if($request->exists('client_comment'))
            $clientRequest->client_comment = $request->client_comment;

        if(!$clientRequest->save())
            return response()->json(['errors' => 'No se pudo crear la Petición del usuario'], 422);


        notificationSolicitudCliente(Auth::user()->client, $service->slug);
        addClientTimeline(Auth::user()->client->id, 1, 'Client Request', 'create', true);

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ClientRequest  $clientRequest
     * @return \Illuminate\Http\Response
     */
    public function show($idClientRequest)
    {
        if(ClientRequest::where('id', $idClientRequest)->count() > 0)
        {
            $clientRequest = ClientRequest::leftJoin('clients', 'client_requests.client_id', '=', 'clients.id')
                                ->leftJoin('employees', 'client_requests.employee_id', '=', 'employees.id')
                                ->leftJoin('services', 'client_requests.service_id', 'services.id')
                                ->where('client_requests.id', $idClientRequest)
                                ->select('client_requests.id as id_client_request', 'services.id as id_service', 'services.name as name_service', 'client_requests.flag_attended as flag_attended_client_request', 'client_requests.attended_at as attended_at_client_request', 'client_requests.observation as observation_client_request', 'client_requests.client_comment as client_comment_client_request', 'client_requests.created_at as created_at_client_request', 'clients.id as id_client', DB::raw("CONCAT(clients.names, ' ', clients.surnames) AS full_name_client"), 'employees.id as id_employee', DB::raw("CONCAT(employees.name, ' ', employees.surname) AS full_name_employee"))
                                        ->first();

            return response()->json([
                'status'            =>  'success',
                'clientRequest'    => $clientRequest
            ], 200);
        }

        return response()->json(['errors' => 'No existe la Petición del Cliente'], 422);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ClientRequest  $clientRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idClientRequest)
    {
        // La edición solo sirve para indicar que el requerimiento ha sido atendido

        if(ClientRequest::where('id', $idClientRequest)->count() > 0)
        {
            $validator = Validator::make($request->all(), [
                'flag_attended'             =>  'required|boolean',
                'observation'               =>  'required'
            ],
            [
                'flag_attended.required'    =>  'El Flag Atendido es requerido',
                'flag_attended.numeric'     =>  'El Flag Atendido debe tener los valores de 1 o 0',
                'observation.required'      =>  'la Observación es requerida, así sea un espacio en blanco'
            ]);

            if($validator->fails())
                return response()->json(['errors' => $validator->errors()], 422);

            $clientRequest = ClientRequest::find($idClientRequest);

            if($request->flag_attended == 1)
            {
                $clientRequest->employee_id = Auth::user()->employee->id;
                $clientRequest->flag_attended = true;
                $clientRequest->attended_at = date('Y-m-d H:i:s');
                $clientRequest->observation = $request->observation;
            }
            else{
                $clientRequest->employee_id = null;
                $clientRequest->flag_attended = false;
                $clientRequest->attended_at = null;
                $clientRequest->observation = null;
            }

            if(!$clientRequest->save())
                return response()->json(['errors' => 'No se pudo actualizar la Petición del Cliente'], 422);

            if($request->flag_attended == 1)
                addClientTimeline($clientRequest->client_id, Auth::user()->employee->id, 'Client Request', 'update', false);
            else
                addClientTimeline($clientRequest->client_id, Auth::user()->employee->id, 'Client Request', 'cleaned', false);

            return response()->json(['status' => 'success'], 200);
        }

        return response()->json(['errors' => 'No existe la Petición del Cliente'], 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ClientRequest  $clientRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClientRequest $clientRequest)
    {
        // Ningún requerimiento debería ser borrado
    }

    public function indexMyRequest()
    {
        $clientRequests = ClientRequest::leftJoin('clients', 'client_requests.client_id', '=', 'clients.id')
                                    ->leftJoin('employees', 'client_requests.employee_id', '=', 'employees.id')
                                    ->leftJoin('services', 'client_requests.service_id', '=', 'services.id')
                                    ->where('client_requests.client_id', Auth::user()->client->id)
                                    ->select('client_requests.id as id_client_request', 'services.id as id_service', 'services.name as name_service', 'client_requests.client_comment as client_comment_client_request','client_requests.flag_attended as flag_attended_client_request', 'client_requests.attended_at as attended_at_client_request', 'client_requests.created_at as created_at_client_request')
                                    ->get();

        return response()->json([
        'status'            =>  'success',
        'clientRequests'    => $clientRequests
        ], 200);
    }
}
