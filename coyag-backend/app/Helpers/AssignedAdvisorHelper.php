<?php

use App\Models\Client;
use App\Models\Employee;
use App\Models\AssignedAdvisor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

function assignAdvisor($clientId, $generatorEmployeeId)
{
    $employee = Employee::leftJoin('users', 'users.id', '=', 'employees.user_id')
            ->leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
            ->leftJoin('assigned_advisors', 'assigned_advisors.employee_id', '=', 'employees.id')
            ->leftJoin('clients', 'clients.id', '=', 'assigned_advisors.client_id')
            ->whereIn('roles.slug', ['asesor.comercial', 'asesor.ejecutivo'])
            ->where('employees.flag_permission', '=', false)
            ->select('employees.id as id_employee', DB::raw('COUNT(clients.id) as num_clients'))
            ->groupBy('employees.id')
            ->orderBy('num_clients', 'ASC')
            ->first();


    $assignedAdvisor = new AssignedAdvisor();
    $assignedAdvisor->client_id = $clientId;
    $assignedAdvisor->employee_id = 14; //$employee->id_employee;
    $assignedAdvisor->generator_employee_id = $generatorEmployeeId;

    addClientTimeline($clientId, $generatorEmployeeId, "Assigned Advisor", "create", false);

    $assignedAdvisor->save();

    notificationAsignacionAsesor($clientId, $employee->id_employee, $generatorEmployeeId);

    return $employee->id_employee;

}
