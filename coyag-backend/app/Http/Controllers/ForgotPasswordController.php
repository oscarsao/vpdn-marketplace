<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{

    use SendsPasswordResetEmails;

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'email'    => 'required|email',
        ],
        [
            'email.required'    =>  'El email es requerido',
            'email.email'       =>  'Debe ingresar un email válido'
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        if(User::where('email', $request->email)->count() == 0)
            return response()->json(['status' => 'success'], 200);

        /*
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );
        */

        $response = '';

        $user = User::where('email', $request->email)->first();
        $token = app(\Illuminate\Auth\Passwords\PasswordBroker::class)->createToken($user);

        try {
            Mail::to($user->email)->send(new ResetPasswordMail($request->frontend, $token));
        } catch(Exception $e) {
            Log::error("Error - ForgotPasswordController - COYAG -> $e");
        }

        switch ($response) {
            case \Password::INVALID_USER:
                return response()->json(['error' => 'INVALID_USER'], 400);
                break;

            case \Password::INVALID_TOKEN:
                return response()->json(['error' => 'INVALID_TOKEN'], 422);
                break;

            default:
                return response()->json(['status' => 'success'], 200);
        }
    }
}
