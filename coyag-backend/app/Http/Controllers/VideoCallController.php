<?php

namespace App\Http\Controllers;

use App\Models\AssignedAdvisor;
use App\Models\Business;
use App\Models\VideoCall;
use App\Models\VideoCallType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VideoCallController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $videoCalls = VideoCall::join('video_call_types', 'video_call_types.id', '=', 'video_calls.video_call_type_id')
                                ->join('clients', 'clients.id', '=', 'video_calls.client_id')
                                ->join('employees', 'employees.id', '=', 'video_calls.employee_id')
                                ->select('video_calls.id as id_video_call', 'video_calls.list_of_business as list_of_business_video_call', 'video_calls.status as status_video_call', 'video_calls.client_availability as client_availability_video_call', 'video_calls.date_and_time as date_and_time_video_call', 'video_calls.created_at as created_at_video_call', 'video_calls.updated_at as updated_at_video_call', 'video_call_types.id as id_video_call_type', 'video_call_types.name as name_video_call_type', 'clients.id as id_client', 'clients.names as name_client', 'clients.surnames as surname_client', DB::raw('CONCAT(clients.names, " ", clients.surnames) as full_name_client'), 'employees.id as id_employee', DB::raw('CONCAT(employees.name, " ", employees.surname) as full_name_employee'));

        if($request->exists('id'))
            $videoCalls = $videoCalls->where('video_calls.id', $request->id);

        if($request->exists('status'))
            $videoCalls = $videoCalls->where('video_calls.status', $request->status);

        if($request->exists('min_date_created'))
            $videoCalls = $videoCalls->where('video_calls.created_at', '>=', $request->min_date_created);

        if($request->exists('max_date_created'))
            $videoCalls = $videoCalls->where('video_calls.created_at', '<=', $request->max_date_created);

        if($request->exists('min_date_and_time'))
            $videoCalls = $videoCalls->where('video_calls.date_and_time', '>=', $request->min_date_and_time);

        if($request->exists('max_date_and_time'))
            $videoCalls = $videoCalls->where('video_calls.date_and_time', '<=', $request->max_date_and_time);

        if($request->exists('id_video_call_type'))
            $videoCalls = $videoCalls->where('video_calls.video_call_type_id', $request->id_video_call_type);

        if(Auth::user()->hasRole(['presidente', 'asistente.direccion', 'recepcion.lobby','director.tecnologia',
                                'director.comercial', 'gerente.comercial', 'coordinador.comercial',
                                'director.ejecutivo', 'gerente.ejecutivo', 'coordinador.ejecutivo',
                                'asesor.comercial', 'asesor.ejecutivo']))
        {
            $videoCalls = $videoCalls->addSelect('video_calls.report as report_video_call')
                                        ->addSelect('video_call_types.description as description_video_call_type');

            if($request->exists('id_client'))
                $videoCalls = $videoCalls->where('video_calls.client_id', $request->id_client);
        }

        if(Auth::user()->hasRole(['presidente', 'asistente.direccion', 'recepcion.lobby','director.tecnologia',
                                'director.comercial', 'gerente.comercial', 'coordinador.comercial',
                                'director.ejecutivo', 'gerente.ejecutivo', 'coordinador.ejecutivo']))
        {
            if($request->exists('id_employee'))
                $videoCalls = $videoCalls->where('video_calls.employee_id', $request->id_employee);
        }

        if(Auth::user()->hasRole(['asesor.comercial', 'asesor.ejecutivo']))
        {
            $videoCalls = $videoCalls->where('video_calls.employee_id', Auth::user()->employee->id);
        }

        if(Auth::user()->hasRole(['usuario.premium.mayor', 'usuario.premium.menor', 'usuario.estandar.mayor', 'usuario.estandar.menor', 'usuario.lite', 'cliente.registrado', 'cliente.fase.evaluacion', 'cliente.fase.analisis', 'cliente.fase.ejecucion', 'cliente.fase.asesoramiento.integral']))
        {
            $videoCalls = $videoCalls->where('video_calls.client_id', Auth::user()->client->id);
        }

        $videoCalls = $videoCalls->get();

        return response()->json([
            'status'        =>  'success',
            'videoCalls'     =>  $videoCalls
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
        $validator = Validator::make($request->all(), [
            'video_call_type_id'    =>  'required|numeric|exists:video_call_types,id',
            'client_availability'   =>  'required|max:255',
        ],
        [
            'video_call_type_id.required'       =>  'El ID del Tipo de Video es requerido',
            'video_call_type_id.numeric'        =>  'El ID del Tipo de Video debe ser del tipo numérico',
            'video_call_type_id.exists'         =>  'El ID del Tipo de Video no existe',
            'client_availability.required'      =>  'La Disponibilidad del Cliente es requerida',
            'client_availability.max'           =>  'La Disponibilidad del Cliente debe tener una longitud máxima de 255 caracteres',
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $videoCall = new VideoCall();

        $videoCall->video_call_type_id = $request->video_call_type_id;

        $videoCall->client_id = Auth::user()->client->id;

        if(!videoCallsAvailable($request->video_call_type_id))
            return response()->json(['errors'   =>  'No posee Videollamadas disponibles'], 422);

        $aAd = AssignedAdvisor::where('client_id', Auth::user()->client->id)->first();
        $videoCall->employee_id = $aAd->employee_id;

        $videoCallType = VideoCallType::find($request->video_call_type_id);

        if($request->exists('list_of_business'))
        {
            $arrAuxBusinesses = explode(',', $request->list_of_business);

            $auxBusinessNumber = true;

            switch($videoCallType->business_number)
            {
                case '0': $auxBusinessNumber = (count($arrAuxBusinesses) == 0) ? true : false ; break;
                case '1': $auxBusinessNumber = (count($arrAuxBusinesses) == 1) ? true : false ; break;
            }

            if(!$auxBusinessNumber)
                return response()->json(['errors'   =>  'El número de negocios no coincide con el tipo de la Videollamada'], 422);

            for($i = 0; $i < count($arrAuxBusinesses); $i++)
            {
                $business = Business::where('id_code', $arrAuxBusinesses[$i])->first();

                if($business)
                {
                    if($business->flag_sold)
                        return response()->json(['errors' => 'El negocio con id_code: ' . $arrAuxBusinesses[$i] . ' ya ha sido vendido'], 422);

                    if(!$business->flag_active)
                        return response()->json(['errors' => 'El negocio con id_code: ' . $arrAuxBusinesses[$i] . ' no se encuentra activo'], 422);
                }
                else
                    return response()->json(['errors' => 'El negocio con id_code: ' . $arrAuxBusinesses[$i] . ' no existe'], 422);
            }

            $videoCall->list_of_business = $request->list_of_business;
        }
        else{
            if($videoCallType->business_number == 1)
                return response()->json(['errors' => 'El Tipo de Videollamada seleccionada exige la elección de un negocio'], 422);
        }

        $videoCall->client_availability = $request->client_availability;

        if($videoCall->save())
        {
            notificationVideoCall(Auth::user()->client, $videoCall, $videoCallType);
            addClientTimeline(Auth::user()->client->id, 1, "VideoCall", "create", true);
            return response()->json(['status' => 'success'], 200);
        }


        return response()->json(['errors' => 'No se pudo guardar la Videollamada'], 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VideoCall  $videoCall
     * @return \Illuminate\Http\Response
     */
    public function show($idVideoCall)
    {
        if(VideoCall::where('id', $idVideoCall)->count() == 0)
            return response()->json(['errors' => 'La Videollamada no existe'], 422);


        $videoCall = VideoCall::join('video_call_types', 'video_call_types.id', '=', 'video_calls.video_call_type_id')
                                ->join('clients', 'clients.id', '=', 'video_calls.client_id')
                                ->join('employees', 'employees.id', '=', 'video_calls.employee_id')
                                ->where('video_calls.id', $idVideoCall)
                                ->select('video_calls.id as id_video_call', 'video_calls.list_of_business as list_of_business_video_call', 'video_calls.status as status_video_call', 'video_calls.client_availability as client_availability_video_call', 'video_calls.date_and_time as date_and_time_video_call', 'video_calls.created_at as created_at_video_call', 'video_calls.updated_at as updated_at_video_call', 'video_call_types.id as id_video_call_type', 'video_call_types.name as name_video_call_type', 'clients.id as id_client', 'clients.names as name_client', 'clients.surnames as surname_client', DB::raw('CONCAT(clients.names, " ", clients.surnames) as full_name_client'), 'employees.id as id_employee', DB::raw('CONCAT(employees.name, " ", employees.surname) as full_name_employee'));

        if(Auth::user()->hasRole(['presidente', 'asistente.direccion', 'recepcion.lobby','director.tecnologia',
                                    'director.comercial', 'gerente.comercial', 'coordinador.comercial',
                                    'director.ejecutivo', 'gerente.ejecutivo', 'coordinador.ejecutivo']))
        {
            $videoCall = $videoCall->addSelect('video_calls.report as report_video_call')
                                    ->addSelect('video_call_types.description as description_video_call_type');
        }

        if(Auth::user()->hasRole(['asesor.comercial', 'asesor.ejecutivo']))
        {
            $videoCall = $videoCall->where('video_calls.client_id', Auth::user()->employee->id)
                                    ->addSelect('video_calls.report as report_video_call')
                                    ->addSelect('video_call_types.description as description_video_call_type');
        }

        if(Auth::user()->hasRole(['usuario.premium.mayor', 'usuario.premium.menor', 'usuario.estandar.mayor', 'usuario.estandar.menor', 'usuario.lite', 'cliente.registrado', 'cliente.fase.evaluacion', 'cliente.fase.analisis', 'cliente.fase.ejecucion', 'cliente.fase.asesoramiento.integral']))
        {
            $videoCall = $videoCall->where('video_calls.client_id', Auth::user()->client->id);
        }

        $videoCall = $videoCall->first();

        return response()->json([
            'status'        =>  'success',
            'videoCall'     =>  $videoCall
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\VideoCall  $videoCall
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VideoCall $videoCall)
    {
        //POR AHORA NO HAY ELEMENTOS REQUERIDOS PARA QUE SEAN EDITADOS
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VideoCall  $videoCall
     * @return \Illuminate\Http\Response
     */
    public function destroy(VideoCall $videoCall)
    {
        //NO APLICA PORQUE LAS VIDEOLLAMADAS TIENEN UN STATUS ASOCIADO
    }

    public function changeStatusAndReport(Request $request, $idVideoCall)
    {
        $videoCall = VideoCall::find($idVideoCall);

        if($videoCall){

            if($videoCall->employee_id != Auth::user()->employee->id)
                return response()->json(['errors' => 'Usted no es el asesor asignado a la videollamada'], 422);

            if($videoCall->status == 'Atendida' || $videoCall->status == 'Cancelada' || $videoCall->status == 'No atendida')
                return response()->json(['errors' => "El Estatus de esta Videollamada es $videoCall->status y ya no acepta modificación"], 422);

            $validator = Validator::make($request->all(), [
                'status'        =>  'required|in:"Atendida","Cancelada","No atendida","Agendada',
                'report'        =>  'required'

            ],
            [
                'status.required'           =>  'El Estatus es requerido',
                'status.in'                 =>  'El Estatus debe tener los valores: Atendida, Cancelada, No atendida o Agendada',
                'report.required'           =>  'El Reporte es requerido'
            ]);

            if($validator->fails())
                return response()->json(['errors'   =>  $validator->errors()], 422);

            $videoCall->status = $request->status;
            $videoCall->report = $request->report;

            if($videoCall->status == 'Atendida' || $videoCall->status == 'Agendada')
            {
                $validator = Validator::make($request->all(), [
                    'date_and_time' => 'required|date_format:"Y-m-d H:i:s"'
                ],
                [
                    'date_and_time.required'        =>  'La fecha y hora es requerida',
                    'date_and_time.date_format'     =>  'La fecha y hora debe tener el formato aaaa-mm-dd hh:mm:ss'
                ]);

                if($validator->fails())
                    return response()->json(['errors'   =>  $validator->errors()], 422);

                $videoCall->date_and_time = $request->date_and_time;
            }

            if($videoCall->save())
                return response()->json(['status'   =>  'success'], 200);

            return response()->json(['errors'   =>  'No se pudo actualizar el Estatus y Reporte de la Videollamada'], 422);
        }
        else
            return response()->json(['errors'   =>  'La Videollamada no existe'], 422);
    }

    public function restartStatusAndReport($idVideoCall)
    {
        $videoCall = VideoCall::find($idVideoCall);

        if(Auth::user()->hasRole(['presidente', 'director.tecnologia', 'director.comercial', 'director.ejecutivo']))
        {
            if($videoCall)
            {
                $videoCall->status = 'Por atender';
                $videoCall->date_and_time = null;
                $videoCall->report = null;

                if($videoCall->save())
                    return response()->json(['status'   =>  'success'], 200);

                return response()->json(['errors'   =>  'No se pudo reestablecer la Videollamada'], 422);
            }
            else
                return response()->json(['errors'   =>  'La Videollamada no existe'], 422);
        }
        else {
            return response()->json(['errors' =>  'Solo Presidente y los Directores de Tecnología, Comercial y Ejecutivo pueden restablecer el Estatus y Reporte'], 422);
        }
    }
}
