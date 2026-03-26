<?php

use App\Models\Business;
use App\Models\Busiest;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File as FileStorage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

function addBusiestInBusiness($idBusiness, Request $request)
{

    $errors = null;
    $validator = Validator::make($request->all(),
    [
        'busiest_file'                 =>  'mimes:jpg,jpeg,png|max:30720',
    ],
    [
        'busiest_file.mimes'           =>  'El tipo de archivo debe ser jpg, jpeg o png',
        'busiest_file.max'             =>  'El tamaño máximo del archivo no debe superar los 30MB',
    ]);

    if($validator->fails()) {
        $errors =  $validator->errors();
        return $errors;
    }

    if(Business::where('id', $idBusiness)->count() == 0) {
        $errors = 'No existe el Negocio';
    }

    if(!$request->exists('busiest_file'))
        return $errors;

    $auxFile = $request->busiest_file;

    $auxPath = "files/busiest/$idBusiness";
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

    $dossier = new Busiest();
    $dossier->business_id = $idBusiness;
    $dossier->file_id = $file->id;
    $dossier->save();

    $auxFile->move($path, $auxName);


    return $errors;

}
