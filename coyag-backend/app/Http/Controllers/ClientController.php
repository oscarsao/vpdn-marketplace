<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

// Str
use Illuminate\Support\Str;

class ClientController extends Controller {
    public function index() {
        $clients = Client::leftJoin('users', 'clients.user_id', '=', 'users.id')
            ->leftJoin('countries as first_nation', 'clients.first_nationality_id',  '=', 'first_nation.id')
            ->leftJoin('countries as second_nation', 'clients.second_nationality_id', '=', 'second_nation.id')
            ->select(
                'clients.id',
                'clients.first_nationality_id',
                'clients.second_nationality_id',
                'clients.names',
                'clients.surnames',
                'clients.phone_mobile',
                'users.id as user_id', 'users.email as email', 'first_nation.name as first_nation_name', 'second_nation.name as second_nation_name')
            ->with('contracts')->get();
        return response()->json( $clients , 200);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'email'                 => 'required|email|unique:users',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required|same:password'
        ],[
            'email.required'        =>  'El correo electrónico es requerido',
            'email.email'           =>  'Debe ingresar un correo electrónico válido',
            'email.unique'          =>  'El correo electrónico debe ser único',
            'password.required'     =>  'La contraseña es requerida.',
            'password.min'          =>  'La longitud mínima de la contraseña es de 8 caracteres.',
            'password.confirmed'          => 'Las contraseñas no coinciden.',
            'password_confirmation.required'    => 'La confirmación de la contraseña es requerida.',
            'password_confirmation.same'        => 'La confirmación de la contraseña y la contraseña no coinciden.'
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        if(!isset(Auth::user()->employee->id) && $this->createClientLimitIP($request->ip()))
            return response()->json(['errors' => 'Ha excedido el número máximo de registros por día'], 422);

        $user = new User();
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->email_verified_at = now();
        if(!$user->save()) { 
            $user->forceDelete();
            return response()->json(['errors' => 'No se pudo crear el Usuario del Cliente'], 422);
        }

        $client = new Client();
        $client->user_id = $user->id;
        if ($request->exists('names')) $client->names = $request->names;
        if ($request->exists('surnames')) $client->surnames = $request->surnames;
        if ($request->exists('phone_mobile')) $client->phone_mobile = $request->phone_mobile;
        if ($request->exists('first_nation')) $client->first_nationality_id = $request->first_nation;
        if ($request->exists('second_nation')) $client->second_nationality_id = $request->second_nation;

        // De verdad quisiera saber que fumada es esta y porque tengo que sufrir por ella
        $client->id_public = Str::random(32);


        if(!$client->save()) {
            $user->forceDelete();
            return response()->json(['errors' => 'No se pudo crear el Cliente'], 422);
        }

        $client->save();

        $client = Client::leftJoin('users', 'clients.user_id', '=', 'users.id')
            ->leftJoin('countries as first_nation', 'clients.first_nationality_id',  '=', 'first_nation.id')
            ->leftJoin('countries as second_nation', 'clients.second_nationality_id', '=', 'second_nation.id')
            ->select('clients.*', 'users.id as user_id', 'users.email as email', 'first_nation.name as first_nation_name', 'second_nation.name as second_nation_name')
            ->with('contracts')->find($client->id);
        return response()->json($client);
    }

    public function show($id) {
        if(Client::where('id', $id)->count() > 0) {
            $client = Client::leftJoin('users', 'clients.user_id', '=', 'users.id')->where('clients.id', $id)->first();
            return response()->json( $client , 200);
        }
        return response()->json(['error' => 'El Cliente no existe'], 422);
    }
    
    function update(Request $request, $id) {
        $client = Client::find($id);
        if(!$client) return response()->json(['errors' => 'El Cliente no existe'], 422);

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

            $user = User::find($client->user_id);
            $user->password = bcrypt($request->password);
            if(!$user->save()) return response()->json(['errors' => 'No se pudo actualizar la contraseña'], 422);

            return response()->json(['status' => 'success'], 200);
        }

        if($request->has('email')) {
            $user = User::find($client->user_id);
            $user->email = $request->email;
            $user->save();
        }

        if ($request->exists('names')) $client->names = $request->names;
        if ($request->exists('surnames')) $client->surnames = $request->surnames;
        if ($request->exists('phone_mobile')) $client->phone_mobile = $request->phone_mobile;
        if ($request->exists('first_nation')) $client->first_nationality_id = $request->first_nation;
        if ($request->exists('second_nation')) $client->second_nationality_id = $request->second_nation;

        $client->save();

        $client = Client::leftJoin('users', 'clients.user_id', '=', 'users.id')
            ->leftJoin('countries as first_nation', 'clients.first_nationality_id',  '=', 'first_nation.id')
            ->leftJoin('countries as second_nation', 'clients.second_nationality_id', '=', 'second_nation.id')
            ->select('clients.*', 'users.id as user_id', 'users.email as email', 'first_nation.name as first_nation_name', 'second_nation.name as second_nation_name')
            ->with('contracts')->find($client->id);
        return response()->json($client);
    }
    
    public function destroy($id) {
        $item = Client::find($id);
        if (!$item) return response()->json(['message' => 'Client not found'], 404);
        $item->delete();
        return response()->json(['message' => 'Client deleted successfully']);
    }
}
