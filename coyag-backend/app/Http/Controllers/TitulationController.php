<?php

namespace App\Http\Controllers;

use App\Models\Titulation;
use Illuminate\Http\Request;

class TitulationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $titulations = Titulation::select('id as id_titulation', 'name as name_titulation', 'created_at as created_at_titulation', 'updated_at as updated_at_titulation', 'deleted_at as deleted_at_titulation')
                            ->get();

        return response()->json(
            [
                'status' => 'success',
                'titulations' => $titulations->toArray()
            ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Titulation  $titulation
     * @return \Illuminate\Http\Response
     */
    public function show($idTitulation)
    {
        if(Titulation::where('id', $idTitulation)->count() > 0) {
            $titulation = Titulation::select('id as id_titulation', 'name as name_titulation', 'created_at as created_at_titulation', 'updated_at as updated_at_titulation', 'deleted_at as deleted_at_titulation')
                                ->where('id', $idTitulation)
                                ->first();

            return response()->json(
            [
                'status' => 'success',
                'titulation' => $titulation
            ], 200);
        }

        return response()->json(['error' => 'La Titulación no existe'], 422);
    }

}
