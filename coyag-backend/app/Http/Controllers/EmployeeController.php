<?php

namespace App\Http\Controllers;
use App\Models\Client;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Headquarter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index() {
        $employees = Employee::leftJoin('users', 'employees.user_id', '=', 'users.id')
            ->select('employees.*', 'users.id as user_id', 'users.email')
            ->with('contracts')
            ->get();
        return response()->json($employees, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'email'                     =>  'required|email|unique:users',
            'password'                  =>  'required|min:8|confirmed',
            'password_confirmation'     =>  'required|same:password',
            'name'                      =>  'required',
            'surname'                   =>  'required',
            'mobile_phone'              =>  'required',
        ],
        [
            'email.required'        =>  'El correo electrónico es requerido',
            'email.email'           =>  'Debe ingresar un correo electrónico válido',
            'email.unique'          =>  'El correo electrónico debe ser único',
            'password.required'     =>  'La contraseña es requerida.',
            'password.min'          =>  'La longitud mínima de la contraseña es de 8 caracteres.',
            'password.confirmed'                =>  'Las contraseñas no coinciden.',
            'password_confirmation.required'    =>  'La confirmación de la contraseña es requerida.',
            'password_confirmation.same'        =>  'La confirmación de la contraseña y la contraseña no coinciden.',
            'name.required'                     =>  'El nombre es requerido',
            'surname.required'                  =>  'El apellido es requerido',
            'mobile_phone.required'             =>  'El teléfono celular es requerido',
        ]);

        if($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);

        $user = new User();
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->email_verified_at = now();

        if(!$user->save()) return response()->json(['errors' => 'No se pudo crear el Usuario del Empleado'], 422);

        $employee = new Employee();
        $employee->user_id = $user->id;
        $employee->name = $request->name;
        $employee->surname = $request->surname;
        $employee->mobile_phone = $request->mobile_phone;

        if(!$employee->save()) {
            $user->forceDelete();
            return response()->json(['errors' => 'No se puedo crear al Empleado']);
        }

        $employee = Employee::leftJoin('users', 'employees.user_id', '=', 'users.id')->select('employees.*', 'users.id as user_id', 'users.email')->with('contracts')->find($employee->id);
        return response()->json($employee, 200);
    }
    
    public function show($id) {
        $employee = Employee::leftJoin('users', 'employees.user_id', '=', 'users.id')->where('employees.id', $id)->select('employees.*', 'users.id as user_id', 'users.email')->first();
        if (!$employee)  return response()->json(['error' => 'El Empleado no existe'], 422);
        return response()->json( $employee, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $employee = Employee::find($id);
        if(!$employee) return response()->json(['errors' => 'El Empleado no existe'], 422);

        if ($request->has('password')) {
            $validator = Validator::make($request->all(), [
                'password'                  =>  'required|min:8|confirmed',
                'password_confirmation'     =>  'required|same:password',
            ],
            [
                'password.required'     =>  'La contraseña es requerida.',
                'password.min'          =>  'La longitud mínima de la contraseña es de 8 caracteres.',
                'password.confirmed'                =>  'Las contraseñas no coinciden.',
                'password_confirmation.required'    =>  'La confirmación de la contraseña es requerida.',
                'password_confirmation.same'        =>  'La confirmación de la contraseña y la contraseña no coinciden.',
            ]);

            if($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);

            $user = User::find($employee->user_id);
            $user->password = bcrypt($request->password);
            if(!$user->save()) return response()->json(['errors' => 'No se pudo actualizar la contraseña'], 422);

            return response()->json(['status' => 'success'], 200);
        }

        if($request->has('email')) {
            $user = User::find($employee->user_id);
            $user->email = $request->email;
            $user->save();
        }
        if($request->has('name')) $employee->name = $request->name; 
        if($request->has('surname')) $employee->surname = $request->surname;
        if($request->has('mobile_phone')) $employee->mobile_phone = $request->mobile_phone;
        $employee->save();

        $employee = Employee::leftJoin('users', 'employees.user_id', '=', 'users.id')->select('employees.*', 'users.id as user_id', 'users.email')->with('contracts')->find($employee->id);
        return response()->json($employee);
    }

    public function destroy( $id ) {
        $item = Employee::find($id);
        if (!$item) return response()->json(['message' => 'Employee not found'], 404);
        $item->delete();
        return response()->json(['message' => 'Employee deleted successfully']);
    }
}
