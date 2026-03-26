<?php

namespace App\Http\Controllers;

use App\Models\Headquarter;
use Illuminate\Http\Request;

class HeadquarterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $headquarters = Headquarter::leftJoin('countries', 'headquarters.country_id', '=', 'countries.id')
                            ->select('headquarters.id as id_headquarter', 'headquarters.name as name_headquarter', 'headquarters.address as address_headquarter', 'headquarters.city as city_headquarter', 'headquarters.created_at as created_at_headquarter', 'headquarters.updated_at as updated_at_headquarter', 'headquarters.deleted_at as deleted_at_headquarter', 'countries.id as id_country', 'countries.name as name_country')
                            ->get();



        return response()->json(
        [
            'status' => 'success',
            'headquarters' => $headquarters->toArray()
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Headquarter  $headquarter
     * @return \Illuminate\Http\Response
     */
    public function show($idHeadquarter)
    {
        if(Headquarter::where('id', $idHeadquarter)->count() > 0) {
            $headquarter = Headquarter::leftJoin('countries', 'headquarters.country_id', '=', 'countries.id')
                                ->where('headquarters.id', $idHeadquarter)
                                ->select('headquarters.id as id_headquarter', 'headquarters.name as name_headquarter', 'headquarters.address as address_headquarter', 'headquarters.city as city_headquarter', 'headquarters.created_at as created_at_headquarter', 'headquarters.updated_at as updated_at_headquarter', 'headquarters.deleted_at as deleted_at_headquarter', 'countries.id as id_country', 'countries.name as name_country')
                                ->first();



            return response()->json(
            [
                'status' => 'success',
                'headquarter' => $headquarter
            ], 200);
        }

        return response()->json(['error' => 'La sede no existe'], 422);

    }

}
