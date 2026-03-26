<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\District;
use App\Models\Neighborhood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NeighborhoodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $neighborhoods;

        if(isset(Auth::user()->employee->id))
            $neighborhoods = Neighborhood::select('id as id_neighborhood', 'name as name_neighborhood', 'rent_per_capita as rent_per_capita_neighborhood', 'demographic_data as demographic_data_neighborhood','district_id as id_district', 'created_at as created_at_neighborhood', 'updated_at as updated_at_neighborhood');
        else
            $neighborhoods = Neighborhood::select('id as id_neighborhood', 'name as name_neighborhood','district_id as id_district', 'created_at as created_at_neighborhood', 'updated_at as updated_at_neighborhood');

            $neighborhoods = $neighborhoods->withCount(['businesses' => function ($query) {
                $query->where('flag_active', true)->where('business_type_id', 1);
            }, 'businesses as properties_count' => function ($query) {
                $query->where('flag_active', true)->where('business_type_id', 3);
            }])->get();

        return response()->json([
            'status'            =>  'success',
            'neighborhoods'         =>  $neighborhoods
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
            'district_id'       =>  'required|numeric|exists:districts,id',
            'name'              =>  'required'
        ],
        [
            'district_id.required'          =>  'El ID del Distrito es requerido',
            'district_id.numeric'           =>  'El ID del Distrito debe ser del tipo numérico',
            'district_id.exists'            =>  'El ID del Distrito no existe en la BD',
            'name.required'                 =>  'El Nombre del Barrio es requerido'
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $neighborhood = new Neighborhood();

        $neighborhood->district_id = $request->district_id;

        $neighborhood->name = $request->name;

        if($request->exists('rent_per_capita'))
            $neighborhood->rent_per_capita = $request->rent_per_capita;

        if($request->exists('demographic_data'))
            $neighborhood->demographic_data = $request->demographic_data;

        if($neighborhood->save())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors' => 'No se pudo guardar el Barrio'], 422);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Neighborhood  $neighborhood
     * @return \Illuminate\Http\Response
     */
    public function show($idNeighborhood)
    {
        if(Neighborhood::where('id', $idNeighborhood)->count() > 0)
        {
            $neighborhood = Neighborhood::where('id', $idNeighborhood);

            if(isset(Auth::user()->employee->id))
                $neighborhood = $neighborhood->select('id as id_neighborhood', 'name as name_neighborhood', 'rent_per_capita as rent_per_capita_neighborhood', 'demographic_data as demographic_data_neighborhood','district_id as id_district', 'created_at as created_at_neighborhood', 'updated_at as updated_at_neighborhood');
            else
                $neighborhood = $neighborhood->select('id as id_neighborhood', 'name as name_neighborhood','district_id as id_district', 'created_at as created_at_neighborhood', 'updated_at as updated_at_neighborhood');

            $neighborhood = $neighborhood->first();

            return response()->json([
                'status'            =>  'success',
                'neighborhood'         =>  $neighborhood
            ], 200);
        }

        return response()->json(['errors' => 'El Barrio no existe'], 422);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Neighborhood  $neighborhood
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idNeighborhood)
    {
        if(Neighborhood::where('id', $idNeighborhood)->count() > 0)
        {
            $neighborhood = Neighborhood::find($idNeighborhood);

            if($request->exists('name'))
                $neighborhood->name = $request->name;

            if($request->exists('rent_per_capita')) {
                if($request->rent_per_capita == 'null')
                    $neighborhood->rent_per_capita = null;
                else
                    $neighborhood->rent_per_capita = $request->rent_per_capita;
            }

            if($request->exists('demographic_data')) {
                if($request->demographic_data == 'null')
                    $neighborhood->demographic_data = null;
                else
                    $neighborhood->demographic_data = $request->demographic_data;
            }

            if($request->exists('district_id'))
            {
                $neighborhood->district_id = $request->district_id;

                if(District::where('id', $neighborhood->district_id)->count() == 0)
                    return response()->json(['errors' => 'El ID del Distrito no es válido'], 422);
            }

            if($neighborhood->save())
                return response()->json(['status' => 'success'], 200);

            return response()->json(['errors' => 'No se pudo actualizar el Barrio'], 422);
        }

        return response()->json(['errors' => 'El Barrio no existe'], 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Neighborhood  $neighborhood
     * @return \Illuminate\Http\Response
     */
    public function destroy($idNeighborhood)
    {
        if(Neighborhood::where('id', $idNeighborhood)->count() > 0)
        {
            $neighborhood = Neighborhood::find($idNeighborhood);

            if(Business::where('neighborhood_id', $idNeighborhood)->count() > 0)
                return response()->json(['errors' => 'No se pudo borrar el Barrio porque tiene Negocios asociados'], 422);

            if($neighborhood->delete())
                return response()->json(['status' => 'success'], 200);

            return response()->json(['errors' => 'No se pudo borrar el Barrio'], 422);
        }

        return response()->json(['errors' => 'El Barrio no existe'], 422);
    }
}
