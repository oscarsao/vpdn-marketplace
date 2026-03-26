<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SectorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sectors = Sector::select('id as id_sector', 'name as name_sector', 'created_at as created_at_sector', 'updated_at as updated_at_sector')->orderBy('name');

        $sectors = $sectors->withCount(['business' => function ($query) {
            $query->where('flag_active', true)->where('business_type_id', 1);
        }])->get();

        return response()->json([
            'status' => 'success',
            'sectors'   => $sectors
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
            'name'      =>      'required|unique:sectors'
        ],
        [
            'name.required'     =>      'El nombre es requerido',
            'name.unique'       =>      'El nombre del Sector debe ser único'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'   =>  $validator->errors()], 422);
        }

        $sector = new Sector();

        $sector->name = $request->name;

        if($sector->save()) {
            return response()->json(['status' => 'success'], 200);
        }

        return response()->json(['errors' => 'No se guardó el Sector'], 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Sector  $sector
     * @return \Illuminate\Http\Response
     */
    public function show($idSector)
    {
        if(Sector::where('id', $idSector)->count() > 0) {

            $sector = Sector::select('id as id_sector', 'name as name_sector', 'created_at as created_at_sector', 'updated_at as updated_at_sector')
                            ->where('id', $idSector)
                            ->first();

            return response()->json([
                'status' => 'success',
                'sector'    => $sector
            ], 200);

        }

        return response()->json(['errors' => 'El Sector no existe'], 422);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Sector  $sector
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idSector)
    {
        if(Sector::where('id', $idSector)->count() > 0)
        {
            $sector = Sector::find($idSector);

            if($request->exists('name')) $sector->name = $request->name;

            if($sector->save())
            {
                return response()->json(['status' => 'success'], 200);
            }

            return response()->json(['errors' => 'No se actualizó el Sector'], 422);
        }

        return response()->json(['errors' => 'El Sector no existe'], 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Sector  $sector
     * @return \Illuminate\Http\Response
     */
    public function destroy($idSector)
    {
        if(Sector::where('id', $idSector)->count() > 0)
        {
            $sector = Sector::find($idSector);

            if(Business::where('sector_id', $idSector)->count() > 0)
                return response()->json(['errors' => 'No se pudo borrar el Sector porque tiene Negocios asociados'], 422);

            if($sector->delete()) {
                return response()->json(['status' => 'success'], 200);
            }

            return response()->json(['errors' => 'No se pudo borrar el Sector'], 422);
        }

        return response()->json(['errors' => 'El Sector no existe'], 422);
    }
}
