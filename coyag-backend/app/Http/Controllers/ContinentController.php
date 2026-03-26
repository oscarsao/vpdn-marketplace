<?php

namespace App\Http\Controllers;

use App\Models\Continent;
use Illuminate\Http\Request;

class ContinentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $continents = Continent::select('id as id_continent', 'name as name_continent', 'created_at as created_at_continent', 'updated_at as updated_at_continent')
                            ->get();

        return response()->json(
        [
            'status' => 'success',
            'continents' => $continents
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Continent  $continent
     * @return \Illuminate\Http\Response
     */
    public function show($idContinent)
    {

        if(Continent::where('id', $idContinent)->count() > 0) {

            $continent = Continent::select('id as id_continent', 'name as name_continent', 'created_at as created_at_continent', 'updated_at as updated_at_continent')
                        ->where('id', $idContinent)
                        ->first();

            return response()->json(
            [
                'status' => 'success',
                'continent' => $continent
            ], 200);

        }

        return response()->json(['error' => 'El Continente no existe'], 422);
    }
}
