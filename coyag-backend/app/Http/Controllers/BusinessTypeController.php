<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BusinessTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $businessTypes = BusinessType::select('id as id_business_type', 'name as name_business_type', 'created_at as created_at_business_type', 'updated_at as updated_at_business_type', 'deleted_at as deleted_at_business_type')
                            ->get();

        return response()->json([
            'status' => 'success',
            'businessTypes'    => $businessTypes->toArray()
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
            'name'      =>      'required|unique:business_types'
        ],
        [
            'name.required'     =>      'El nombre es requerido',
            'name.unique'       =>      'El nombre del Tipo de Negocio debe ser único'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'   =>  $validator->errors()], 422);
        }

        $businessType = new BusinessType();
        $businessType->name = $request->input('name');

        if($businessType->save()) {
            return response()->json(['status' => 'success'], 200);
        }

        return response()->json(['errors' => 'No se guardó el tipo de negocio'], 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BusinessType  $businessType
     * @return \Illuminate\Http\Response
     */
    public function show($idBusinessType)
    {
        if(BusinessType::where('id', $idBusinessType)->count() > 0) {

            $businessType = BusinessType::select('id as id_business_type', 'name as name_business_type', 'created_at as created_at_business_type', 'updated_at as updated_at_business_type', 'deleted_at as deleted_at_business_type')
                            ->where('id', $idBusinessType)
                            ->first();

            return response()->json([
                'status' => 'success',
                'businessType'    => $businessType
            ], 200);

        }

        return response()->json(['errors' => 'El Tipo de Negocio no existe'], 422);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BusinessType  $businessType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idBusinessType)
    {
        if(BusinessType::where('id', $idBusinessType)->count() > 0) {

            $businessType = BusinessType::find($idBusinessType);

            if($request->exists('name')) $businessType->name = $request->input('name');

            if($businessType->save()) {
                return response()->json(['status' => 'success'], 200);
            }

            return response()->json(['errors' => 'No se actualizó el Tipo de Negocio'], 422);
        }

        return response()->json(['errors' => 'No existe el Tipo de Negocio'], 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BusinessType  $businessType
     * @return \Illuminate\Http\Response
     */
    public function destroy($idBusinessType)
    {
        if(BusinessType::where('id', $idBusinessType)->count() > 0) {

            $businessType = BusinessType::find($idBusinessType);

            if(Business::where('business_type_id', $idBusinessType)->count() > 0)
                return response()->json(['errors' => 'No se pudo borrar el Tipo de Negocio porque tiene Negocios asociados'], 422);

            if($businessType->delete()) {
                return response()->json(['status' => 'success'], 200);
            }

            return response()->json(['errors' => 'No se pudo borrar el Tipo de Negocio'], 422);
        }

        return response()->json(['errors' => 'No existe el Tipo de Negocio']);
    }
}
