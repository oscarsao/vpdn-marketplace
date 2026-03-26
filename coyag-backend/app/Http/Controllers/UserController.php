<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use \Gumlet\ImageResize;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $users = User::leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                ->leftJoin('roles', 'role_user.role_id', '=', 'roles.id')
                ->select('users.id as id', 'email', 'role_user.role_id as id_role', 'roles.name as name_role')
                ->get();

        return response()->json(
            [
                'status' => 'success',
                'users' => $users->toArray()
            ], 200);
    }


    public function show(Request $request, $idUser)
    {

        if(User::where('id', $idUser)->count() > 0) {

            $user = User::find($idUser);

            return response()->json(
                [
                    'status' => 'success',
                    'user' => $user->toArray()
                ], 200);

        }

        return response()->json(['error' => 'El Usuario no existe'], 422);
    }


    public function destroy($idUser)
    {
        if(User::where('id', $idUser)->count() > 0) {

            if(Auth::user()->id == $idUser)
                return response()->json(['error' => 'No se puede borrar a usted mismo'], 401);


            $numC = Client::where('user_id', '=', $idUser)->count();

            if($numC == 1) {
                $client = Client::where('user_id', '=', $idUser)->first();
                //$employee = Employee::where("user_id", Auth::user()->id)->first();
                //storeTimeline($client->id, $employee->id, "User", "delete");
                $client->delete();
            }

            $numE = Employee::where('user_id', '=', $idUser)->count();

            if($numE == 1)
                Employee::where('user_id', '=', $idUser)->delete();


            if(User::find($idUser)->delete()) {
                return response()->json(
                    [
                        'status' => 'success'
                    ], 200);
            }

            return response()->json(['error' => 'No se pudo borrar al usuario'], 401);

        }

        return response()->json(['error' => 'El Usuario no existe'], 422);
    }

    /**
     * CAMBIO DE flag_login y observation_flag_login
     */
    public function flagLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'                        =>  'required|numeric',
            'user_type'                 =>  'required|in:"client","employee"',
            'flag_login'                =>  'required|boolean'
        ],
        [
            'id.required'               =>  'El ID (Cliente o Empleado) es requerido',
            'id.numeric'                =>  'El ID (Cliente o Empleado) debe ser del tipo numérico',
            'user_type.required'        =>  'El Tipo de usuario es requerido',
            'user_type.in'              =>  'El Tipo de usuario debe ser client o employee',
            'flag_login.required'       =>  'flag_login es requerido',
            'flag_login.boolean'        =>  'flag_login debe tener los valores de 1 o 0'
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $aux = ($request->user_type == 'client') ? Client::find($request->id) : Employee::find($request->id);
        $user = User::find($aux->user_id);

        // if($user->flag_login == $request->flag_login)
        //    return response()->json(['errors' => 'El flag_login que se envía es igual a su valor actual'], 422);

        $user->flag_login = $request->flag_login;


        if($request->flag_login == 1)
            $user->observation_flag_login = null;
        else
        {
            $validatorObservation = Validator::make($request->all(), [
                'observation_flag_login'    =>  'required'
            ],
            [
                'observation_flag_login.required'   =>  'observation_flag_login es requerido'
            ]);

            if($validatorObservation->fails())
                return response()->json(['errors' => $validatorObservation->errors()], 422);

            $user->observation_flag_login = $request->observation_flag_login;
        }

        if($user->save())
        {
            if($request->user_type == 'employee' && !$request->flag_login)
            {
                $user->employee->flag_permission = true;
                $user->employee->observation_flag_permission = 'No tiene acceso a la plataforma';
                $user->employee->save();
            }

            return response()->json(['status'  => 'success'], 200);
        }


        return response()->json(['errors' => 'No se pudo guardar el usuario'], 422);
    }


    /**
     * CAMBIO DE PASSWORD
     */

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'                        =>  'required|numeric',
            'user_type'                 =>  'required',
            'password'                  =>  'required|min:8|confirmed',
            'password_confirmation'     =>  'required|same:password'
        ],
        [
            'id.required'               =>  'El ID (Cliente o Empleado) es requerido',
            'id.numeric'                =>  'El ID (Cliente o Empleado) debe ser del tipo numérico',
            'user_type.required'        =>  'El Tipo de usuario es requerido',
            'password.required'         =>  'La contraseña es requerida',
            'password.min'              =>  'La longitud mínima de la contraseña es de 8 caracteres',
            'password.confirmed'        =>  'Las contraseñas no coinciden',
            'password_confirmation.required'    => 'La confirmación de la contraseña es requerida',
            'password_confirmation.same'        => 'La confirmación de la contraseña y la contraseña no coinciden'
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $resp = $this->validateUserType($request->id, $request->user_type);
        if($resp != '')
            return $resp;

        $aux = ($request->user_type == 'client') ? Client::find($request->id) : Employee::find($request->id);
        $user = User::find($aux->user_id);

        $user->password = bcrypt($request->password);

        if($user->save())
            return response()->json(['status'  => 'success'], 200);

        return response()->json(['errors' => 'No se pudo guardar el usuario'], 422);

    }

    public function changeMyPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password'                  =>  'required|min:8|confirmed',
            'password_confirmation'     =>  'required|same:password'
        ],
        [
            'password.required'         =>  'La contraseña es requerida',
            'password.min'              =>  'La longitud mínima de la contraseña es de 8 caracteres',
            'password.confirmed'        =>  'Las contraseñas no coinciden',
            'password_confirmation.required'    => 'La confirmación de la contraseña es requerida',
            'password_confirmation.same'        => 'La confirmación de la contraseña y la contraseña no coinciden'
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        Auth::user()->password = bcrypt($request->password);

        if(Auth::user()->save())
            return response()->json(['status'  => 'success'], 200);

        return response()->json(['errors' => 'No se pudo guardar el usuario'], 422);
    }

    /**
     * CAMBIO DEL NOMBRE DE USUARIO
     */

    public function changeUsername(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'                        =>  'required|numeric',
            'user_type'                 =>  'required',
            'username'                  =>  'required|min:6|unique:users',
        ],
        [
            'id.required'               =>  'El ID (Cliente o Empleado) es requerido',
            'id.numeric'                =>  'El ID (Cliente o Empleado) debe ser del tipo numérico',
            'user_type.required'        =>  'El Tipo de usuario es requerido',
            'username.required'         =>  'El nombre de usuario (username) es requerido',
            'username.min'              =>  'El nombre de usuario (username) debe tener una logitud mínima de 6 caracteres',
            'username.unique'           =>  'El nombre de usuario (username) debe ser único'
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $resp = $this->validateUserType($request->id, $request->user_type);
        if($resp != '')
            return $resp;

        $aux = ($request->user_type == 'client') ? Client::find($request->id) : Employee::find($request->id);
        $user = User::find($aux->user_id);


        $arrayAux = array('*', '#', '@', ' ', '=', '¿', '?', '¡', '!');
        foreach(str_split($request->username) as $aux)
        {
            if(in_array($aux, $arrayAux))
                return response()->json(['errors' => 'El nombre de usuario no puede contener los siguientes caracteres: *, #, @, =, ¿, ?, ¡, ! o espacios en blanco'], 422);
        }

        $user->username = $request->username;

        if($user->save())
            return response()->json(['status'  => 'success'], 200);

        return response()->json(['errors' => 'No se pudo guardar el usuario'], 422);
    }

    public function changeMyUsername(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'                  =>  'required|min:6|unique:users'
        ],
        [
            'username.required'         =>  'El nombre de usuario (username) es requerido',
            'username.min'              =>  'El nombre de usuario (username) debe tener una logitud mínima de 6 caracteres',
            'username.unique'           =>  'El nombre de usuario (username) debe ser único'
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $arrayAux = array('*', '#', '@', ' ', '=', '¿', '?', '¡', '!');
        foreach(str_split($request->username) as $aux)
        {
            if(in_array($aux, $arrayAux))
                return response()->json(['errors' => 'El nombre de usuario no puede contener los siguientes caracteres: *, #, @, =, ¿, ?, ¡, ! o espacios en blanco'], 422);
        }

        Auth::user()->username = $request->username;

        if(Auth::user()->save())
            return response()->json(['status'  => 'success'], 200);

        return response()->json(['errors' => 'No se pudo guardar el usuario'], 422);
    }

    /**
     * CAMBIO DE IMAGEN DE PERFIL
     */

    public function changeProfileImage(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'id'                        =>  'required|numeric',
            'user_type'                 =>  'required',
            'image' =>  'required|file|max:3072|dimensions:min_width=300,max_width=3200,min_height=300,max_height=3200,ratio=1/1|mimes:jpeg,bmp,png'
        ],
        [
            'id.required'           =>  'El ID (Cliente o Empleado) es requerido',
            'id.numeric'            =>  'El ID (Cliente o Empleado) debe ser del tipo numérico',
            'user_type.required'    =>  'El Tipo de usuario es requerido',
            'image.required'        =>  'La imagen es requerida',
            'image.file'            =>  'La imagen debe ser un tipo de archivo',
            'image.max'             =>  'La imagen debe tener un peso máximo de 3MB',
            'image.dimensions'      =>  'La imagen debe debe ser cuadrada y el tamaño debe estar entre 300px y 3200px',
            'image.mimes'           =>  'La imagen debe ser jpg, bmp o png'
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $resp = $this->validateUserType($request->id, $request->user_type);
        if($resp != '')
            return $resp;

        $aux = ($request->user_type == 'client') ? Client::find($request->id) : Employee::find($request->id);
        $user = User::find($aux->user_id);

        $auxPath = "files/users/profile-image/$user->id";
        $path = public_path($auxPath);
        File::deleteDirectory($path); //Garantiza de borrar las imágenes de perfil anterior
        Storage::makeDirectory($path);

        //original_profile_image
        $auxIMG = $request->image;
        $extension = $auxIMG->extension();
        $originalName = str_replace(' ','', $auxIMG->getClientOriginalName());
        $auxIMG->move($path, $originalName);

        $fullPathOriginalImage = $auxPath . '/' . $originalName;
        $user->original_profile_image = $auxPath . '/' . $originalName;

        $fullPathNewImage = $auxPath . '/' . Str::random(12) . '.' . $extension;
        $image = new ImageResize($fullPathOriginalImage);
        $image->resize(120, 120);
        $image->save($fullPathNewImage);
        $user->thumbnail_profile_image = $fullPathNewImage;

        $fullPathNewImage = $auxPath . '/' . Str::random(12) . '.' . $extension;
        $image = new ImageResize($fullPathOriginalImage);
        $image->resize(40, 40);
        $image->save($fullPathNewImage);
        $user->avatar_profile_image = $fullPathNewImage;

        $user->save();

        return response()->json(['status' => 'success'], 200);

    }

    public function changeMyProfileImage(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'image' =>  'required|file|max:3072|dimensions:min_width=300,max_width=3200,min_height=300,max_height=3200,ratio=1/1|mimes:jpeg,bmp,png'
        ],
        [
            'image.required'        =>  'La imagen es requerida',
            'image.file'            =>  'La imagen debe ser un tipo de archivo',
            'image.max'             =>  'La imagen debe tener un peso máximo de 3MB',
            'image.dimensions'      =>  'La imagen debe debe ser cuadrada y el tamaño debe estar entre 300px y 3200px',
            'image.mimes'           =>  'La imagen debe ser jpg, bmp o png'
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $auxPath = "files/users/profile-image/" . Auth::user()->id;
        $path = public_path($auxPath);
        File::deleteDirectory($path); //Garantiza de borrar las imágenes de perfil anterior
        Storage::makeDirectory($path);

        //original_profile_image
        $auxIMG = $request->image;
        $extension = $auxIMG->extension();
        $originalName = str_replace(' ','', $auxIMG->getClientOriginalName());
        $auxIMG->move($path, $originalName);

        $fullPathOriginalImage = $auxPath . '/' . $originalName;
        Auth::user()->original_profile_image = $auxPath . '/' . $originalName;

        $fullPathNewImage = $auxPath . '/' . Str::random(12) . '.' . $extension;
        $image = new ImageResize($fullPathOriginalImage);
        $image->resize(120, 120);
        $image->save($fullPathNewImage);
        Auth::user()->thumbnail_profile_image = $fullPathNewImage;

        $fullPathNewImage = $auxPath . '/' . Str::random(12) . '.' . $extension;
        $image = new ImageResize($fullPathOriginalImage);
        $image->resize(40, 40);
        $image->save($fullPathNewImage);
        Auth::user()->avatar_profile_image = $fullPathNewImage;

        Auth::user()->save();

        return response()->json(['status' => 'success'], 200);
    }


    private function validateUserType($id, $userType)
    {
        if(!in_array($userType, ['client', 'employee']))
            return response()->json(['errors' => 'El tipo de usuario no es válido'], 422);

        if($userType == 'client')
        {
            if(Client::where('id', $id)->count() == 0)
                return response()->json(['errors' => 'El cliente no existe'], 422);
        }

        if($userType == 'employee')
        {
            if(Employee::where('id', $id)->count() == 0)
                return response()->json(['errors' => 'El empleado no existe'], 422);
        }

        return '';
    }

    /**
     * GET IMAGEN DE PERFIL
     */

    public function showProfileImage(Request $request)
    {
        return response()->json([
            'status'                    =>  'success',
            'original_profile_image'    =>  Auth::user()->original_profile_image,
            'thumbnail_profile_image'   =>  Auth::user()->thumbnail_profile_image,
            'avatar_profile_image'      =>  Auth::user()->avatar_profile_image
        ], 200);
    }

}
