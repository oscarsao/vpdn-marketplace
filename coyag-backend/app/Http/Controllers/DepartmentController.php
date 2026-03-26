<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = Department::select('id as id_department', 'name as name_department', 'description as description_department', 'created_at as created_at_department', 'updated_at as updated_at_department', 'deleted_at as deleted_at_department')
                            ->get();

        return response()->json(
            [
                'status' => 'success',
                'departments' => $departments->toArray()
            ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function show($idDepartment)
    {

        if(Department::where('id', $idDepartment)->count() > 0) {

            $department = Department::select('id as id_department', 'name as name_department', 'description as description_department', 'created_at as created_at_department', 'updated_at as updated_at_department', 'deleted_at as deleted_at_department')
                                ->where('id', $idDepartment)
                                ->first();

            return response()->json(
            [
                'status' => 'success',
                'department' => $department
            ], 200);

        }

        return response()->json(['error' => 'El Departamento no existe'], 422);
    }

}
