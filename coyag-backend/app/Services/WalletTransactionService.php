<?php

namespace App\Services;

use \stdClass;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WalletTransactionService
{
    public function index(Request $request)
    {
        $data = new stdClass();
        $data->code     = 200;
        $data->status   = 'success';

        $transactions = WalletTransaction::leftJoin('clients', 'clients.id', '=', 'wallet_transactions.client_id')
                                            ->leftJoin('employees', 'employees.id', '=', 'wallet_transactions.employee_id');

        if($request->exists('client_id'))
            $transactions = $transactions->where('wallet_transactions.client_id', $request->client_id);

        $transactions = $transactions->select('wallet_transactions.id as id_transaction', 'wallet_transactions.title as title_transaction', 'wallet_transactions.description as description_transaction', 'wallet_transactions.amount as amount_transaction', 'wallet_transactions.created_at as created_at_transaction', 'clients.id as id_client', DB::raw('CONCAT(clients.names, " ", clients.surnames) as full_name_client'), 'employees.id as id_employee', DB::raw('CONCAT(employees.name, " ", employees.surname) as full_name_employee'))
                                        ->get();

        $data->transactions = $transactions;


        return $data;
    }

    public function store(Request $request)
    {
        $data = new stdClass();
        $data->code     = 422;
        $data->status   = 'errors';

        $validator = Validator::make($request->all(), [
            'client_id'         =>  'required|exists:clients,id',
            'title'             =>  'required|max:100',
            'description'       =>  'max:255',
            'amount'            =>  'required|numeric',
        ],
        [
            'client_id.required'    =>  'El ID del Cliente es Requerido',
            'client_id.exists'      =>  'El ID del Cliente no Existe',
            'title.required'        =>  'El Título es Requerido',
            'title.max'             =>  'La Longitud del título no puede exceder los 100 caracteres',
            'description.max'       =>  'La Longitud de la Observación no puede exceder los 255 caracteres',
            'amount.required'       =>  'El Monto es Requerido',
            'amount.numeric'        =>  'El Monto debe ser numérico',
        ]);

        if ($validator->fails())
        {
            $data->errors   = $validator->errors();
            return $data;
        }


        $transaction = new WalletTransaction();

        $transaction->client_id = $request->client_id;
        $transaction->title = $request->title;
        if($request->exists('description'))
            $transaction->description = $request->description;
        $transaction->amount = $request->amount;
        $transaction->employee_id = Auth::user()->employee->id;

        if($transaction->save())
        {
            $data->code     = 200;
            $data->status   = 'success';
            $data->transaction = $transaction;
            return $data;
        }

        $data->errors   = ['transaction' => ['No se pudo crear el Wallet Transaction']];
        return $data;
    }

    public function show (Request $request, $idWalletTransaction)
    {
        $transaction = WalletTransaction::leftJoin('clients', 'clients.id', '=', 'wallet_transactions.client_id')
                                ->leftJoin('employees', 'employees.id', '=', 'wallet_transactions.employee_id')
                                ->where('wallet_transactions.id', $idWalletTransaction)
                                ->select('wallet_transactions.id as id_transaction', 'wallet_transactions.title as title_transaction', 'wallet_transactions.description as description_transaction', 'wallet_transactions.amount as amount_transaction', 'wallet_transactions.created_at as created_at_transaction', 'clients.id as id_client', DB::raw('CONCAT(clients.names, " ", clients.surnames) as full_name_client'), 'employees.id as id_employee', DB::raw('CONCAT(employees.name, " ", employees.surname) as full_name_employee'))
                                ->first();

        $data = new stdClass();

        if(!$transaction)
        {
            $data->code     = 422;
            $data->status   = 'errors';
            $data->errors   = ['transaction' => ['Wallet Transaction no existe']];
            return $data;
        }

        $data->code     = 200;
        $data->status   = 'success';
        $data->transaction = $transaction;

        return $data;
    }

    public function update(Request $request, $idWalletTransaction)
    {
        $data = new stdClass();
        $data->code     = 422;
        $data->status   = 'errors';

        $validator = Validator::make($request->all(), [
            'title'             =>  'max:100',
            'description'       =>  'max:255',
            'amount'            =>  'numeric',
        ],
        [
            'title.max'             =>  'La Longitud del título no puede exceder los 100 caracteres',
            'description.max'       =>  'La Longitud de la Observación no puede exceder los 255 caracteres',
            'amount.numeric'        =>  'El Monto debe ser numérico',
        ]);

        if ($validator->fails())
        {
            $data->errors   = $validator->errors();
            return $data;
        }

        $transaction = WalletTransaction::find($idWalletTransaction);

        if(!$transaction)
        {
            $data->errors   = ['transaction' => ['Wallet Transaction no existe']];
            return $data;
        }

        if($request->exists('title') && $request->title != '')
            $transaction->title = $request->title;

        if($request->exists('description'))
            $transaction->description = $request->description == '' ? null
                                                                    : $request->description;

        if($request->exists('amount') && $request->amount != '')
            $transaction->amount = $request->amount;

        $transaction->employee_id = Auth::user()->employee->id;

        if($transaction->save())
        {
            $data->code     = 200;
            $data->status   = 'success';
            $data->transaction = $transaction;
            return $data;
        }

        $data->errors   = ['transaction' => ['No se pudo crear el Wallet Transaction']];
        return $data;
    }

    public function destroy(Request $request, $idWalletTransaction)
    {
        $data = new stdClass();
        $data->code     = 422;
        $data->status   = 'errors';

        $transaction = WalletTransaction::find($idWalletTransaction);

        if(!$transaction)
        {
            $data->errors   = ['transaction' => ['Wallet Transaction no existe']];
            return $data;
        }

        if($transaction->delete())
        {
            $data->code     = 200;
            $data->status   = 'success';
            return $data;
        }

        $data->errors   = ['transaction' => ['No se pudo Borrar el Wallet Transaction']];
        return $data;
    }

    public function getTotalAmount(Request $request)
    {

        $data = $this->index($request);

        $totalAmount = $data->transactions->sum('amount_transaction');

        $data = new stdClass();
        $data->code     = 200;
        $data->status   = 'success';
        $data->total_amount = $totalAmount;

        return $data;
    }

}
