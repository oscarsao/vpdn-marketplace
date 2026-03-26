<?php

namespace App\Http\Controllers;

use App\Models\VisaStepType;
use App\Models\VisaType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VisaStepTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $visaStepTypes = VisaStepType::select('id as id_visa_step_type', 'name as name_visa_step_type', 'client_description as client_description_visa_step_type', 'advisor_description as advisor_description_visa_step_type', 'created_at as created_at_visa_step_type', 'updated_at as updated_at_visa_step_type')
                                            ->orderBy('name', 'asc')
                                            ->get();

        $arrVisaStepTypes = [];
        for($i = 0; $i < count($visaStepTypes); $i++) {

            $visaTypes = VisaType::leftJoin('visa_step_type_visa_type', 'visa_step_type_visa_type.visa_type_id', '=', 'visa_types.id')
                                    ->where('visa_step_type_visa_type.visa_step_type_id', $visaStepTypes[$i]['id_visa_step_type'])
                                    ->select('visa_types.id as id_visa_type', 'visa_types.name as name_visa_type', 'visa_step_type_visa_type.number_client as number_client', 'visa_step_type_visa_type.number_advisor as number_advisor')
                                    ->get();

            $keepVisaType = false;
            $visaTypesStr = '';
            $visaTypesIdStr = '';
            $numberCAStr = '';

            $visaTypeTotal = count($visaTypes);

            for($j = 0; $j <  $visaTypeTotal; $j++){

                $visaTypesStr =  $visaTypesStr . $visaTypes[$j]['id_visa_type'] . '-' . $visaTypes[$j]['name_visa_type'];
                $visaTypesIdStr = $visaTypesIdStr . $visaTypes[$j]['id_visa_type'];
                $numberCAStr = $numberCAStr . 'VT:' . $visaTypes[$j]['id_visa_type'] .  '-NC:' . $visaTypes[$j]['number_client'] . '-NA:' . $visaTypes[$j]['number_advisor'];

                if($j != ( $visaTypeTotal - 1) ) {
                    $visaTypesStr = $visaTypesStr . ',';
                    $visaTypesIdStr = $visaTypesIdStr . ',';
                    $numberCAStr = $numberCAStr . ',';
                }


                if($request->exists('visa_types'))
                    $keepVisaType = $keepVisaType || in_array($visaTypes[$j]['id_visa_type'], explode(',', str_replace(' ', '', $request->visa_types)));

            }

            $visaStepTypes[$i]['visa_types'] = $visaTypesStr;
            $visaStepTypes[$i]['id_visa_types'] = $visaTypesIdStr;
            $visaStepTypes[$i]['numbers_client_advisor'] = $numberCAStr;

            if($request->exists('visa_types'))
            {

                if($keepVisaType)
                    array_push($arrVisaStepTypes, $visaStepTypes[$i]);


            }
            else {
                array_push($arrVisaStepTypes, $visaStepTypes[$i]);
            }

        }

        return response()->json([
            'status'            =>  'success',
            'visaStepTypes'     =>  $arrVisaStepTypes,
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
            'name'                  =>  'required|max:128',
        ],
        [
            'name.required'         =>  'El Nombre es Requerido',
            'name.max'              =>  'El Nombre solo puede tener hasta 128 caracteres',
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        if($request->exists('visa_types'))
        {
            $visaTypes = explode(',', $request->visa_types);
            for($i = 0; $i < count($visaTypes); $i++)
            {
                $auxVSTVT = explode('-', $visaTypes[$i]);
                $vT = str_replace('VT:', '', $auxVSTVT[0]);

                if(VisaType::where('id', $vT)->count() == 0)
                    return response()->json(['errors' => "El Tipo de Visa con ID $vT no existe"], 422);
            }
        }

        $visaStepType = new VisaStepType();

        $visaStepType->name = $request->name;

        if($request->exists('client_description'))
            $visaStepType->client_description = $request->client_description;


        if($request->exists('advisor_description'))
            $visaStepType->advisor_description = $request->advisor_description;

        if($visaStepType->save()) {

            if($request->exists('visa_types'))
            {
                $visaTypes = explode(',', $request->visa_types);
                for($i = 0; $i < count($visaTypes); $i++)
                {

                    $auxVSTVT = explode('-', $visaTypes[$i]);
                    $vT = str_replace('VT:', '', $auxVSTVT[0]);
                    $nC = str_replace('NC:', '', $auxVSTVT[1]);
                    $nA = str_replace('NA:', '', $auxVSTVT[2]);

                    $arrAux = [];

                    if($nC != '') $arrAux['number_client'] = $nC;
                    if($nA != '') $arrAux['number_advisor'] = $nA;

                    $visaStepType->visaType()->attach($vT, $arrAux);
                }
            }

            return response()->json(['status' => 'success'], 200);
        }

        return response()->json(['errors'   =>  'No se pudo Crear el Tipo de Paso'], 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VisaStepType  $visaStepType
     * @return \Illuminate\Http\Response
     */
    public function show($idVisaStepType)
    {
        if(VisaStepType::where('id', $idVisaStepType)->count() == 0)
            return response()->json(['errors'   => 'El Tipo de Paso no existe'], 422);

        $visaStepType = VisaStepType::select('id as id_visa_step_type', 'name as name_visa_step_type', 'client_description as client_description_visa_step_type', 'advisor_description as advisor_description_visa_step_type', 'created_at as created_at_visa_step_type', 'updated_at as updated_at_visa_step_type')
                                        ->where('id', $idVisaStepType)
                                        ->first();

        $visaTypes = VisaType::leftJoin('visa_step_type_visa_type', 'visa_step_type_visa_type.visa_type_id', '=', 'visa_types.id')
                                        ->where('visa_step_type_visa_type.visa_step_type_id', $idVisaStepType)
                                        ->select('visa_types.id as id_visa_type', 'visa_types.name as name_visa_type', 'visa_step_type_visa_type.number_client as number_client', 'visa_step_type_visa_type.number_advisor as number_advisor')
                                        ->get();

        $numbersVCA = VisaType::leftJoin('visa_step_type_visa_type', 'visa_step_type_visa_type.visa_type_id', '=', 'visa_types.id')
                                        ->where('visa_step_type_visa_type.visa_step_type_id', $idVisaStepType)
                                        ->select('visa_types.id as id_visa_type', 'visa_step_type_visa_type.number_client as number_client', 'visa_step_type_visa_type.number_advisor as number_advisor')
                                        ->get();

        $visaStepType['visa_types'] = $visaTypes;

        $visaStepType['numbers_client_advisor'] = $numbersVCA;


        return response()->json([
            'status'            =>  'success',
            'visaStepType'      =>  $visaStepType,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\VisaStepType  $visaStepType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idVisaStepType)
    {
        $visaStepType = VisaStepType::find($idVisaStepType);

        if(!$visaStepType)
            return response()->json(['errors'   => 'El Tipo de Paso no existe'], 422);

        $validator = Validator::make($request->all(),
        [
            'name'                  =>  'max:128',
        ],
        [
            'name.max'              =>  'El Nombre solo puede tener hasta 128 caracteres'
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        if($request->exists('visa_types'))
        {
            $visaTypes = explode(',', $request->visa_types);

            for($i = 0; $i < count($visaTypes); $i++)
            {
                $auxVSTVT = explode('-', $visaTypes[$i]);
                $vT = str_replace('VT:', '', $auxVSTVT[0]);

                if(VisaType::where('id', $vT)->count() == 0)
                    return response()->json(['errors' => "El Tipo de Visa con ID $vT no existe"], 422);
            }

            $visaStepType->visaType()->detach();

            for($i = 0; $i < count($visaTypes); $i++)
            {

                $auxVSTVT = explode('-', $visaTypes[$i]);
                $vT = str_replace('VT:', '', $auxVSTVT[0]);
                $nC = str_replace('NC:', '', $auxVSTVT[1]);
                $nA = str_replace('NA:', '', $auxVSTVT[2]);

                $arrAux = [];

                if($nC != '') $arrAux['number_client'] = $nC;
                if($nA != '') $arrAux['number_advisor'] = $nA;

                $visaStepType->visaType()->attach($vT, $arrAux);
            }
        }

        if($request->exists('name'))
            $visaStepType->name = $request->name;

        if($request->exists('client_description'))
            $visaStepType->client_description = $request->client_description;


        if($request->exists('advisor_description'))
            $visaStepType->advisor_description = $request->advisor_description;

        if($visaStepType->save())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors'   =>  'No se pudo Editar el Tipo de Paso'], 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VisaStepType  $visaStepType
     * @return \Illuminate\Http\Response
     */
    public function destroy($idVisaStepType)
    {
        $visaStepType = VisaStepType::find($idVisaStepType);

        if(!$visaStepType)
            return response()->json(['errors'   => 'El Tipo de Paso no existe'], 422);

        $visaStepType->visaType()->detach();

        if($visaStepType->delete())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors'   =>  'No se pudo Eliminar el Tipo de Paso'], 422);

    }
}
