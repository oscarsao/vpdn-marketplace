<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $calendars = Calendar::leftJoin('clients', 'calendars.client_id', '=', 'clients.id')
                                ->leftJoin('employees', 'calendars.employee_id', '=', 'employees.id');

        if(isset(Auth::user()->employee->id)){
            if($request->exists('client_id'))
                $calendars = $calendars->where('client_id', $request->client_id);
        }
        else {
            $calendars = $calendars->where('client_id', Auth::user()->client->id);
        }

        if($request->exists('employee_id'))
            $calendars = $calendars->where('employee_id', $request->employee_id);

        if($request->exists('status'))
            $calendars = $calendars->where('status', $request->status);

        if($request->exists('start_date_min'))
            $calendars = $calendars->where('start_date', '>=', $request->start_date_min);

        if($request->exists('start_date_max'))
            $calendars = $calendars->where('start_date', '<=', $request->start_date_max);

        if($request->exists('due_date_min'))
            $calendars = $calendars->where('due_date', '>=', $request->due_date_min);

        if($request->exists('due_date_max'))
            $calendars = $calendars->where('due_date', '<=', $request->due_date_max);

        $calendars = $calendars->select('calendars.id as id_calendar', 'calendars.name as name_calendar', 'calendars.description as description_calendar', 'calendars.start_date as start_date_calendar', 'calendars.due_date as due_date_calendar', 'calendars.status as status_calendar', 'calendars.created_at as created_at_calendar', 'calendars.updated_at as updated_at_calendar', 'clients.id as id_client', DB::raw('CONCAT(clients.names, " ", clients.surnames) as full_name_client'), 'employees.id as id_employee', DB::raw('CONCAT(employees.name, " ", employees.surname) as full_name_employee'))
                                ->get();

        return response()->json([
            'status'        => 'success',
            'calendars'     =>  $calendars
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
            'client_id'             =>  'required|exists:clients,id',
            'name'                  =>  'required|max:255',
            'start_date'            =>  'required|date',
            'due_date'              =>  'date'
        ],
        [
            'client_id.required'    =>  'EL ID del Cliente es Requerido',
            'client_id.exists'      =>  'El Cliente no existe en el sistema',
            'name.required'         =>  'El Nombre del Evento es Requerido',
            'name.max'              =>  'El Nombre debe tener una longitud máxima de 255 caracteres',
            'start_date.required'   =>  'La Fecha de Inicio es requerida',
            'start_date.date'       =>  'El formato de Fecha de Inicio no es válido',
            'due_date.date'         =>  'El formato de Fecha de Culminación no es válido',
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        $calendar = new Calendar();

        $calendar->client_id = $request->client_id;
        $calendar->employee_id = Auth::user()->employee->id;

        $calendar->name = $request->name;

        if($request->exists('description'))
            $calendar->description = $request->description;

        $calendar->start_date = $request->start_date;

        if($request->exists('due_date'))
            $calendar->due_date = $request->due_date;
        else
        {
            $startDate = new Carbon($calendar->start_date);
            $calendar->due_date = $startDate->addHour()->toDateTimeString();
        }

        $calendar->status = 'Pendiente';

        $startDate = new Carbon($calendar->start_date);
        $dueDate = new Carbon($calendar->due_date);

        if($startDate >= $dueDate)
            return response()->json(['errors' => 'La Fecha de Inicio no puede ser mayor o igual a la Fecha de Culminación'], 422);

        if($calendar->save())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors' => 'No se pudo Crear el Evento en el Calendario'], 422);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function show($idCalendar)
    {
        if(Calendar::where('id', $idCalendar)->count() == 0)
            return response()->json(['errors'   =>  'El Evento en el Calendario no existe'], 422);

        $calendar = Calendar::leftJoin('clients', 'calendars.client_id', '=', 'clients.id')
                                ->leftJoin('employees', 'calendars.employee_id', '=', 'employees.id')
                                ->where('calendars.id', $idCalendar);

        if(isset(Auth::user()->client->id))
           $calendar = $calendar->where('client_id', Auth::user()->client->id);


        $calendar = $calendar->select('calendars.id as id_calendar', 'calendars.name as name_calendar', 'calendars.description as description_calendar', 'calendars.start_date as start_date_calendar', 'calendars.due_date as due_date_calendar', 'calendars.status as status_calendar', 'calendars.created_at as created_at_calendar', 'calendars.updated_at as updated_at_calendar', 'clients.id as id_client', DB::raw('CONCAT(clients.names, " ", clients.surnames) as full_name_client'), 'employees.id as id_employee', DB::raw('CONCAT(employees.name, " ", employees.surname) as full_name_employee'))
                                ->first();

        return response()->json([
        'status'        => 'success',
        'calendar'      =>  $calendar
        ], 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idCalendar)
    {
        $calendar = Calendar::find($idCalendar);

        if(!$calendar)
            return response()->json(['errors'   =>  'El Evento en el Calendario no existe'], 422);

        $validator = Validator::make($request->all(),
        [
            'client_id'             =>  'exists:clients,id',
            'name'                  =>  'max:255',
            'start_date'            =>  'date',
            'due_date'              =>  'date'
        ],
        [
            'client_id.exists'      =>  'El Cliente no existe en el sistema',
            'name.max'              =>  'El Nombre debe tener una longitud máxima de 255 caracteres',
            'start_date.date'       =>  'El formato de Fecha de Inicio no es válido',
            'due_date.date'         =>  'El formato de Fecha de Culminación no es válido',
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        if($request->exists('client_id'))
            $calendar->client_id = $request->client_id;

        if($request->exists('name'))
            $calendar->name = $request->name;

        if($request->exists('description'))
            $calendar->description = $request->description;

        if($request->exists('start_date'))
            $calendar->start_date = $request->start_date;

        if($request->exists('due_date'))
            $calendar->due_date = $request->due_date;
        else
        {
            $startDate = new Carbon($calendar->start_date);
            $calendar->due_date = $startDate->addHour()->toDateTimeString();
        }

        $startDate = new Carbon($calendar->start_date);
        $dueDate = new Carbon($calendar->due_date);

        if($startDate >= $dueDate)
            return response()->json(['errors' => 'La Fecha de Inicio no puede ser mayor o igual a la Fecha de Culminación'], 422);

        if($calendar->save())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors' => 'No se pudo Actualizar el Evento en el Calendario'], 422);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function destroy($idCalendar)
    {
        $calendar = Calendar::find($idCalendar);

        if(!$calendar)
            return response()->json(['errors'   =>  'El Evento en el Calendario no existe'], 422);

        if($calendar->status == 'Realizado')
            return response()->json(['errors'   =>  'Este Evento en el Calendario no se puede borrar porque ya ha sido Realizado'], 422);

        if($calendar->delete())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors' => 'No se pudo Eliminar el Evento en el Calendario'], 422);
    }

    public function updateStatus(Request $request, $idCalendar)
    {
        $calendar = Calendar::find($idCalendar);

        if(!$calendar)
            return response()->json(['errors'   =>  'El Evento en el Calendario no existe'], 422);

        $validator = Validator::make($request->all(),
        [
            'status'                =>  'required|in:"Pendiente","Realizado"'
        ],
        [
            'status.required'       =>  'El Estatus es Requerido',
            'status.in'             =>  'Los valores aceptados del Estatus son: Pendiente y Realizado'
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        $calendar->status = $request->status;

        if($calendar->save())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors'   =>  'No se pudo Actualizar el Estatus del Evento del Calendario'], 422);
    }
}
