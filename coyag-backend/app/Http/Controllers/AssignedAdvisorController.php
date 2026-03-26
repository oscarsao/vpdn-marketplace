<?php

namespace App\Http\Controllers;

use App\Models\AssignedAdvisor;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignedAdvisorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $assignedAdvisors = AssignedAdvisor::leftJoin('clients', 'clients.id', '=', 'assigned_advisors.client_id')
                                ->leftJoin('employees', 'employees.id', '=', 'assigned_advisors.employee_id')
                                ->leftJoin('users', 'clients.user_id', '=', 'users.id')
                                ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                                ->leftJoin('roles', 'role_user.role_id', '=', 'roles.id')
                                ->select('assigned_advisors.id as id_assigned_advisor', 'assigned_advisors.client_id as id_client', 'users.email as email_client', 'clients.names as names_client', 'clients.surnames as surnames_client', 'assigned_advisors.employee_id as id_employee', 'employees.name as name_employee', 'employees.surname as surname_employee', DB::raw('CONCAT(employees.name, " ", employees.surname) as full_name_employee'), 'assigned_advisors.generator_employee_id as id_generator_employee', DB::raw("(SELECT CONCAT(employees.name, ' ', employees.surname) FROM employees WHERE assigned_advisors.generator_employee_id = employees.id) as full_name_generator_employee"), 'assigned_advisors.created_at as created_at_assigned_advisor', 'assigned_advisors.updated_at as updated_at_assigned_advisor')
                                ->get();

        return response()->json(
        [
            'status' => 'success',
            'assignedAdvisors' => $assignedAdvisors
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
        /*
         * No existe para este proyecto porque cuando un usuario se registra se el asigna
         * de forma automática un asesor
         */
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AssignedAdvisor  $assignedAdvisor
     * @return \Illuminate\Http\Response
     */
    public function show($idAssignedAdvisor)
    {

        $assignedAdvisor = AssignedAdvisor::leftJoin('clients', 'clients.id', '=', 'assigned_advisors.client_id')
                                ->leftJoin('employees', 'employees.id', '=', 'assigned_advisors.employee_id')
                                ->leftJoin('users', 'clients.user_id', '=', 'users.id')
                                ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                                ->leftJoin('roles', 'role_user.role_id', '=', 'roles.id')
                                ->where('assigned_advisors.id', $idAssignedAdvisor)
                                ->select('assigned_advisors.id as id_assigned_advisor', 'assigned_advisors.client_id as id_client', 'users.email as email_client','clients.names as names_client', 'clients.surnames as surnames_client', 'assigned_advisors.employee_id as id_employee', 'employees.name as name_employee', 'employees.surname as surname_employee', DB::raw('CONCAT(employees.name, " ", employees.surname) as full_name_employee'), 'assigned_advisors.generator_employee_id as id_generator_employee', DB::raw("(SELECT CONCAT(employees.name, ' ', employees.surname) FROM employees WHERE assigned_advisors.generator_employee_id = employees.id) as full_name_generator_employee"), 'assigned_advisors.created_at as created_at_assigned_advisor', 'assigned_advisors.updated_at as updated_at_assigned_advisor')
                                ->first();

        return response()->json(
            [
                'status' => 'success',
                'assignedAdvisor' => $assignedAdvisor
            ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AssignedAdvisor  $assignedAdvisor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idAssignedAdvisor)
    {
        if(AssignedAdvisor::where('id', $idAssignedAdvisor)->count() > 0) {

            $assignedAdvisor = AssignedAdvisor::find($idAssignedAdvisor);

            $idCurrentEmployee = Auth::user()->employee->id;

            if($request->exists('employee_id'))
            {
                $employee = Employee::where('id', $request->employee_id)->first();

                if($employee)
                {
                    if($employee->flag_permission)
                        return response()->json(['errors' => 'El Empleado está de permiso'], 422);
                }
                else
                    return response()->json(['errors' => 'El Empleado no existe'], 422);

                $assignedAdvisor->generator_employee_id = $idCurrentEmployee;
                $assignedAdvisor->employee_id = $request->employee_id;
            }

            if($assignedAdvisor->save()) {
                addClientTimeline($assignedAdvisor->client_id, $idCurrentEmployee, "Assigned Advisor", "update", false);
                notificationAsignacionAsesor($assignedAdvisor->client_id, $assignedAdvisor->employee_id, $assignedAdvisor->generator_employee_id);
                return response()->json(['status' => 'success'], 200);
            }

        }
        return response()->json(['errors' => 'No existe la asignación de Asesor Comercial'], 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AssignedAdvisor  $assignedAdvisor
     * @return \Illuminate\Http\Response
     */
    public function destroy($idAssignedAdvisor)
    {
        /**
         * Este método no existe porque un Cliente no se puede quedar sin asesor
         */
    }
}
