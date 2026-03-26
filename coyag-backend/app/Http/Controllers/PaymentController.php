<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Employee;
use App\Models\File;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use jeremykenedy\LaravelRoles\Models\Role;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $payments = Payment::leftJoin('files', 'files.id', '=', 'payments.file_id')
                        ->leftJoin('clients', 'clients.id', '=', 'payments.client_id')
                        ->leftJoin('users', 'clients.user_id', '=', 'users.id')
                        ->leftJoin('employees', 'employees.id', '=', 'payments.employee_id');

        if($request->exists('client_id'))
            $payments = $payments->where('payments.client_id', $request->client_id);

        if($request->exists('employee_id'))
            $payments = $payments->where('payments.employee_id', $request->employee_id);

        $payments = $payments->select('payments.id as id_payment', 'users.id as id_user', 'payments.client_id as id_client', 'users.email as email_client', 'clients.names as names_client', 'clients.surnames as surnames_client', 'payments.employee_id as id_employee', 'employees.name as name_employee', 'employees.surname as surname_employee', 'payments.bank as bank_payment', 'payments.no_transaction as no_transaction_payment', 'payments.amount as amount_payment', 'payments.currency as currency_payment', 'payments.observation as observation_payment', 'payments.created_at as created_at_payment', 'payments.updated_at as updated_at_payment', 'files.id as file_id', 'files.full_path as full_path_file')
                                ->get();

        for($i = 0; $i < count($payments); $i++)
        {
            $clientR = Role::leftJoin('role_user', 'role_user.role_id', '=', 'roles.id')
                            ->where('role_user.user_id', $payments[$i]["id_user"])
                            ->select('role_user.role_id as id_role', 'roles.name as name_role')
                            ->get();

            $payments[$i]["roles"] = $clientR;

        }

        return response()->json(
        [
            'status' => 'success',
            'payments' => $payments
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
        $validator = Validator::make($request->all(), [
            'client_id'                 =>      'required|numeric|exists:clients,id',
            'file'                      =>      'required|mimes:jpg,jpeg,png,pdf,xlx,xlxs,doc,docs,csv|max:5120',
            'bank'                      =>      'required',
            'no_transaction'            =>      'required',
            'amount'                    =>      'required',
            'observation'               =>      'max:255',
            'currency'                  =>      'in:"ARS", "AUD", "BRL", "CAD", "CLP", "COP", "EUR", "MXN", "USD", "VES"'
        ],
        [
            'client_id.required'        =>      'Falta el ID del Cliente',
            'client_id.numeric'         =>      'El ID del Cliente debe ser numérico',
            'client_id.exists'          =>      'El Cliente no existe en la BD',
            'file.required'             =>      'Falta el archivo',
            'file.mimes'                =>      'El tipo MIME no es soportado',
            'file.max'                  =>      'El tamaño máximo del archivo debe ser menor a 5MB',
            'bank.required'             =>      'Falta el banco',
            'no_transaction.required'   =>      'Falta el Nro de transacción',
            'amount.required'           =>      'Falta el monto',
            'observation.max'           =>      'La Observación no debe pasar de 255 caracteres',
            'currency.in'               =>      'Moneda (Currency) solo acepta: ARS, AUD, BRL, CAD, CLP, COP, EUR, MXN, USD o VES'
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $auxFile = $request->file;

        if(!in_array(strtolower($auxFile->extension()), ['jpg', 'jpeg', 'png', 'pdf', 'xlx', 'xlxs', 'doc', 'docs', 'csv'])) {
            //Esto porque el tipo MIME es la estructura interna del archivo, en cambio aquí se valida la extensión
            return response()->json(['errors' => 'La extensión no es soportada'], 422);
        }

        $employee = Employee::where("user_id", Auth::user()->id)->first();
        $client = Client::find($request->client_id);

        $auxPath = "files/payment/$client->id";
        $path = public_path($auxPath);

        if (!is_dir($path)) {
            Storage::makeDirectory($path);
        }

        $file = new File();

        $file->client_id        = $client->id;
        $file->employee_id      = $employee->id;
        $file->original_name    = $auxFile->getClientOriginalName();
        $file->extension        = $auxFile->extension();
        $auxName                = Str::random(12). '.' . $auxFile->extension();
        $file->size             = $auxFile->getSize();
        $file->mime_type        = $auxFile->getMimeType();
        $file->path             = $auxPath;
        $file->full_path        = $auxPath.'/'.$auxName;

        if(!$file->save()) {
            return response()->json(['errors' => 'No se guardó el archivo'], 422);
        }

        $payment                    = new Payment();
        $payment->client_id         = $client->id;
        $payment->employee_id       = $employee->id;
        $payment->file_id           = $file->id;
        $payment->bank             = $request->bank;
        $payment->no_transaction   = $request->no_transaction;
        $payment->amount             = $request->amount;
        if($request->exists('observation')) $payment->observation = $request->observation;
        if($request->exists('currency')) $payment->currency = $request->currency;

        if(!$payment->save()) {
            $file->delete();
            return response()->json(['errors' => 'No se guardó el pago'], 422);
        }

        addClientTimeline($client->id, $employee->id, "Payment", "create", false);
        notificationPagoCargado($client, $payment);

        $auxFile->move($path, $auxName);

        return response()->json([
            'status' => 'success',
            'id_payment'    =>  $payment->id
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show($idPayment)
    {
        if(Payment::where('id', $idPayment)->count() > 0)
        {
            $payment = Payment::leftJoin('files', 'files.id', '=', 'payments.file_id')
                        ->leftJoin('clients', 'clients.id', '=', 'payments.client_id')
                        ->leftJoin('users', 'clients.user_id', '=', 'users.id')
                        ->leftJoin('employees', 'employees.id', '=', 'payments.employee_id')
                        ->where('payments.id', $idPayment)
                        ->select('payments.id as id_payment', 'users.id as id_user', 'payments.client_id as id_client', 'users.email as email_client', 'clients.names as names_client', 'clients.surnames as surnames_client', 'payments.employee_id as id_employee', 'employees.name as name_employee', 'employees.surname as surname_employee', 'payments.bank as bank_payment', 'payments.no_transaction as no_transaction_payment', 'payments.amount as amount_payment', 'payments.currency as currency_payment', 'payments.observation as observation_payment', 'payments.created_at as created_at_payment', 'payments.updated_at as updated_at_payment', 'files.id as file_id', 'files.full_path as full_path_file')
                        ->first();

            $clientR = Role::leftJoin('role_user', 'role_user.role_id', '=', 'roles.id')
                            ->where('role_user.user_id', $payment["id_user"])
                            ->select('role_user.role_id as id_role', 'roles.name as name_role')
                            ->get();

            $payment["roles"] = $clientR;

            return response()->json(
            [
                'status' => 'success',
                'payment' => $payment
            ], 200);
        }

        return response()->json(['errors' => 'El Pago no existe'], 422);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idPayment)
    {
        /*
         * Este método solo edita el pago, no el archivo
         * A diferencia de coyag, update se separa en 2 métodos:
         * update, actualiza datos propios del pago
         * updateFilePayment, actualiza el soporte o archivo del pago
         */

        if(Payment::where('id', $idPayment)->count() > 0)
        {
            $validator = Validator::make($request->all(), [
                'observation'             =>      'max:255',
                'currency'                =>      'in:"ARS", "AUD", "BRL", "CAD", "CLP", "COP", "EUR", "MXN", "USD", "VES"'
            ],
            [
                'observation.max'           =>      'La Observación no debe pasar de 255 caracteres',
                'currency.in'               =>      'Moneda (Currency) solo acepta: ARS, AUD, BRL, CAD, CLP, COP, EUR, MXN, USD o VES'
            ]);

            if($validator->fails())
                return response()->json(['errors' => $validator->errors()], 422);

            $payment = Payment::find($idPayment);

            if($request->exists('bank')) $payment->bank = $request->bank;
            if($request->exists('no_transaction')) $payment->no_transaction = $request->no_transaction;
            if($request->exists('amount')) $payment->amount = $request->amount;
            if($request->exists('observation')) $payment->observation = $request->observation;
            if($request->exists('currency')) $payment->currency = $request->currency;

            $payment->save();

            addClientTimeline($payment->client_id, Auth::user()->employee->id, "Payment", "update", false);
            $client = Client::find($payment->client_id);
            notificationPagoCargado($client, $payment, true);

            return response()->json(['status' => 'success'], 200);
        }

        return response()->json(['errors' => 'El Pago no existe'], 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy($idPayment)
    {
        // Por ahora no se podrá borrar un pago
        return response()->json(['errors' => 'Por ahora el borrado de pagos está suspendido'], 422);
        /*
        if(Payment::where('id', $idPayment)->count() > 0)
        {

            //No se borra el archivo porque es un borrado lógico
            $payment = Payment::find($idPayment);

            $payment->file()->delete();

            addClientTimeline($payment->client_id, Auth::user()->employee->id, "Payment", "delete", false);

            $payment->delete();

            return response()->json(['status' => 'success'], 200);
        }

        return response()->json(['errors' => 'El Pago no existe'], 422);
        */
    }

    public function updateFilePayment(Request $request, $idPayment)
    {
        if(Payment::where('id', $idPayment)->count() > 0)
        {

            $validator = Validator::make($request->all(), [
                'file'                      =>      'required|mimes:jpg,jpeg,png,pdf,xls,xlsx,doc,docx,csv|max:5120'
            ],
            [
                'file.required'             =>      'Falta el archivo',
                'file.mimes'                =>      'El tipo MIME no es soportado',
                'file.max'                  =>      'El tamaño máximo del archivo debe ser menor a 5MB'
            ]);

            if($validator->fails())
                return response(['errors' => $validator->errors()], 422);

            $payment = Payment::find($idPayment);
            $file = File::where('files.id', '=', $payment->file_id)->first();

            $auxFile = $request->file;

            if(!in_array(strtolower($auxFile->extension()), ['jpg', 'jpeg', 'png', 'pdf', 'xlx', 'xlxs', 'doc', 'docs', 'csv'])) {
                //Esto porque el tipo MIME es la estructura interna del archivo, en cambio aquí se valida la extensión
                return response()->json(['errors' => 'La extensión no es soportada'], 422);
            }

            $auxPath = "files/payment/$payment->client_id";
            $path = public_path($auxPath);

            if (!is_dir($path)) {
                Storage::makeDirectory($path);
            }


            $file2 = new File();

            $file2->client_id        = $file->client_id;
            $file2->employee_id      = Auth::user()->employee->id;
            $file2->original_name    = $auxFile->getClientOriginalName();
            $file2->extension        = $auxFile->extension();
            $auxName                 = Str::random(12). '.' . $auxFile->extension();
            $file2->size             = $auxFile->getSize();
            $file2->mime_type        = $auxFile->getMimeType();
            $file2->path             = $auxPath;
            $file2->full_path        = $auxPath.'/'.$auxName;

            if(!$file2->save()) {
                return response()->json(['errors' => 'No se guardó el archivo'], 422);
            }

            $auxFile->move($path, $auxName); //Se guarda el archivo en disco

            $file->delete(); //Borra lógicamente el archivo actual, el archivo se mantiene
            $payment->file_id = $file2->id;
            $payment->save();

            addClientTimeline($payment->client_id, Auth::user()->employee->id, "Payment", "update", false);
            notificationPagoCargado(Client::find($payment->client_id), $payment, true);

            return response()->json(['status' => 'success'], 200);  //retorna porque solo actualiza el archivo

        }

        return response()->json(['errors' => 'El Pago no existe'], 422);
    }

    /* --------------- Pagos de un Cliente--------------- */

    public function paymentIndexClient()
    {
        $payments = Payment::leftJoin('employees', 'employees.id', '=', 'payments.employee_id')
                            ->where('payments.client_id', Auth::user()->client->id)
                            ->select('payments.id as id_payment', 'payments.employee_id as id_employee', 'employees.name as name_employee', 'employees.surname as surname_employee', 'payments.bank as bank_payment', 'payments.no_transaction as no_transaction_payment', 'payments.amount as amount_payment', 'payments.currency as currency_payment', 'payments.observation as observation_payment', 'payments.created_at as created_at_payment', 'payments.updated_at as updated_at_payment')
                            ->get();

        return response()->json(
        [
            'status' => 'success',
            'payments' => $payments
        ], 200);
    }

    public function paymentShowClient($idPayment)
    {

        if(Payment::where('id', $idPayment)->count() == 0)
            return response()->json(['errors'   =>  'El pago no existe'], 422);

        $payment = Payment::leftJoin('employees', 'employees.id', '=', 'payments.employee_id')
                        ->where('payments.client_id', Auth::user()->client->id)
                        ->where('payments.id', $idPayment)
                        ->select('payments.id as id_payment', 'payments.employee_id as id_employee', 'employees.name as name_employee', 'employees.surname as surname_employee', 'payments.bank as bank_payment', 'payments.no_transaction as no_transaction_payment', 'payments.amount as amount_payment', 'payments.currency as currency_payment', 'payments.observation as observation_payment', 'payments.created_at as created_at_payment', 'payments.updated_at as updated_at_payment')
                        ->first();

        return response()->json(
        [
            'status' => 'success',
            'payment' => $payment
        ], 200);
    }

    public function paymentsWithoutAssociatedService(Request $request)
    {
        $paymentsWithoutAssociatedService = Payment::leftJoin('added_service_payment', 'added_service_payment.payment_id', '=', 'payments.id')
                                                ->leftJoin('files', 'files.id', '=', 'payments.file_id')
                                                ->leftJoin('clients', 'clients.id', '=', 'payments.client_id')
                                                ->leftJoin('users', 'users.id', '=', 'clients.user_id')
                                                ->leftJoin('employees', 'employees.id', '=', 'payments.employee_id')
                                                ->where('added_service_payment.payment_id', null);

        if($request->exists('client_id')){
            $paymentsWithoutAssociatedService = $paymentsWithoutAssociatedService->where('payments.client_id', $request->client_id);
        }


        $paymentsWithoutAssociatedService = $paymentsWithoutAssociatedService->select('payments.id as id_payment', 'payments.bank as bank_payment', 'payments.no_transaction as no_transaction_payment', 'payments.amount as amount_payment', 'payments.observation as observation_payment', 'payments.currency as currency_payment', 'payments.created_at as created_at_payment', 'clients.id as id_client', DB::raw('CONCAT(clients.names, " ", clients.surnames) as full_name_client'), 'users.email as email_client', 'employees.id as id_employee', DB::raw('CONCAT(employees.name, " ", employees.surname) as full_name_employee'), 'files.id as file_id', 'files.full_path as full_path_file')
                                                ->get();

        return response()->json(
        [
            'status' => 'success',
            'paymentsWithoutAssociatedService' => $paymentsWithoutAssociatedService
        ], 200);
    }

}
