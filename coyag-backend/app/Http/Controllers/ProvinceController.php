<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Municipality;
use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $provinces = Province::select('id as id_province', 'name as name_province', 'autonomous_community_id as id_autonomous_community', 'created_at as created_at_province', 'updated_at as updated_at_province', 'deleted_at as deleted_at_province') ->get();
        
        return response()->json([
            'status' => 'success',
            'provinces' => $provinces->toArray()
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Province  $province
     * @return \Illuminate\Http\Response
     */
    public function show($idProvince)
    {
        if(Province::where('id', $idProvince)->count() > 0) {
            $province = Province::select('id as id_province', 'name as name_province', 'autonomous_community_id as id_autonomous_community', 'created_at as created_at_province', 'updated_at as updated_at_province', 'deleted_at as deleted_at_province')
                        ->where('id', $idProvince)
                        ->first();

            return response()->json(
                [
                    'status' => 'success',
                    'province' => $province
                ], 200);
        }

        return response()->json(['errors' => 'La Provincia no existe'], 422);
    }

    /*
     *  Obtiene todos los municipios de una Provincia
     */
    public function municipalities($idProvince)
    {
        if(Province::where('id', $idProvince)->count() > 0)
        {
            $municipalities = Municipality::leftJoin('provinces', 'provinces.id', '=', 'municipalities.province_id')
                                ->leftJoin('autonomous_communities', 'autonomous_communities.id', '=', 'provinces.autonomous_community_id')
                                ->where('provinces.id', $idProvince)
                                ->select('municipalities.id as id_municipality', 'municipalities.name as name_municipality', 'municipalities.flag_city as flag_city_municipality', 'provinces.id as id_province', 'provinces.name as name_province', 'autonomous_communities.id as id_autonomous_community', 'autonomous_communities.name as name_autonomous_community')
                                ->get();

            return response()->json(
                [
                    'status'            => 'success',
                    'municipalities'    => $municipalities
                ], 200);
        }

        return response()->json(['errors' => 'La Provincia no existe'], 422);
    }

    /*
     *  Obtiene todos los distritos de una Provincia
     */

    public function districts($idProvince)
    {
        if(Province::where('id', $idProvince)->count() > 0)
        {
            $districts = District::leftJoin('municipalities', 'municipalities.id', '=', 'districts.municipality_id')
                            ->leftJoin('provinces', 'provinces.id', '=', 'municipalities.province_id')
                            ->leftJoin('autonomous_communities', 'autonomous_communities.id', '=', 'provinces.autonomous_community_id')
                            ->where('provinces.id', $idProvince)
                            ->select('districts.id as id_district', 'districts.name as name_district', 'municipalities.id as id_municipality', 'municipalities.name as name_municipality','municipalities.flag_city as flag_city_municipality', 'provinces.id as id_province', 'provinces.name as name_province', 'autonomous_communities.id as id_autonomous_community', 'autonomous_communities.name as name_autonomous_community')
                            ->get();

            return response()->json(
                [
                    'status' => 'success',
                    'districts' => $districts
                ], 200);
        }

        return response()->json(['errors' => 'La Provincia no existe'], 422);
    }
}
