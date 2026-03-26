<?php

namespace App\Http\Controllers;

use ZipArchive;
use App\Models\AddedService;
use App\Models\Family;
use App\Models\File;
use App\Models\VisaRequirement;
use App\Traits\EmailManagementTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File as FileStorage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class VisaRequirementController extends Controller {
    use EmailManagementTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $visaRequirements = VisaRequirement::with('files')
            ->leftJoin('visa_document_types', 'visa_document_types.id', '=', 'visa_requirements.visa_document_type_id')
            ->leftJoin('added_services', 'added_services.id', '=', 'visa_requirements.added_service_id')
            ->leftJoin('clients', 'clients.id', '=', 'visa_requirements.client_id')
            ->leftJoin('families', 'families.id', '=', 'visa_requirements.family_id');
            
        if($request->exists('visa_document_type_id'))
            $visaRequirements = $visaRequirements->where('visa_requirements.visa_document_type_id', $request->visa_document_type_id);

        if($request->exists('added_service_id'))
            $visaRequirements = $visaRequirements->where('visa_requirements.added_service_id', $request->added_service_id);

        if(isset(Auth::user()->employee->id)) {
            if($request->exists('client_id'))
                $visaRequirements = $visaRequirements->where('visa_requirements.client_id', $request->client_id);
        } else {
            $visaRequirements = $visaRequirements->where('visa_requirements.client_id', Auth::user()->client->id);
        }

        if($request->exists('family_id'))
            $visaRequirements = $visaRequirements->where('visa_requirements.family_id', $request->family_id);

        $visaRequirements = $visaRequirements->select('visa_requirements.id', 'visa_requirements.visa_document_type_id as id_visa_document_type', 'visa_document_types.name as name_visa_document_type', 'visa_requirements.added_service_id as id_added_service', 'visa_requirements.client_id as id_client', DB::raw('CONCAT(clients.names, " ", clients.surnames) as full_name_client'), 'visa_requirements.family_id as id_family', DB::raw('CONCAT(families.names, " ", families.surnames) as full_name_family'), 'visa_requirements.expiration_date as expiration_date_visa_requirement', 'visa_requirements.status as status_visa_requirement', 'visa_requirements.passport_number as passport_number_visa_requirement', 'visa_requirements.application_date as application_number_visa_requirement', 'visa_requirements.date_of_issue as date_of_issue_visa_requirement', 'visa_requirements.observation as observation_visa_requirement')->get();

        foreach ($visaRequirements as &$visaRequirement) {
            $visaRequirement['id_visa_requirement'] = $visaRequirement['id'];
            unset($visaRequirement['id']);
        }

        return response()->json([
            'status'            => 'success',
            'visaRequirements'  =>  $visaRequirements
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(),
        [
            'visa_document_type_id'     =>  'exists:visa_document_types,id',
            'name_visa_document_type'   =>  'max:512|unique:visa_document_types,name',
            'added_service_id'          =>  'required|exists:added_services,id',
            'family_id'                 =>  'exists:families,id',
        ],
        [
            'visa_document_type_id.required'    =>  'El ID del Tipo de Documento de Visa es Requerido',
            'visa_document_type_id.exists'      =>  'El ID del Tipo de Documento de Visa no existe en el Sistema',
            'name_visa_document_type.max'       =>  'El Nombre no debe ser mayor a 512 caracteres',
            'name_visa_document_type.unique'    =>  'El Nombre ya está siendo utilizado',
            'added_service_id.required'         =>  'El ID de AddedService es Requerido',
            'added_service_id.exists'           =>  'El ID de AddedService no existe en el Sistema',
            'family_id.exists'                  =>  'El ID del Familiar no existe en el Sistema',
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        if(!$request->exists('visa_document_type_id') and !$request->exists('name_visa_document_type'))
            return response()->json(['errors'   =>  'Debe enviar el ID de VisaDocumentType o un Nombre para crear uno'], 422);

        if($request->exists('visa_document_type_id')) {
            $idVisaDocumentType = $request->visa_document_type_id;
        } else {
            // Crea el VisaDocumentType
            if($request->exists('visa_types')) {
                $auxRVT = checkVisaType($request->visa_types);

                if($auxRVT['status'] == 'FAIL') return response()->json(['errors' => $auxRVT['errors']], 422);

                $idVisaDocumentType = createVisaDocumentType($request->name_visa_document_type, $request->visa_types);
            } else {
                $idVisaDocumentType = createVisaDocumentType($request->name_visa_document_type, null);
            }

            if($idVisaDocumentType == 0)
                return response()->json(['errors' => 'No se pudo guardar el Tipo de Documento de Visa'], 422);
        }

        $addedService = AddedService::find($request->added_service_id);

        if($request->exists('family_id')) {

            $family = Family::find($request->family_id);
            if($family->client_id != $addedService->client_id)
                return response()->json(['errors'   => 'El Familiar no pertenece al Cliente'], 422);

            if(VisaRequirement::where('visa_document_type_id', $idVisaDocumentType)->where('added_service_id', $request->added_service_id)->where('family_id', $request->family_id)->count() > 0)
                return response()->json(['errors'   => 'Este Familiar ya tiene asociado un Requerimiento de Visa con el mismo tipo de documento'], 422);
        } else {
            if(VisaRequirement::where('visa_document_type_id', $idVisaDocumentType)->where('added_service_id', $request->added_service_id)->where('client_id', $addedService->client_id)->count() > 0)
                return response()->json(['errors'   => 'Este Cliente ya tiene asociado un Requerimiento de Visacon el mismo tipo de documento'], 422);
        }

        if($addedService->flag_active_plan == false)
            return response()->json(['errors'   => 'El AddedService no se encuentra activo'], 422);

        if($addedService->service->type != 'Inmigration')
            return response()->json(['errors'   => 'El AddedService no está asociado a un Servicio de Extranjería'], 422);

        $visaRequirement = new VisaRequirement();

        $visaRequirement->visa_document_type_id = $idVisaDocumentType;
        $visaRequirement->added_service_id = $request->added_service_id;
        $visaRequirement->client_id = $addedService->client_id;

        if($request->exists('family_id'))
            $visaRequirement->family_id = $request->family_id;

        if($visaRequirement->save())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors'   =>  'No se pudo crear el Requerimiento de Visa'], 422);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VisaRequirement  $visaRequirement
     * @return \Illuminate\Http\Response
     */
    public function show($idVisaRequirement) {
        if(VisaRequirement::where('id', $idVisaRequirement)->count() == 0)
            return response()->json(['errors' => 'El Requerimiento de Visa no existe'], 422);

        $visaRequirement = VisaRequirement::leftJoin('visa_document_types', 'visa_document_types.id', '=', 'visa_requirements.visa_document_type_id')
            ->leftJoin('added_services', 'added_services.id', '=', 'visa_requirements.added_service_id')
            ->leftJoin('clients', 'clients.id', '=', 'visa_requirements.client_id')
            ->leftJoin('families', 'families.id', '=', 'visa_requirements.family_id')
            ->where('visa_requirements.id', $idVisaRequirement);

        if(isset(Auth::user()->client->id)) {
            $visaRequirement = $visaRequirement->where('visa_requirements.client_id', Auth::user()->client->id);
        }

        $visaRequirement = $visaRequirement->select('visa_requirements.id as id_visa_requirement', 'visa_requirements.visa_document_type_id as id_visa_document_type', 'visa_document_types.name as name_visa_document_type', 'visa_requirements.added_service_id as id_added_service', 'visa_requirements.client_id as id_client', DB::raw('CONCAT(clients.names, " ", clients.surnames) as full_name_client'), 'visa_requirements.family_id as id_family', DB::raw('CONCAT(families.names, " ", families.surnames) as full_name_family'), 'visa_requirements.expiration_date as expiration_date_visa_requirement', 'visa_requirements.status as status_visa_requirement', 'visa_requirements.passport_number as passport_number_visa_requirement', 'visa_requirements.application_date as application_number_visa_requirement', 'visa_requirements.date_of_issue as date_of_issue_visa_requirement', 'visa_requirements.observation as observation_visa_requirement')->first();
        
        if ($visaRequirement) {
            $visaRequirement->files = File::where('id_visa_requirement', $visaRequirement->id_visa_requirement)->get(); 
        }

        return response()->json([
            'status'           => 'success',
            'visaRequirement'  =>  $visaRequirement
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\VisaRequirement  $visaRequirement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idVisaRequirement) {
        $visaRequirement = VisaRequirement::find($idVisaRequirement);
        if(!$visaRequirement)
            return response()->json(['errors'   => 'El Requerimiento de Visa no existe'], 422);

        $validator = Validator::make($request->all(),
        [
            'visa_document_type_id' => 'exists:visa_document_types,id',
            'family_id'             => 'exists:families,id',
        ],
        [
            'visa_document_type_id.exists' => 'El ID del Tipo de Documento de Visa no existe en el Sistema',
            'family_id.exists'             => 'El ID del Familiar no existe en el Sistema',
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        $addedService = AddedService::find($visaRequirement->added_service_id);

        if($request->exists('family_id')) {
            $family = Family::find($request->family_id);
            if($family->client_id != $addedService->client_id)
                return response()->json(['errors'   => 'El Familiar no pertenece al Cliente'], 422);

            if(VisaRequirement::where('visa_document_type_id', $request->visa_document_type_id)->where('added_service_id', $request->added_service_id)->where('family_id', $request->family_id)->count() > 0)
                return response()->json(['errors'   => 'Este Familiar ya tiene asociado un Requerimiento de Visa con el mismo tipo de documento'], 422);
        } else {
            if(VisaRequirement::where('visa_document_type_id', $request->visa_document_type_id)->where('added_service_id', $request->added_service_id)->where('client_id', $addedService->client_id)->count() > 0)
                return response()->json(['errors'   => 'Este Cliente ya tiene asociado un Requerimiento de Visa con el mismo tipo de documento'], 422);
        }


        if($request->exists('visa_document_type_id'))
            $visaRequirement->visa_document_type_id = $request->visa_document_type_id;

        if($request->exists('family_id'))
            $visaRequirement->family_id = $request->family_id;

        if($visaRequirement->save())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors'   =>  'No se pudo actualizar el Requerimiento de Visa'], 422);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VisaRequirement  $visaRequirement
     * @return \Illuminate\Http\Response
     */
    public function destroy($idVisaRequirement) {
        $visaRequirement = VisaRequirement::find($idVisaRequirement);

        if(!$visaRequirement)
            return response()->json(['errors'   => 'El Requerimiento de Visa no existe'], 422);

        File::where('id_visa_requirement', $visaRequirement->id)->delete();

        $visaRequirement->delete();

        return response()->json(['status' => 'success'], 200);
    }

    public function updateMetadaData(Request $request, $idVisaRequirement) {
        $visaRequirement = VisaRequirement::find($idVisaRequirement);

        if(!$visaRequirement)
            return response()->json(['errors'   => 'El Requerimiento de Visa no existe'], 422);

        $validator = Validator($request->all(),
        [
            'passport_number'      => 'max:64',
            'application_date'     => 'date',
            'date_of_issue'        => 'date',
        ],
        [
            'passport_number.max'   => 'La cantidad máxima de caracteres es de 64',
            'application_date.date' => 'Formato de Fecha Inválido (application_date)',
            'date_of_issue.date'    => 'Formato de Fecha Inválido (date_of_issue)',
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        if($request->exists('expiration_date')) {
            if($request->expiration_date == 'null') {
                $visaRequirement->expiration_date = null;
            } else {
                $validator = Validator($request->all(),
                    [ 'expiration_date' => 'date' ],
                    [ 'expiration_date.date' => 'Formato de Fecha Inválido (expiration_date)' ]
                );

                if($validator->fails())
                    return response()->json(['errors'   =>  $validator->errors()], 422);

                $visaRequirement->expiration_date = $request->expiration_date;
            }
        }


        if($request->exists('passport_number'))
            $visaRequirement->passport_number = $request->passport_number;

        if($request->exists('application_date'))
            $visaRequirement->application_date = $request->application_date;

        if($request->exists('date_of_issue'))
            $visaRequirement->date_of_issue = $request->date_of_issue;

        if($visaRequirement->save())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors'   =>  'No se pudo actualizar la MetaData del Requerimiento de Visa'], 422);
    }

    public function removeMetaData($idVisaRequirement) {
        $visaRequirement = VisaRequirement::find($idVisaRequirement);

        if(!$visaRequirement)
            return response()->json(['errors'   => 'El Requerimiento de Visa no existe'], 422);

        $visaRequirement->expiration_date = null;
        $visaRequirement->passport_number = null;
        $visaRequirement->application_date = null;
        $visaRequirement->date_of_issue = null;

        if($visaRequirement->save())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors'   =>  'No se pudo remover la MetaData del Requerimiento de Visa'], 422);
    }

    public function addFile(Request $request, $idVisaRequirement) {
        $visaRequirement = VisaRequirement::find($idVisaRequirement);

        if(!$visaRequirement) return response()->json(['errors' => 'El Requerimiento de Visa no existe'], 422);

        $validator = Validator::make($request->all(),
        [  'file.*' =>  'required|mimes:jpg,jpeg,png,pdf,xlx,xlxs,doc,docs,csv,zip|max:30720' ],
        [
            'file.required' => 'El Archivo es requerido',
            'file.mimes'    => 'El tipo MIME no es soportado',
            'file.max'      => 'El tamaño máximo del archivo no debe superar los 30MB',
        ]);

        if($validator->fails())
            return response()->json(['errors' =>  $validator->errors()], 422);

        if(isset(Auth::user()->client->id)) {
            if($visaRequirement->client_id != Auth::user()->client->id)
                return response()->json(['errors' => 'El Requerimiento de Visa no le pertenece'], 422);
        }

        if($visaRequirement->file_id != null)
            return response()->json(['errors' => 'El Requerimiento de Visa ya posee un archivo asociado'], 422);

        $allFiles = $request->allFiles();
        $allFiles = $allFiles['file'];
        $files_ids = [];

        foreach ($allFiles as $file) {
            $auxFile = $file;
            $auxPath = "files/visa_requirement/$visaRequirement->client_id";
            $path = public_path($auxPath);
            
            if (!is_dir($path)) Storage::makeDirectory($path);

            $file = new File();
            $file->client_id        = $visaRequirement->client_id ;
            $file->employee_id      = (isset(Auth::user()->employee->id)) ? Auth::user()->employee->id : 1;
            $file->original_name    = $auxFile->getClientOriginalName();
            $file->extension        = $auxFile->extension();
            $auxName                = Str::random(12). '.' . $auxFile->extension();
            $file->size             = $auxFile->getSize();
            $file->mime_type        = $auxFile->getMimeType();
            $file->path             = $auxPath;
            $file->full_path        = $auxPath.'/'.$auxName;

            $file->id_visa_requirement = $idVisaRequirement;

            $file->save();
            
            $auxFile->move($path, $auxName);
            $files_ids[] = $file->id;
        }

        // $visaRequirement->file_id = $file->id;
        $visaRequirement->status = 'Cargado';

        $visaRequirement->save();

        return response()->json(['status' => 'success', 'files' => $files_ids], 200);
    }

    public function removeFile($idVisaRequirement) {
        $visaRequirement = VisaRequirement::with('files')->find($idVisaRequirement);
        if(!$visaRequirement) {
            return response()->json(['errors' => 'El Requerimiento de Visa no existe'], 422);
        }
        if(isset(Auth::user()->client->id) && $visaRequirement->client_id != Auth::user()->client->id) {
            return response()->json(['errors' => 'Este Requerimiento de Visa no le Pertenece'], 422);
        }
        if($visaRequirement->status == 'Aceptado') {
            return response()->json(['errors' => 'El Requerimiento de Visa ya fue aceptado'], 422);
        }
        // Assuming the status 'Sin Cargar' means there are no files to delete
        if($visaRequirement->status == 'Sin Cargar') {
            return response()->json(['errors' => 'El Documento del Requerimiento de Visa aún no ha sido cargado'], 422);
        }

        foreach($visaRequirement->files as $file) {
            if (FileStorage::exists(public_path($file->full_path))) {
                FileStorage::delete(public_path($file->full_path));
            }
            $file->delete();
        }
        // Update the VisaRequirement status or perform any other necessary cleanup
        $visaRequirement->status = 'Sin Cargar';
        $visaRequirement->save();
        return response()->json(['status' => 'success'], 200);
    }


    public function downloadFile($idVisaRequirement) {
        $visaRequirement = VisaRequirement::with('files')->find($idVisaRequirement);
        if(!$visaRequirement) {
            return response()->json(['errors' => 'El Requerimiento de Visa no existe'], 422);
        }
        if(isset(Auth::user()->client->id) && Auth::user()->client->id != $visaRequirement->client_id) {
            return response()->json(['errors' => 'No tiene permiso para descargar este Archivo'], 422);
        }
        if($visaRequirement->files->isEmpty()) {
            return response()->json(['errors' => 'El Requerimiento de Visa no posee archivos asociados'], 422);
        }

        if (count($visaRequirement->files) === 1) {
            $file = $visaRequirement->files[0];
            $headers = ['Content-Type' => $file->mime_type, 'Content-Disposition' => 'attachment; filename="' . $zipFileName . '"'];
            return response()->download($file->full_path, $file->original_name, $headers);
        }


        $zip = new ZipArchive;
        $zipFileName = 'files-' . $visaRequirement->name_visa_document_type . '.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($visaRequirement->files as $file) {
                $filePath = public_path($file->full_path);
                if(file_exists($filePath)) {
                    $relativeNameInZipFile = basename($filePath);
                    $zip->addFile($filePath, $relativeNameInZipFile);
                }
            }
            $zip->close();
        } else {
            return response()->json(['errors' => 'No se pudo crear el archivo ZIP'], 422);
        }
        $headers = ['Content-Type' => 'application/zip', 'Content-Disposition' => 'attachment; filename="' . $zipFileName . '"'];

        $response = response()->download($zipPath, $zipFileName, $headers)->deleteFileAfterSend(true);
        return $response;
    }


    public function updateStatus(Request $request, $idVisaRequirement) {
        $visaRequirement = VisaRequirement::find($idVisaRequirement);

        if(!$visaRequirement)
            return response()->json(['errors'   => 'El Requerimiento de Visa no existe'], 422);

        $validator = Validator($request->all(),
        [
            'status'            =>  'required|in:"Aceptado","Rechazado"',
            'observation'       =>  'string|max:255'
        ],
        [
            'status.required'       =>  'El Estatus es requerido',
            'status.in'             =>  'Los valores que acepta Estatus son: Aceptado y Rechazado',
            'observation.string'    =>  'Observación debe ser un String',
            'observation.max'       =>  'Observación no debe exceder los 255 caracteres',
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        if($visaRequirement->file_id == null)
            return response()->json(['errors'   =>  'No se puede cambiar el Estatus porque no tiene un archivo asociado'], 422);

        $visaRequirement->status = $request->status;

        switch ($visaRequirement->status) {
            case 'Aceptado':
                $visaRequirement->observation = null;
                break;

            case 'Rechazado':
                if($request->exists('observation'))
                    $visaRequirement->observation = $request->observation;
                else
                    $visaRequirement->observation = null;
                break;

            default:
                $visaRequirement->observation = null;
                break;
        }

        if($visaRequirement->save())
        {
            if(in_array($visaRequirement->status, ['Aceptado', 'Rechazado']))
                $this->visaRequirementUpdate($visaRequirement->client);

            return response()->json(['status' => 'success'], 200);
        }

        return response()->json(['errors'   =>  'No se pudo actualizar el Status del Archivo del Requerimiento de Visa'], 422);
    }
}
