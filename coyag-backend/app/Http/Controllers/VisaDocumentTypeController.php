<?php

namespace App\Http\Controllers;

use App\Models\VisaDocumentType;
use App\Models\VisaRequirement;
use App\Models\VisaType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VisaDocumentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $visaDocumentTypes = VisaDocumentType::select('id as id_visa_document_type', 'name as name_visa_document_type', 'created_at as created_at_visa_document_type')
                                ->get();

        $arrVisaDocumentTypes = [];
        for($i = 0; $i < count($visaDocumentTypes); $i++)
        {

            $visaTypes = VisaType::leftJoin( 'visa_document_type_visa_type', 'visa_document_type_visa_type.visa_type_id', '=', 'visa_types.id')
                                    ->where('visa_document_type_visa_type.visa_document_type_id', $visaDocumentTypes[$i]['id_visa_document_type'])
                                    ->select('visa_types.id as id_visa_type', 'visa_types.name as name_visa_type')
                                    ->get();

            $keepVisaType = false;
            $visaTypesStr = '';
            $visaTypeTotal = count($visaTypes);
            for($j = 0; $j <  $visaTypeTotal; $j++)
            {
                $visaTypesStr =  $visaTypesStr . $visaTypes[$j]['id_visa_type'] . '-' . $visaTypes[$j]['name_visa_type'];

                if($j != ( $visaTypeTotal - 1) )
                    $visaTypesStr = $visaTypesStr . ',';

                if($request->exists('visa_types'))
                    $keepVisaType = $keepVisaType || in_array($visaTypes[$j]['id_visa_type'], explode(',', str_replace(' ', '', $request->visa_types)));

            }


            $visaDocumentTypes[$i]['visa_types'] = $visaTypesStr;



            if($request->exists('visa_types'))
            {

                if($keepVisaType)
                    array_push($arrVisaDocumentTypes, $visaDocumentTypes[$i]);


            }
            else {
                array_push($arrVisaDocumentTypes, $visaDocumentTypes[$i]);
            }
        }

         return response()->json([
            'status'                 =>  'success',
            'visaDocumentTypes'      =>  $arrVisaDocumentTypes
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
            'name'              =>  'required|max:512|unique:visa_document_types',
        ],
        [
            'name.required'     =>  'El Nombre es requerido',
            'name.max'          =>  'El Nombre no debe ser mayor a 512 caracteres',
            'name.unique'       =>  'El Nombre ya está siendo utilizado',
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        $auxId;

        if($request->exists('visa_types'))
        {
            $auxRVT = checkVisaType($request->visa_types);

            if($auxRVT['status'] == 'FAIL')
                return response()->json(['errors' => $auxRVT['errors']], 422);

            $auxId = createVisaDocumentType($request->name, $request->visa_types);
        }
        else
            $auxId = createVisaDocumentType($request->name, null);

        if($auxId != 0)
            return response()->json(['status'    =>  'success'], 200);

        return response()->json(['errors'    =>  'No se pudo guardar el Tipo de Documento de Visa'], 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VisaDocumentType  $visaDocumentType
     * @return \Illuminate\Http\Response
     */
    public function show($idVisaDocumentType)
    {
        if(VisaDocumentType::where('id', $idVisaDocumentType)->count() == 0)
            return response()->json(['errors'   =>  'El Tipo de Documento de Visa NO existe']);

        $visaDocumentType = VisaDocumentType::select('id as id_visa_document_type', 'name as name_visa_document_type', 'created_at as created_at_visa_document_type')
                                            ->where('id', $idVisaDocumentType)
                                            ->first();

        $visaTypes = VisaType::leftJoin('visa_document_type_visa_type', 'visa_document_type_visa_type.visa_type_id', '=', 'visa_types.id')
                                ->where('visa_document_type_visa_type.visa_document_type_id', $idVisaDocumentType)
                                ->select('visa_types.id as id_visa_type', 'visa_types.name as name_visa_type')
                                ->get();

        $visaDocumentType['visa_types'] = $visaTypes;


        return response()->json([
            'status'                =>  'success',
            'visaDocumentType'      =>  $visaDocumentType
        ], 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\VisaDocumentType  $visaDocumentType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idVisaDocumentType)
    {

        $visaDocumentType = VisaDocumentType::find($idVisaDocumentType);

        if(!$visaDocumentType)
            return response()->json(['errors'   =>  'El Tipo de Documento de Visa NO existe']);

        $validator = Validator::make($request->all(),
        [
            'name'              =>  'max:512|unique:visa_document_types',
        ],
        [
            'name.max'          =>  'El Nombre no debe ser mayor a 512 caracteres',
            'name.unique'       =>  'El Nombre ya está siendo utilizado',
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        if($request->exists('visa_types'))
        {
            $visaTypes = explode(',', $request->visa_types);

            for($i = 0; $i < count($visaTypes); $i++)
            {
                if(VisaType::where('id', $visaTypes[$i])->count() == 0)
                    return response()->json(['errors' => "El Tipo de Visa con ID $visaTypes[$i] no existe"], 422);
            }

            $visaDocumentType->visaType()->detach();

            for($i = 0; $i < count($visaTypes); $i++)
            {
                $visaDocumentType->visaType()->attach($visaTypes[$i]);
            }
        }

        if($request->exists('name'))
            $visaDocumentType->name = $request->name;

        if($visaDocumentType->save())
            return response()->json(['status'    =>  'success'], 200);

        return response()->json(['errors'    =>  'No se pudo actualizar el Tipo de Documento de Visa'], 422);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VisaDocumentType  $visaDocumentType
     * @return \Illuminate\Http\Response
     */
    public function destroy($idVisaDocumentType)
    {
        $visaDocumentType = VisaDocumentType::find($idVisaDocumentType);

        if(!$visaDocumentType)
            return response()->json(['errors'   =>  'El Tipo de Documento de Visa NO existe']);

        if(VisaRequirement::where('visa_document_type_id', $idVisaDocumentType)->count() > 0)
            return response()->json(['errors'   =>  'No se puede borrar porque hay Requerimientos de Visa asociados a este tipo'], 422);

        $visaDocumentType->visaType()->detach();

        if($visaDocumentType->delete())
            return response()->json(['status'   =>  'success'], 200);

        return response()->json(['errors'   =>  'No se pudo borrar el Tipo de Documento de Visa'], 422);

    }
}
