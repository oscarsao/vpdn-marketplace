<?php

namespace App\Http\Controllers;

use App\Models\AutonomousCommunity;
use App\Models\District;
use App\Models\Province;
use App\Models\Municipality;
use Illuminate\Http\Request;

class AutonomousCommunityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $autonomousCommunities = AutonomousCommunity::select('id as id_autonomous_community', 'name as name_autonomous_community', 'created_at as created_at_autonomous_community', 'updated_at as updated_at_autonomous_community', 'deleted_at as deleted_at as deleted_at_autonomous_community')
                                    ->get();

        return response()->json([
            'status'    =>  'success',
            'autonomousCommunities' =>  $autonomousCommunities->toArray()
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AutonomousCommunity  $autonomousCommunity
     * @return \Illuminate\Http\Response
     */
    public function show($idAutonomousCommunity)
    {
        if(AutonomousCommunity::where('id', $idAutonomousCommunity)->count() > 0)
        {

            $autonomousCommunity = AutonomousCommunity::select('id as id_autonomous_community', 'name as name_autonomous_community', 'created_at as created_at_autonomous_community', 'updated_at as updated_at_autonomous_community', 'deleted_at as deleted_at as deleted_at_autonomous_community')
                                                            ->where('id', $idAutonomousCommunity)
                                                            ->first();

            return response()->json([
                'status'    =>  'success',
                'autonomousCommunity' =>  $autonomousCommunity
            ], 200);

        }

        return response()->json(['errors' => 'La Comunidad Autónoma no existe'], 422);
    }

    /*
     *  Obtiene todas las provincias de una Comunidad autónoma
     */
    public function provinces($idAutonomousCommunity)
    {
        if(AutonomousCommunity::where('id', $idAutonomousCommunity)->count() > 0)
        {
            $provinces = Province::leftJoin('autonomous_communities', 'autonomous_communities.id', '=', 'provinces.autonomous_community_id')
                                ->where('autonomous_communities.id', $idAutonomousCommunity)
                                ->select('provinces.id as id_province', 'provinces.name as name_province', 'autonomous_communities.id as id_autonomous_community', 'autonomous_communities.name as name_autonomous_community')
                                ->get();


            return response()->json(
                [
                    'status' => 'success',
                    'provinces' => $provinces->toArray()
                ], 200);
        }

        return response()->json(['errors' => 'La Comunidad Autónoma no existe'], 422);
    }

    /*
     *  Obtiene todas los municipios de una Comunidad autónoma
     */
    public function municipalities($idAutonomousCommunity)
    {
        if(AutonomousCommunity::where('id', $idAutonomousCommunity)->count() > 0)
        {
            $municipalities = Municipality::leftJoin('provinces', 'provinces.id', '=', 'municipalities.province_id')
                            ->leftJoin('autonomous_communities', 'autonomous_communities.id', '=', 'provinces.autonomous_community_id')
                            ->where('autonomous_communities.id', $idAutonomousCommunity)
                            ->select('municipalities.id as id_municipality', 'municipalities.name as name_municipality','municipalities.flag_city as flag_city_municipality', 'provinces.id as id_province', 'provinces.name as name_province', 'autonomous_communities.id as id_autonomous_community', 'autonomous_communities.name as name_autonomous_community')
                            ->get();

            return response()->json(
                [
                    'status' => 'success',
                    'municipalities' => $municipalities
                ], 200);
        }

        return response()->json(['errors' => 'La Comunidad Autónoma no existe'], 422);
    }

    /*
     *  Obtiene todos los distritos de una Comunidad autónoma
     */

    public function districts($idAutonomousCommunity)
    {
        if(AutonomousCommunity::where('id', $idAutonomousCommunity)->count() > 0)
        {
            $districts = District::leftJoin('municipalities', 'municipalities.id', '=', 'districts.municipality_id')
                            ->leftJoin('provinces', 'provinces.id', '=', 'municipalities.province_id')
                            ->leftJoin('autonomous_communities', 'autonomous_communities.id', '=', 'provinces.autonomous_community_id')
                            ->where('autonomous_communities.id', $idAutonomousCommunity)
                            ->select('districts.id as id_district', 'districts.name as name_district','municipalities.id as id_municipality', 'municipalities.name as name_municipality','municipalities.flag_city as flag_city_municipality', 'provinces.id as id_province', 'provinces.name as name_province', 'autonomous_communities.id as id_autonomous_community', 'autonomous_communities.name as name_autonomous_community')
                            ->get();

            return response()->json(
                [
                    'status' => 'success',
                    'districts' => $districts
                ], 200);
        }

        return response()->json(['errors' => 'La Comunidad Autónoma no existe'], 422);
    }

}
