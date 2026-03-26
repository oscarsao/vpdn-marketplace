<?php

namespace App\Http\Controllers;

use App\Models\VisaType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VisaTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Debo adminitir que no me gusta colocar el select al inicio
        $visaTypes = VisaType::select('id as id_visa_type', 'name as name_visa_type', 'flag_active as flag_active_visa_type', 'created_at as created_at_visa_type');

        if($request->exists('flag_active'))
        {
            if(in_array($request->flag_active, [0, 1]))
            {
                $visaTypes = $visaTypes->where('flag_active', $request->flag_active);
            }
        }

        $visaTypes = $visaTypes->get();

        return response()->json([
            'status'        =>  'success',
            'visaTypes'     =>  $visaTypes
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
            'name'                  =>  'required|max:64|unique:visa_types',
            'flag_active'           =>  'boolean'
        ],
        [
            'name.required'              =>  'El Nombre es Requerido',
            'name.max'              =>  'La longitud máximo del Nombre es de 64 caracteres',
            'name.unique'           =>  'El Nombre ya está siendo utilizado',
            'flag_active.boolean'   =>  'El valor de flag_active debe ser 0 o 1'
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        $visaType = new VisaType();

        $visaType->name = $request->name;

        if($request->exists('flag_active'))
            $visaType->flag_active = $request->flag_active;

        if($visaType->save())
            return response()->json(['status'    =>  'success'], 200);

        return response()->json(['errors'    =>  'No se pudo Crear el Tipo de Visado'], 422);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VisaType  $visaType
     * @return \Illuminate\Http\Response
     */
    public function show($idVisaType)
    {

        if(VisaType::where('id', $idVisaType)->count() == 0)
            return response()->json(['errors'   =>  'No existe el Tipo de Visa'], 422);

        $visaType = VisaType::select('id as id_visa_type', 'name as name_visa_type', 'flag_active as flag_active_visa_type', 'created_at as created_at_visa_type')
                                ->first();

        return response()->json([
            'status'       =>  'success',
            'visaType'     =>  $visaType
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\VisaType  $visaType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idVisaType)
    {
        $visaType = VisaType::find($idVisaType);

        if(!$visaType)
            return response()->json(['errors'   =>  'No existe el Tipo de Visa'], 422);

        $validator = Validator::make($request->all(),
        [
            'name'                  =>  'max:64',
            'flag_active'           =>  'boolean'
        ],
        [
            'name.max'              =>  'La longitud máximo del Nombre es de 64 caracteres',
            'flag_active.boolean'   =>  'El valor de flag_active debe ser 0 o 1'
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        if($request->exists('name'))
        {
            if(VisaType::where('name', $request->name)->where('id', '!=', $visaType->id)->count() == 1)
                return response()->json(['errors'   =>  'El Nombre ya está siendo utilizado'], 422);

            if(VisaType::where('name', $request->name)->count() == 0  && $visaType->name != $request->name)
                $visaType->name = $request->name;
        }

        if($request->exists('flag_active'))
            $visaType->flag_active = $request->flag_active;

        if($visaType->save())
            return response()->json(['status'    =>  'success'], 200);

        return response()->json(['errors'    =>  'No se pudo actualizar el Tipo de Visado'], 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VisaType  $visaType
     * @return \Illuminate\Http\Response
     */
    public function destroy(VisaType $visaType)
    {
        /*
            No se contempla porque está la opción de desactivarse
        */
    }
}
