<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use App\Notifications\ContactNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Notification;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contacts = Contact::leftJoin('employees', 'employees.id', '=', 'contacts.employee_id')
                            ->select('contacts.id as id_contact', 'contacts.name as name_contact', 'contacts.email as email_contact', 'contacts.message as message_contact', 'contacts.request_ip as request_ip_contact', 'contacts.flag_read as flag_read_contact', 'contacts.flag_answered as flag_answered_contact', 'contacts.created_at as created_at_contact', 'contacts.updated_at as updated_at_contact', 'employees.id as id_employee', 'employees.name as name_employee', 'employees.surname as surname_employees')
                            ->get();

        return response()->json([
            'status'        =>  'success',
            'contacts'      =>  $contacts->toArray()
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
        $validator = Validator::make($request->all(),[
            'name'      =>  'required',
            'email'     =>  'required|email',
            'message'   =>  'required'
        ],
        [
            'name.required'     =>  'El nombre es requerido',
            'email.required'    =>  'El email es requerido',
            'email.email'       =>  'El email tiene un formato inválido',
            'message.required'  =>  'El mensaje es requerido'
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $contact = new Contact();
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->message = $request->message;
        $contact->request_ip = $request->ip();
        $contact->save();

        $user = User::find(2);

        try {
            Notification::send($user, new ContactNotification($contact));
        } catch (Exception $e) {
            Log::error("Error - COYAG -> Excepción capturada en ContactController@store: $e");
        }


        /** Retorna de esta manera porque lo importante es que envíe un email de notificación a algún empleado
         * Es decir, no hay validación si se guarda o no el objeto contacto
         */
        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($idContact)
    {
        if(Contact::where('id', $idContact)->count() > 0)
        {
            $contact = Contact::leftJoin('employees', 'employees.id', '=', 'contacts.employee_id')
                                ->where('contacts.id', $idContact)
                                ->select('contacts.id as id_contact', 'contacts.name as name_contact', 'contacts.email as email_contact', 'contacts.message as message_contact', 'contacts.request_ip as request_ip_contact', 'contacts.flag_read as flag_read_contact', 'contacts.flag_answered as flag_answered_contact', 'contacts.created_at as created_at_contact', 'contacts.updated_at as updated_at_contact', 'employees.id as id_employee', 'employees.name as name_employee', 'employees.surname as surname_employees')
                                ->first();

            return response()->json([
                'status'        =>  'success',
                'contact'      =>  $contact
            ], 200);
        }

        return response()->json(['errors' => 'No existe el Registro de Contacto'], 422);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idContact)
    {
        if(Contact::where('id', $idContact)->count() > 0)
        {
            $contact = Contact::find($idContact);

            if($request->exists('flag_read')) $contact->flag_read = $request->flag_read;

            if($request->exists('flag_answered')) {
                $contact->flag_answered = $request->flag_answered;
                if($contact->flag_answered == false)
                    $contact->employee_id = Null;
                else
                    $contact->employee_id = Auth::user()->employee->id;
            }

            if($contact->save())
                return response()->json(['status' => 'success'], 200);

            return response()->json(['errors' => 'No se pudo actualizar el Registro de Contacto'], 422);
        }

        return response()->json(['errors' => 'No existe el Registro de Contacto'], 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($idContact)
    {
        if(Contact::where('id', $idContact)->count() > 0)
        {
            $contact = Contact::find($idContact);

            if($contact->delete())
                return response()->json(['status' => 'success'], 200);

            return response()->json(['errors' => 'No se pudo borrar el Registro de Contacto'], 422);
        }

        return response()->json(['errors' => 'No existe el Registro de Contacto'], 422);
    }
}
