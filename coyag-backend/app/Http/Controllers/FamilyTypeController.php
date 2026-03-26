<?php

namespace App\Http\Controllers;

use App\Models\Family;
use App\Models\FamilyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FamilyTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $familyTypes = FamilyType::select('id as id_family_type', 'name as name_family_type')
                                    ->orderBy('name', 'ASC')
                                    ->get();

        return response()->json(
        [
            'status'        => 'success',
            'familyTypes'   => $familyTypes
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
        $validator = Validator::make($request->all(),
        [
            'name'      =>  'required|max:64|unique:family_types'
        ],
        [
            'name.required'     =>  'El Nombre es Requerido',
            'name.max'          =>  'La longitud máxima del Nombre es de 64 caracteres',
            'name.unique'       =>  'El Nombre ya está siendo usado',
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        $familyType = new FamilyType();

        $familyType->name = $request->name;

        if($familyType->save())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors'   =>  'No se pudo crear el Tipo de Familiar'], 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FamilyType  $familyType
     * @return \Illuminate\Http\Response
     */
    public function show($idFamilyType)
    {
        $familyType = FamilyType::select('id as id_family_type', 'name as name_family_type')
                                ->where('id', $idFamilyType)
                                ->first();

        return response()->json(
        [
            'status'        => 'success',
            'familyType'    => $familyType
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FamilyType  $familyType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idFamilyType)
    {
        $familyType = FamilyType::find($idFamilyType);

        if(!$familyType)
            return response()->json(['errors' => 'El Tipo de Familiar no existe'], 422);

        $validator = Validator::make($request->all(),
        [
            'name'      =>  'required|max:64|unique:family_types'
        ],
        [
            'name.required'     =>  'El Nombre es Requerido',
            'name.max'          =>  'La longitud máxima del Nombre es de 64 caracteres',
            'name.unique'       =>  'El Nombre ya está siendo usado',
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        $familyType->name = $request->name;

        if($familyType->save())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors'   =>  'No se pudo crear el Tipo de Familiar'], 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FamilyType  $familyType
     * @return \Illuminate\Http\Response
     */
    public function destroy($idFamilyType)
    {
        $familyType = FamilyType::find($idFamilyType);

        if(!$familyType)
            return response()->json(['errors' => 'El Tipo de Familiar no existe'], 422);

        if(Family::where('family_type_id', $idFamilyType)->count() > 0)
            return response()->json(['errors' => 'No se puede eliminar, hay familiares de este Tipo'], 422);

        if($familyType->delete())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors' => 'No se pudo eliminar el Tipo de Familiar'], 422);
    }
}
