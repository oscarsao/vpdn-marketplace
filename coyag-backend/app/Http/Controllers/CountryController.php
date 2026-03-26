<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $countries = Country::select('countries.id as id_country', 'countries.name as name_country', 'countries.iso as iso_code_country', 'countries.created_at as created_at_country', 'countries.updated_at as updated_at_country', 'continents.id as id_continent', 'continents.name as name_continent')
                    ->leftJoin('continents', 'continents.id', '=', 'countries.continent_id')
                            ->get();

        return response()->json(
        [
            'status' => 'success',
            'countries' => $countries->toArray()
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function show($idCountry)
    {
        if(Country::where('id', $idCountry)->count() > 0) {
            $country = Country::select('countries.id as id_country', 'countries.name as name_country', 'countries.created_at as created_at_country', 'countries.updated_at as updated_at_country', 'continents.id as id_continent', 'continents.name as name_continent')
                        ->leftJoin('continents', 'continents.id', '=', 'countries.continent_id')
                                ->where('countries.id', $idCountry)
                                ->first();

            return response()->json(
            [
                'status' => 'success',
                'country' => $country
            ], 200);
        }

        return response()->json(['error' => 'El País no existe'], 422);
    }

}
