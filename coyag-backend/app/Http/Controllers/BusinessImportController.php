<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessMultimedia;
use App\Imports\BusinessImport;
use App\Imports\UpdateBusinessImport;
use App\Imports\UpdateBusinessTerraceSmokeOutletImport;
use App\Traits\PreferenceTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Importer;

use Illuminate\Support\Facades\DB;


class BusinessImportController extends Controller
{
    use PreferenceTrait;
    private $importer;
    public function __construct(Importer $importer) {
        $this->importer = $importer;
    }

    public function store(Request $request) {
        /*
            A diferencia de los otros métodos este puede importar todas las plataformas en un mismo archivo,
            es decir, gestiona los archivos que contiene negocios de toda las webs y está bien procesado
        */

        $validator = Validator::make($request->all(), [
            'file'                          =>  'required|file|max:4096|mimes:xls,xlsx',
            'type'                          =>  'required|in:"completa", "parcial", "residuo"',
            'update_images'                 =>  'required|boolean', // Indica si se actualizarán las imágenes
            'update_fields'                 =>  'required|boolean',
            'update_client_business_list'   =>  'required|boolean',
        ],
        [
            'file.required'                 =>  'El Archivo es requerido',
            'file.file'                     =>  'El Archivo debe ser del tipo binario',
            'file.max'                      =>  'El Archivo debe tener un peso máximo de 4MB',
            'file.mimes'                    =>  'El Archivo debe ser xls o xlsx',
            'type.required'                 =>  'Debe indicar el tipo de carga',
            'type.in'                       =>  'El tipo de carga solo puede ser completa, parcial o residuo',
            'update_images.required'        =>  'El flag update_images es requerido',
            'update_images.boolean'         =>  'El flag update_images debe tener los valores de 1 o 0',
            'update_fields.required'        =>  'El flag update_fields es requerido',
            'update_fields.boolean'         =>  'El flag update_fields debe tener los valores de 1 o 0',
            'update_client_business_list.required'  =>  'El flag update_client_business_list es requerido',
            'update_client_business_list.boolean'   =>  'El flag update_client_business_list debe tener los valores de 1 o 0'
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        if(in_array($request->type, ['residuo'])) {
            return response()->json(['error' => 'El tipo de carga Residuo no está disponible'], 422);
        }
        if(!$request->update_images && !$request->update_fields) {
            return response()->json(['error' => 'Debe actualizar los campos y/o las imágenes'], 422);
        }

        try {
            $now = Carbon::now();
            $today = Carbon::now()->format('d-m-Y H:i:s');

            $startTime = microtime(true);
            Log::info('=================================');
            Log::info('Inicio Importación de Negocios');
            Log::info('Fecha: ' . $today);
            Log::info('=================================');

            $arrReturn = ['loaded_rows' => '', 'unloaded_rows' => '', 'delete_news_images_file'  => '', 'delete_previous_images' => '',
                            'delete_previous_videos' => '', 'charged_business' => '', 'total_rows' => 0, 'highest_row' => 0];

            $provinces      = DB::table('provinces'     )->select('id','name')->get();
            $neighborhoods  = DB::table('neighborhoods' )->select('id','name')->get();
            $districts      = DB::table('districts'     )->select('id','name')->get();
            $municipalities = DB::table('municipalities')->select('id','name')->get();

            $import = (new BusinessImport($arrReturn, [
                "update_image" => $request->update_images == 1 ? true : false,
                "update_field" => $request->update_fields == 1 ? true : false
            ],
            [
                "provinces"      => $provinces     ,
                "municipalities" => $municipalities,
                "districts"      => $districts     ,
                "neighborhoods"  => $neighborhoods
            ]
            ))->import($request->file('file'));

        } catch (\Exception $e) {
            Log::info('Tiempo: ' . (microtime(true)- $startTime) . 's');
            Log::info("Exception in Store-BusinessImportController - $today");
            Log::info($e);
            $this->deleteImagesAfterError($now, $arrReturn['delete_news_images_file']);
            return response()->json(['error' => 'Error no determinado. Revise el archivo', 'rows' => $arrReturn], 400);
        } catch (\Throwable $e) {
            Log::info('Tiempo: ' . (microtime(true)- $startTime) . 's');
            Log::info("Throwable in Store-BusinessImportController - $today");
            Log::info($e);
            $this->deleteImagesAfterError($now, $arrReturn['delete_news_images_file']);
            return response()->json(['error' => 'Error no determinado. Revise el archivo', 'rows' => $arrReturn], 400);
        }

        // Los negocios anteriores a esta carga ya no serán visibles a los clientes
        if(in_array($request->type, ['completa'])) {
            Business::where('updated_at', '<', $now)->update(['flag_active' => false]);
        }
        if($arrReturn['delete_previous_images'] != '') {
            $this->deletePreviousImages($now, $arrReturn['delete_previous_images']);
        }

        if($arrReturn['delete_previous_videos'] != '')
            $this->deletePreviousVideos($now, $arrReturn['delete_previous_videos']);

        if($request->update_client_business_list) {
            $this->updateBusinessListForAllClients();
        }

        Log::info('=================================');
        Log::info('Fin Importación de Negocios');
        Log::info('Tiempo: ' . (microtime(true)- $startTime) . 's');
        Log::info('=================================');

        return response()->json(['status' => 'success', 'rows' => $arrReturn], 200);
    }

    private function deletePreviousImages($now, $business)
    {
        // Borra las Imagenes anteriores a las actuales
        $arr = explode(',', $business);

        foreach ($arr as $idBusiness) {
            $arrBusinessMultimedia = BusinessMultimedia::where('business_id', $idBusiness)
                                                        ->where('created_at', '<', $now)
                                                        ->where('type', 'image')
                                                        ->get();

            foreach($arrBusinessMultimedia as $bM)
            {
                deleteResourceBusinessMultimedia($bM);
                $bM->delete();
            }
        }
    }

    private function deletePreviousVideos($now, $listBusinessMultimedia)
    {
        // Borra los videos anteriores a los actuales
        $arr = explode(',', $listBusinessMultimedia);
        BusinessMultimedia::whereIn('id', $arr)
                            ->delete();
    }

    private function deleteImagesAfterError($now, $files)
    { // Como ocurrió un Rollback, las imágenes no están en la BD pero si en Disco
        $arrFiles = explode(',', $files);

        foreach ($arrFiles as $file) {
            if(File::exists(public_path($file))){
                File::delete(public_path($file));
            }else{
                Log::error("Error - COYAG ->  Error deleteResourceBusinessMultimedia: No borró el archivo $file");
            }
        }
    }

    public function updateBusiness(Request $request) {

        $validator = Validator::make($request->all(), [
            'file'                 =>  'required|file|max:4096|mimes:xls,xlsx'
        ],
        [
            'file.required'        =>  'El Archivo es requerido',
            'file.file'            =>  'El Archivo debe ser del tipo binario',
            'file.max'             =>  'El Archivo debe tener un peso máximo de 4MB',
            'file.mimes'           =>  'El Archivo debe ser xls o xlsx'
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $today = Carbon::now()->format('d-m-Y H:i:s');

        try {
            $arrReturn = ['loaded_rows' => '', 'unloaded_rows' => '', 'total_rows' => 0, 'highest_row' => 0];
            $import = (new UpdateBusinessImport($arrReturn))->import($request->file('file'));
        }
        catch (\Exception $e) {
            Log::info("Exception in UpdateBusinessImportController - $today");
            Log::info($e);
            return response()->json(['error' => 'Error no determinado. Revise el archivo', 'rows' => $arrReturn], 400);
        } catch (\Throwable $e) {
            Log::info("Throwable in UpdateBusinessImportController - $today");
            Log::info($e);
            return response()->json(['error' => 'Error no determinado. Revise el archivo', 'rows' => $arrReturn], 400);
        }

        return response()->json(['status' => 'success', 'rows' => $arrReturn], 200);
    }

    public function updateTerraceSmokeOutlet(Request $request)
    {
        /**
         * Actualiza: flag_terrace y flag_smoke_outlet pertenecientes a un sector
         * El negocio se busca por medio de su URL
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

        if($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);
        $today = Carbon::now()->format('d-m-Y H:i:s');

        try {
            Log::info('======================================================================');
            Log::info('Inicio Actualización de Negocios (Flag de Terraza y de Salida de Humo)');
            Log::info('Fecha: ' . $today);
            Log::info('======================================================================');
            $arrReturn = ['loaded_rows' => '', 'unloaded_rows' => '', 'update_business_id_crm' => '', 'highest_row' => 0];
            $import = (new UpdateBusinessTerraceSmokeOutletImport($arrReturn))->import($request->file('file')); 
        }
        catch (\Exception $e) {
            Log::info("Exception in updateTerraceSmokeOutlet-BusinessSectorController - $today");
            Log::info($e);
            return response()->json(['error' => 'Error no determinado. Revise el archivo', 'rows' => $arrReturn], 400);
        } catch (\Throwable $e) {
            Log::info("Throwable in updateTerraceSmokeOutlet-BusinessSectorController - $today");
            Log::info($e);
            return response()->json(['error' => 'Error no determinado. Revise el archivo', 'rows' => $arrReturn], 400);
        }

        Log::info('======================================================================');
        Log::info('Fin de Actualización Negocios (Flag de Terraza y de Salida de Humo)');
        Log::info('======================================================================');

        return response()->json(['status' => 'success', 'rows' => $arrReturn], 200);
    }


}
