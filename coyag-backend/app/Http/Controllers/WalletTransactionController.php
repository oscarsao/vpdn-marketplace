<?php

namespace App\Http\Controllers;

use App\Models\WalletTransaction;
use App\Services\WalletTransactionService;
use Illuminate\Http\Request;

class WalletTransactionController extends Controller
{
    private $walletTransactionService;

    public function __construct() {
        $this->walletTransactionService = new WalletTransactionService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = $this->walletTransactionService->index($request);

        if($data->status == 'success')
            return response()->json([
                'status'        => $data->status,
                'transactions'  => $data->transactions
            ], $data->code);
        else
            return response()->json([
                'errors'   =>  $data->errors
            ], $data->code);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->walletTransactionService->store($request);

        if($data->status == 'success')
            return response()->json([
                'status'        => $data->status,
            ], $data->code);
        else
            return response()->json([
                'errors'   =>  $data->errors
            ], $data->code);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\WalletTransaction  $walletTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $idWalletTransaction)
    {
        $data = $this->walletTransactionService->show($request, $idWalletTransaction);

        if($data->status == 'success')
            return response()->json([
                'status'        => $data->status,
                'transaction'  => $data->transaction
            ], $data->code);
        else
            return response()->json([
                'errors'   =>  $data->errors
            ], $data->code);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\WalletTransaction  $walletTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idWalletTransaction)
    {
        $data = $this->walletTransactionService->update($request, $idWalletTransaction);

        if($data->status == 'success')
            return response()->json([
                'status'        => $data->status,
            ], $data->code);
        else
            return response()->json([
                'errors'   =>  $data->errors
            ], $data->code);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\WalletTransaction  $walletTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idWalletTransaction)
    {
        $data = $this->walletTransactionService->destroy($request, $idWalletTransaction);

        if($data->status == 'success')
            return response()->json([
                'status'        => $data->status,
            ], $data->code);
        else
            return response()->json([
                'errors'   =>  $data->errors
            ], $data->code);
    }

    public function getClientWalletTransactions(Request $request, $idClient)
    {
        $request->request->add(['client_id' => $idClient]);

        $data = $this->walletTransactionService->index($request);

        if($data->status == 'success')
            return response()->json([
                'status'        => $data->status,
                'transactions'  => $data->transactions
            ], $data->code);
        else
            return response()->json([
                'errors'   =>  $data->errors
            ], $data->code);
    }

    public function getClientTotalWalletAmount(Request $request, $idClient)
    {
        $request->request->add(['client_id' => $idClient]);

        $data = $this->walletTransactionService->getTotalAmount($request);

        if($data->status == 'success')
            return response()->json([
                'status'        => $data->status,
                'total_amount'  => $data->total_amount
            ], $data->code);
        else
            return response()->json([
                'errors'   =>  $data->errors
            ], $data->code);
    }
}
