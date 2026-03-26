<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\District;
use App\Models\Municipality;
use App\Models\Neighborhood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $districts;

        if(isset(Auth::user()->employee->id))
            $districts = District::select('id as id_district', 'name as name_district', 'rent_per_capita as rent_per_capita_district', 'demographic_data as demographic_data_district','municipality_id as id_municipality', 'created_at as created_at_district', 'updated_at as updated_at_district');
        else
            $districts = District::select('id as id_district', 'name as name_district','municipality_id as id_municipality', 'created_at as created_at_district', 'updated_at as updated_at_district');

        $districts = $districts->withCount(['businesses' => function ($query) {
            $query->where('flag_active', true)->where('business_type_id', 1);
        }, 'businesses as properties_count' => function ($query) {
            $query->where('flag_active', true)->where('business_type_id', 3);
        }])->get();

        return response()->json([
            'status'            =>  'success',
            'districts'         =>  $districts
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
            'municipality_id'       =>  'required|numeric',
            'name'                  =>  'required'
        ],
        [
            'municipality_id.required'          =>  'El ID del Municipio es requerido',
            'municipality_id.numeric'           =>  'El ID del Municipio debe ser del tipo numérico',
            'name.required'                     =>  'El Nombre del Distrito es requerido'
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $district = new District();

        $district->municipality_id = $request->municipality_id;

        $district->name = $request->name;

        if($request->exists('rent_per_capita'))
            $district->rent_per_capita = $request->rent_per_capita;

        if($request->exists('demographic_data'))
            $district->demographic_data = $request->demographic_data;

        if(Municipality::where('id', $district->municipality_id)->count() == 0)
            return response()->json(['errors' => 'El ID del Municipio no es válido'], 422);

        if($district->save())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors' => 'No se pudo guardar el Distrito'], 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\District  $district
     * @return \Illuminate\Http\Response
     */
    public function show($idDistrict)
    {
        if(District::where('id', $idDistrict)->count() > 0)
        {
            $district = District::where('id', $idDistrict);

            if(isset(Auth::user()->employee->id))
                $district = $district->select('id as id_district', 'name as name_district', 'rent_per_capita as rent_per_capita_district', 'demographic_data as demographic_data_district', 'municipality_id as id_municipality', 'created_at as created_at_district', 'updated_at as updated_at_district');
            else
                $district = $district->select('id as id_district', 'name as name_district', 'municipality_id as id_municipality', 'created_at as created_at_district', 'updated_at as updated_at_district');

            $district = $district->first();

            return response()->json([
            'status'            =>  'success',
            'district'         =>  $district
            ], 200);
        }

        return response()->json(['errors' => 'El Distrito no existe'], 422);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\District  $district
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idDistrict)
    {
        if(District::where('id', $idDistrict)->count() > 0)
        {
            $district = District::find($idDistrict);

            if($request->exists('name'))
                $district->name = $request->name;

            if($request->exists('rent_per_capita')) {
                if($request->rent_per_capita == 'null')
                    $district->rent_per_capita = null;
                else
                    $district->rent_per_capita = $request->rent_per_capita;
            }

            if($request->exists('demographic_data')) {
                if($request->demographic_data == 'null')
                    $district->demographic_data = null;
                else
                    $district->demographic_data = $request->demographic_data;
            }


            if($request->exists('municipality_id'))
            {
                $district->municipality_id = $request->municipality_id;

                if(Municipality::where('id', $district->municipality_id)->count() == 0)
                    return response()->json(['errors' => 'El ID del Municipio no es válido'], 422);
            }

            if($district->save())
                return response()->json(['status' => 'success'], 200);

            return response()->json(['errors' => 'No se pudo actualizar el Distrito'], 422);
        }

        return response()->json(['errors' => 'El Distrito no existe'], 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\District  $district
     * @return \Illuminate\Http\Response
     */
    public function destroy($idDistrict)
    {
        if(District::where('id', $idDistrict)->count() > 0)
        {
            $district = District::find($idDistrict);

            if(Business::where('district_id', $idDistrict)->count() > 0)
                return response()->json(['errors' => 'No se pudo borrar el Distrito porque tiene Negocios asociados'], 422);

            if(Neighborhood::where('district_id', $idDistrict)->count() > 0)
                return response()->json(['errors' => 'No se pudo borrar el Distrito porque tiene Barrios asociados'], 422);

            if($district->delete())
                return response()->json(['status' => 'success'], 200);

            return response()->json(['errors' => 'No se pudo borrar el Distrito'], 422);
        }

        return response()->json(['errors' => 'El Distrito no existe'], 422);
    }
}
