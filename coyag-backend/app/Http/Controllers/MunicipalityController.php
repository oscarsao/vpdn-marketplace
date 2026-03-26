<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\District;
use App\Models\Municipality;
use App\Models\Province;
use App\Imports\UpdateMunicipalityImport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MunicipalityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $municipalities;

        if(isset(Auth::user()->employee->id))
            $municipalities = Municipality::select('id as id_municipality', 'name as name_municipality', 'flag_city as flag_city_municipality', 'province_id as id_province', 'rent_per_capita as rent_per_capita_municipality', 'demographic_data as demographic_data_municipality', 'created_at as created_at_municipality', 'updated_at as updated_at_municipality');
        else
            $municipalities = Municipality::select('id as id_municipality', 'name as name_municipality', 'flag_city as flag_city_municipality', 'province_id as id_province', 'created_at as created_at_municipality', 'updated_at as updated_at_municipality');
        
        $municipalities = $municipalities->withCount(['businesses' => function ($query) {
            $query->where('flag_active', true)->where('business_type_id', 1);
        }, 'businesses as properties_count' => function ($query) {
            $query->where('flag_active', true)->where('business_type_id', 3);
        }])->get();

        return response()->json([
        'status'            =>  'success',
        'municipalities'    =>  $municipalities
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
            'province_id'           =>  'required|numeric',
            'name'                  =>  'required'
        ],
        [
            'province_id.required'          =>  'El ID de la Provincia es requerido',
            'province_id.numeric'           =>  'El ID de la provincia debe ser del tipo numérico',
            'name.required'                 =>  'El Nombre del Municipio es requerido'
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $municipality = new Municipality();

        $municipality->province_id = $request->province_id;
        $municipality->name = $request->name;

        if($request->exists('flag_city'))
            $municipality->flag_city = $request->flag_city;

        if(Province::where('id', $municipality->province_id)->count() == 0)
            return response()->json(['errors' => 'El ID de la Provincia no es válido'], 422);

        if($request->exists('rent_per_capita'))
            $municipality->rent_per_capita = $request->rent_per_capita;

        if($request->exists('demographic_data'))
            $municipality->demographic_data = $request->demographic_data;

        if($municipality->save())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors' => 'No se pudo guardar el Municipio'], 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Municipality  $municipality
     * @return \Illuminate\Http\Response
     */
    public function show($idMunicipality)
    {
        if(Municipality::where('id', $idMunicipality)->count() == 0)
            return response()->json(['errors' => 'El Municipio no existe'], 422);

        $municipality =  Municipality::where('id', $idMunicipality);

        if(isset(Auth::user()->employee->id))
            $municipality = $municipality->select('id as id_municipality', 'name as name_municipality', 'flag_city as flag_city_municipality', 'province_id as id_province', 'rent_per_capita as rent_per_capita_municipality', 'demographic_data as demographic_data_municipality', 'created_at as created_at_municipality', 'updated_at as updated_at_municipality');
        else
            $municipality = $municipality->select('id as id_municipality', 'name as name_municipality', 'flag_city as flag_city_municipality', 'province_id as id_province', 'created_at as created_at_municipality', 'updated_at as updated_at_municipality');

        $municipality = $municipality->first();

        return response()->json([
            'status'            =>  'success',
            'municipality'      =>  $municipality
        ], 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Municipality  $municipality
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idMunicipality)
    {
        if(Municipality::where('id', $idMunicipality)->count() > 0)
        {
            $municipality = Municipality::find($idMunicipality);

            if($request->exists('name'))
                $municipality->name = $request->name;

            if($request->exists('flag_city'))
                $municipality->flag_city = $request->flag_city;

            if($request->exists('province_id'))
            {
                $municipality->province_id = $request->province_id;

                if(Province::where('id', $municipality->province_id)->count() == 0)
                    return response()->json(['errors' => 'El ID de la Provincia no es válido'], 422);
            }

            if($request->exists('rent_per_capita')) {
                if($request->rent_per_capita == 'null')
                    $municipality->rent_per_capita = null;
                else
                    $municipality->rent_per_capita = $request->rent_per_capita;
            }

            if($request->exists('demographic_data')) {
                if($request->demographic_data == 'null')
                    $municipality->demographic_data = null;
                else
                    $municipality->demographic_data = $request->demographic_data;
            }

            if($municipality->save())
                return response()->json(['status' => 'success'], 200);

            return response()->json(['errors' => 'No se pudo actualizar el Municipio'], 422);
        }

        return response()->json(['errors' => 'El Municipio no existe'], 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Municipality  $municipality
     * @return \Illuminate\Http\Response
     */
    public function destroy($idMunicipality)
    {
        if(Municipality::where('id', $idMunicipality)->count() > 0)
        {
            $municipality = Municipality::find($idMunicipality);

            if(Business::where('municipality_id', $idMunicipality)->count() > 0)
                return response()->json(['errors' => 'No se pudo borrar el Municipio porque tiene Negocios asociados'], 422);

            if($municipality->delete()) {
                return response()->json(['status' => 'success'], 200);
            }

            return response()->json(['errors' => 'No se pudo borrar el Municipio'], 422);
        }

        return response()->json(['errors' => 'El Municipio no existe'], 422);
    }

    /*
     *  Obtiene todos los distritos de un Municipio
     */

    public function districts($idMunicipality)
    {
        if(Municipality::where('id', $idMunicipality)->count() > 0)
        {
            $districts = District::leftJoin('municipalities', 'municipalities.id', '=', 'districts.municipality_id')
                            ->leftJoin('provinces', 'provinces.id', '=', 'municipalities.province_id')
                            ->leftJoin('autonomous_communities', 'autonomous_communities.id', '=', 'provinces.autonomous_community_id')
                            ->where('municipalities.id', $idMunicipality)
                            ->select('districts.id as id_district', 'districts.name as name_district', 'municipalities.id as id_municipality', 'municipalities.name as name_municipality','municipalities.flag_city as flag_city_municipality', 'provinces.id as id_province', 'provinces.name as name_province', 'autonomous_communities.id as id_autonomous_community', 'autonomous_communities.name as name_autonomous_community')
                            ->get();

            return response()->json(
                [
                    'status' => 'success',
                    'districts' => $districts
                ], 200);
        }

        return response()->json(['errors' => 'El Municipio no existe'], 422);
    }

    public function updateFromExcel(Request $request)
    {
        /**
         * Esto actualiza rent_per_capita y demographic_data de los municipios
         * a través de un archivo de excel
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
            Log::info('=======================================================================');
            Log::info('Inicio Actualización de Municipios (rent_per_capita y demographic_data)');
            Log::info('Fecha: ' . $today);
            Log::info('=======================================================================');

            $arrReturn = ['loaded_rows' => '', 'unloaded_rows' => '', 'update_municipality_id' => '', 'highest_row' => 0];

            $import = (new UpdateMunicipalityImport($arrReturn))->import($request->file('file'));

        }
        catch (\Exception $e) {

            Log::info("Exception in updateFromExcel-MunicipalityController - $today");
            Log::info($e);
            return response()->json(['error' => 'Error no determinado. Revise el archivo', 'rows' => $arrReturn], 400);

        } catch (\Throwable $e) {

            Log::info("Throwable in updateFromExcel-MunicipalityController - $today");
            Log::info($e);

            return response()->json(['error' => 'Error no determinado. Revise el archivo', 'rows' => $arrReturn], 400);
        }

        Log::info('=======================================================================');
        Log::info('Fin Actualización de Municipios (rent_per_capita y demographic_data)');
        Log::info('=======================================================================');

        return response()->json(['status' => 'success', 'rows' => $arrReturn], 200);

    }
}
