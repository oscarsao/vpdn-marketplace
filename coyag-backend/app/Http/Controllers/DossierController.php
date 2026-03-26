<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Dossier;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File as FileStorage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DossierController extends Controller
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
            'dossier_1'                 =>  'required',
        ],
        [
            'business_id.required'      =>  'El ID del Negocio es requerido',
            'business_id.exists'        =>  'El Negocio No Existe',
            'dossier_1.required'        =>  'El Dossier es Requerido'
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        if(Dossier::where('business_id', $request->business_id)->count() >= 2)
            return response()->json(['errors'   =>  'Ya no se aceptan más Dossier para este Negocio'], 422);

        $errors = addDossierInBusiness($request->business_id, $request);

        if($errors == null)
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors' => $errors], 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Dossier  $dossier
     * @return \Illuminate\Http\Response
     */
    public function show(Dossier $dossier)
    {
        // No Aplica, porque se lista junto al negocio
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Dossier  $dossier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dossier $dossier)
    {
        // No Aplica, porque se debe eliminar el Dossier y agregar uno nuevo
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Dossier  $dossier
     * @return \Illuminate\Http\Response
     */
    public function destroy($idDossier)
    {
        $dossier = Dossier::find($idDossier);

        if(!$dossier)
            return response()->json(['errors'   => 'El Dossier no existe'], 422);

        if($dossier->file_id == null)
            return response()->json(['errors'   =>  'El Dossier no posee un Archivo asociado'], 422);


        if(FileStorage::exists(public_path($dossier->file->full_path))){
            FileStorage::delete(public_path($dossier->file->full_path));
        }

        $dossier->file->delete();

        $dossier->delete();

        return response()->json(['status' => 'success'], 200);
    }

    public function downloadFile($idDossier)
    {
        $dossier = Dossier::find($idDossier);

        if(!$dossier)
            return response()->json(['errors'   => 'El Dossier no existe'], 422);

        if($dossier->file_id == null)
            return response()->json(['errors'   =>  'El Dossier no posee un Archivo asociado'], 422);

        $headers = array(
            "Content-Type:" => $dossier->file->mime_type,
        );

        return response()->download($dossier->file->full_path, $dossier->file->original_name, $headers);
    }
}
