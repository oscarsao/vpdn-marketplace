<?php

use App\Models\Business;
use App\Models\Dossier;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File as FileStorage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;


function addDossierInBusiness($idBusiness, Request $request)
{
    $errors = null;
    $validator = Validator::make($request->all(),
    [
        'dossier_1'                 =>  'mimes:pdf|max:30720',
        'dossier_2'                 =>  'mimes:pdf|max:30720',
    ],
    [
        'dossier_1.mimes'           =>  'El tipo de archivo debe ser un PDF',
        'dossier_1.max'             =>  'El tamaño máximo del archivo no debe superar los 30MB',
        'dossier_2.mimes'           =>  'El tipo de archivo debe ser un PDF',
        'dossier_2.max'             =>  'El tamaño máximo del archivo no debe superar los 30MB',
    ]);

    if($validator->fails()) {
        $errors =  $validator->errors();
        return $errors;
    }

    if(Business::where('id', $idBusiness)->count() == 0) {
        $errors = 'No existe el Negocio';
    }

    if($request->exists('dossier_1'))
        storeDossier($request->dossier_1, $idBusiness);

    if($request->exists('dossier_2'))
        storeDossier($request->dossier_2, $idBusiness);


    return $errors;

}

function storeDossier($auxFile, $idBusiness) {

    $auxPath = "files/dossier/$idBusiness";
    $path = public_path($auxPath);

    if (!is_dir($path)) {
        Storage::makeDirectory($path);
    }

    $file = new File();

    $file->employee_id      = Auth::user()->employee->id;
    $file->original_name    = $auxFile->getClientOriginalName();
    $file->extension        = $auxFile->extension();
    $auxName                = Str::random(12). '.' . $auxFile->extension();
    $file->size             = $auxFile->getSize();
    $file->mime_type        = $auxFile->getMimeType();
    $file->path             = $auxPath;
    $file->full_path        = $auxPath.'/'.$auxName;

    $file->save();

    $dossier = new Dossier();
    $dossier->business_id = $idBusiness;
    $dossier->file_id = $file->id;
    $dossier->save();

    $auxFile->move($path, $auxName);
}
