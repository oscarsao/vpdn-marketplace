<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    public function reset(Request $request)
    {

        $v = \Validator::make($request->all(), [
            'email'                 => 'required|email',
            'token'                 => 'required',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required|same:password',
        ],
        [
            'email.required'        =>  'El email es requerido',
            'email.email'           =>  'Debe ingresar un email válido',
            'token.required'        => 'El token es requerido.',
            'password.required'     => 'La contraseña es requerida.',
            'password.min'          => 'La longitud mínima de la contraseña es de 8 caracteres.',
            'password.confirmed'          => 'Las contraseñas no coinciden.',
            'password_confirmation.required'    => 'La confirmación de la contraseña es requerida.',
            'password_confirmation.same'        => 'La confirmación de la contraseña y la contraseña no coinciden.'
        ]);

        if ($v->fails())
        {
            $errors = $v->messages();
            return response()->json(['errors' => $errors], 422);
        }

        $tokenData = DB::table('password_resets')->where('email', $request->email)->first();

        if($tokenData) {
            $fechaActual = strtotime(date("d-m-Y H:i:00", time()));
            $fechaCalculada = strtotime($tokenData->created_at);
            if(($fechaActual - $fechaCalculada) > 3600)
                return response()->json(['error' => 'Token vencido'], 422);
        }
        else {
            return response()->json(['error' => 'El usuario no ha iniciado el proceso para Recuperar su Contraseña'], 422);
        }

        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        return $response == \Password::PASSWORD_RESET
                    ? response()->json(['status' => 'success'], 200)
                    : response()->json(['error' => 'PASSWORD_RESET'], 422);
    }

    protected function resetPassword($user, $password)
    {
        $user->forceFill([
            'password' => bcrypt($password),
            'remember_token' => str_random(60),
        ])->save();

        // Aquí se podría generar el token para autenticar de una vez al usuario
        // $this->guard()->login($user);
    }

}
