<?php

namespace App\Http\Controllers;

use App\Imports\UpdateBusinessSectorImport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BusinessSectorController extends Controller
{
    public function updateFromExcel(Request $request)
    {
        /**
         * Actualiza el sector de los negocios a través de un excel
         * El Negocio es buscado por su ID CRM (id_code) o
         * su URL (source_url)
         */


        /**
         * TODO: Primera versión
         */
        $validator = Validator::make($request->all(), [
            'file'                  =>  'required|file|max:4096|mimes:xls,xlsx',
        ],
        [
            'file.required'         =>  'El Archivo es requerido',
            'file.file'             =>  'El Archivo debe ser del tipo binario',
            'file.max'              =>  'El Archivo debe tener un peso máximo de 4MB',
            'file.mimes'            =>  'El Archivo debe ser xls o xlsx',
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $today = Carbon::now()->format('d-m-Y H:i:s');


        try {
            Log::info('==========================================');
            Log::info('Inicio Actualización de Negocios (Sectores)');
            Log::info('Fecha: ' . $today);
            Log::info('==========================================');

            $arrReturn = ['loaded_rows' => '', 'unloaded_rows' => '', 'update_business_id_crm' => '', 'highest_row' => 0];

            $import = (new UpdateBusinessSectorImport($arrReturn))->import($request->file('file'));

        }
        catch (\Exception $e) {
            Log::info("Exception in updateFromExcel-BusinessSectorController - $today");
            Log::info($e);
            return response()->json(['error' => 'Error no determinado. Revise el archivo', 'rows' => $arrReturn], 400);
        } catch (\Throwable $e) {
            Log::info("Throwable in updateFromExcel-BusinessSectorController - $today");
            Log::info($e);
            return response()->json(['error' => 'Error no determinado. Revise el archivo', 'rows' => $arrReturn], 400);
        }

        Log::info('==========================================');
        Log::info('Fin de Actualización Negocios (Sectores)');
        Log::info('==========================================');

        return response()->json(['status' => 'success', 'rows' => $arrReturn], 200);

    }
}
