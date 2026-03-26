<?php

namespace App\Http\Controllers;

use App\Models\Busiest;
use App\Models\Business;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File as FileStorage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BusiestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // No Aplica, porque se lista junto al negocio
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
            'business_id'               =>  'required|exists:businesses,id',
            'busiest_file'              =>  'required',
        ],
        [
            'business_id.required'      =>  'El ID del Negocio es requerido',
            'business_id.exists'        =>  'El Negocio No Existe',
            'busiest_file.required'     =>  'El Horario Concurrido es Requerido'
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        if(Busiest::where('business_id', $request->business_id)->count() >= 1)
            return response()->json(['errors'   =>  'Ya no se aceptan más Horarios Concurridos para este Negocio'], 422);

        $errors = addBusiestInBusiness($request->business_id, $request);

        if($errors == null)
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors' => $errors], 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Busiest  $busiest
     * @return \Illuminate\Http\Response
     */
    public function show(Busiest $busiest)
    {
        // No Aplica, porque se lista junto al negocio
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Busiest  $busiest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Busiest $busiest)
    {
        // No Aplica, porque se debe eliminar el Busiest y agregar uno nuevo
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Busiest  $busiest
     * @return \Illuminate\Http\Response
     */
    public function destroy($idBusiest)
    {
        $busiest = Busiest::find($idBusiest);

        if(!$busiest)
            return response()->json(['errors'   => 'El Horario Concurrido no existe'], 422);

        if($busiest->file_id == null)
            return response()->json(['errors'   =>  'El Horario Concurrido no posee un Archivo asociado'], 422);


        if(FileStorage::exists(public_path($busiest->file->full_path))){
            FileStorage::delete(public_path($busiest->file->full_path));
        }

        $busiest->file->delete();

        $busiest->delete();

        return response()->json(['status' => 'success'], 200);
    }

    public function downloadFile($idBusiest)
    {
        $busiest = Busiest::find($idBusiest);

        if(!$busiest)
            return response()->json(['errors'   => 'El Horario Concurrido no existe'], 422);

        if($busiest->file_id == null)
            return response()->json(['errors'   =>  'El Horario Concurrido no posee un Archivo asociado'], 422);

        $headers = array(
            "Content-Type:" => $busiest->file->mime_type,
        );

        return response()->download($busiest->file->full_path, $busiest->file->original_name, $headers);
    }
}
