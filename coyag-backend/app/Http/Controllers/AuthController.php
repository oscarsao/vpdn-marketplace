<?php

namespace App\Http\Controllers;

use App\Models\AddedService;
use App\Models\Client;
use App\Models\Employee;
use App\Models\User;
use App\Traits\ClientTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request)  {
        $user = User::where('email', $request->input('email'))->first();
        if(!$user) return response()->json(['error' => 'login_error'], 404);
        $credentials = $request->only('email', 'password');
        if ($token = $this->guard()->attempt($credentials)) {
            if($user->client?->id) addClientTimeline($user->client->id, 1, 'Auth', 'create', true);
            return response()->json(['status' => 'success', 'Authorization' => $token], 200)->header('Authorization', $token);
        }
        return response()->json(['error' => 'login_error'], 401);
    }

    public function logout()  {
        $clientId = Auth::user()->client?->id;

        $this->guard()->logout();

        if($clientId)
            addClientTimeline($clientId, 1, 'Auth', 'delete', true);

        return response()->json([
            'status' => 'success',
            'msg' => 'Logged out exitoso.'
        ], 200);
    }

    public function user(Request $request) {
        $user = DB::table('users');

        if(Employee::where('user_id', Auth::user()->id)->first()){
            $user = $user->leftJoin('employees', 'users.id', '=', 'employees.user_id')
                        ->select('users.id as id', 'email', 'employees.name as name', 'surname', 'employees.id as employee_id');
        }
        else {
            $user = $user->leftJoin('clients', 'users.id', '=', 'clients.user_id')
                        ->select('users.id as id', 'email', 'names as name', 'surnames as surname', 'clients.id as client_id', DB::raw("CASE WHEN users.observation_flag_login LIKE 'Email sin verificar%' THEN 0 ELSE 1 END as verified_email_user"));
        }

        $user = $user->where('users.id', '=', Auth::user()->id)->first();

        $roles =    DB::table('users')
                        ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                        ->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
                        ->where('users.id', '=', Auth::user()->id)
                        ->select('role_user.role_id as id_role', 'roles.name as name_role', 'roles.slug as slug_role')
                        ->get();

        return response()->json([
            'status'    => 'success',
            'data'      => $user,
            'roles'     => $roles
        ]);
    }

    public function refresh() {
        if ($token = $this->guard()->refresh()) {
            return response()
                ->json(['status' => 'successs', 'Authorization' => $token], 200)
                ->header('Authorization', $token);
        }
        return response()->json(['error' => 'refresh_token_error'], 401);
    }
    
    public function me() {
        return response()->json(auth()->user());
    }

    public function payload() {
        return response()->json(auth()->payload());
    }

    private function guard() {
        return Auth::guard();
    }

}
