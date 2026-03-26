<?php

namespace App\Http\Controllers;

use App\Models\VideoCall;
use App\Models\VideoCallType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VideoCallTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $videoCallTypes;

        if(isset(Auth::user()->employee->id))
            $videoCallTypes = VideoCallType::select('video_call_types.id as id_video_call_type', 'video_call_types.name as name_video_call_type', 'video_call_types.slug as slug_video_call_type', 'video_call_types.description as description_video_call_type', 'video_call_types.business_number as business_number_video_call_type', 'video_call_types.created_at as created_at_video_call_type', 'video_call_types.updated_at as updated_at_video_call_type');
        else
            $videoCallTypes = VideoCallType::select('video_call_types.id as id_video_call_type', 'video_call_types.name as name_video_call_type');

        $videoCallTypes = $videoCallTypes->get();

        return response()->json([
            'status'    =>  'success',
            'videoCallTypes'    =>  $videoCallTypes,
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
            'name'          =>  'required|max:128|unique:video_call_types',
            'slug'          =>  'required|max:64|unique:video_call_types',
        ],
        [
            'name.required'         =>  'El Nombre es Requerido',
            'name.max'              =>  'El Nombre no debe pasar de 128 caracteres',
            'name.unique'           =>  'El Nombre ya está siendo utilizado',
            'slug.required'         =>  'El Slug es Requerido',
            'slug.max'              =>  'El Slug no debe pasar de 64 caracteres',
            'slug.unique'           =>  'El Slug ya está siendo utilizado',
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);


        $videoCallType = new VideoCallType();
        $videoCallType->name = $request->name;
        $videoCallType->slug = $request->slug;
        if($request->exists('description'))
        {
            if(strlen($request->description) > 255)
                return response()->json(['errors' => 'La Descripción no debe pasar de 255 caracteres'], 422);

            $videoCallType->description = $request->description;
        }

        if($request->exists('business_number'))
        {
            if(in_array($request->business_number, ['0', '1', 'max']))
                $videoCallType->business_number = $request->business_number;
            else
                return response()->json(['errors'   =>  'El número de negocios debe tener los valores 0, 1 o max'], 422);
        }

        if($videoCallType->save())
            return response()->json(['status' => 'success'], 200);

        return response()->json(['errors' => 'No se pudo guardar el Tipo de Videollamada'], 422);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VideoCallType  $videoCallType
     * @return \Illuminate\Http\Response
     */
    public function show($idVideoCallType)
    {
        if(VideoCallType::where('id', $idVideoCallType)->count() > 0)
        {
            $videoCallType = VideoCallType::select('video_call_types.id as id_video_call_type', 'video_call_types.name as name_video_call_type', 'video_call_types.slug as slug_video_call_type', 'video_call_types.description as description_video_call_type', 'video_call_types.business_number as business_number_video_call_type', 'video_call_types.created_at as created_at_video_call_type', 'video_call_types.updated_at as updated_at_video_call_type')
                                        ->where('id', $idVideoCallType)
                                        ->get();

            return response()->json([
                'status'    =>  'success',
                'videoCallType'    =>  $videoCallType,
            ], 200);
        }

        return response()->json(['errors' => 'No existe el Tipo de Videollamada'], 422);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\VideoCallType  $videoCallType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idVideoCallType)
    {
        if(VideoCallType::where('id', $idVideoCallType)->count() > 0)
        {
            $videoCallType = VideoCallType::find($idVideoCallType);

            if($request->exists('name'))
            {
                if(strlen($request->name) > 128)
                    return response()->json(['errors' => 'El Nombre no debe pasar de 128 caracteres'], 422);

                if(VideoCallType::where('name', $request->name)->count() > 0)
                    return response()->json(['errors' => 'El Nombre ya está siendo utilizado'], 422);


                $videoCallType->name = $request->name;
            }

            if($request->exists('slug'))
            {
                if(strlen($request->slug) > 64)
                    return response()->json(['errors' => 'El Slug no debe pasar de 64 caracteres'], 422);

                if(VideoCallType::where('slug', $request->slug)->count() > 0)
                    return response()->json(['errors' => 'El Slug ya está siendo utilizado'], 422);

                $videoCallType->slug = $request->slug;
            }

            if($request->exists('description'))
            {
                if(strlen($request->description) > 255)
                    return response()->json(['errors' => 'La Descripción no debe pasar de 255 caracteres'], 422);

                $videoCallType->description = $request->description;
            }

            if($request->exists('business_number'))
            {
                if(in_array($request->business_number, ['0', '1', 'max']))
                    $videoCallType->business_number = $request->business_number;
                else
                    return response()->json(['errors'   =>  'El número de negocios debe tener los valores 0, 1 o max'], 422);
            }

            if($videoCallType->save())
                return response()->json(['status' => 'success'], 200);

            return response()->json(['errors' => 'No se pudo actualizar el Tipo de Videollamada'], 422);
        }

        return response()->json(['errors' => 'No existe el Tipo de Videollamada'], 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VideoCallType  $videoCallType
     * @return \Illuminate\Http\Response
     */
    public function destroy($idVideoCallType)
    {
        $videoCallType = VideoCallType::find($idVideoCallType);

        if($videoCallType)
        {
            if(VideoCall::where('video_call_type_id', $idVideoCallType)->count() > 0)
                return response()->json(['errors'   =>  'No se puede borrar porque hay Videollamadas asociadas a este tipo'], 422);

            if($videoCallType->delete())
                return response()->json(['status'   =>  'success'], 200);

            return response()->json(['errors'   =>  'No se pudo borrar el tipo de Videollamada'], 422);
        }

        return response()->json(['errors' => 'No existe el Tipo de Videollamada'], 422);
    }
}
