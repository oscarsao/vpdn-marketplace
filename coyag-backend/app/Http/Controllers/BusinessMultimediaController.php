<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessMultimedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use \Gumlet\ImageResize;

class BusinessMultimediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /* Los recursos multimedias NO deberían listarse
         * ya que estos están atado a un negocio (business)
         */
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Puede almacenar uno por vez

        $validator = Validator::make($request->all(), [
            'business_id'   =>  'required|numeric',
            'type'          =>  'required'
        ],
        [
            'business_id.required'   =>  'El ID del Negocio es requerido',
            'business_id.numeric'    =>  'El ID del Negocio debe ser del tipo numérico',
            'type.required'          =>  'El Tipo del Recurso Multimedia es requerido'
        ]);

        if($validator->fails())
            return response()->json(["errors" => $validator->errors()], 422);

        if(Business::where('id', $request->business_id)->count() >= 0)
        {
            $businessMultimedia = new BusinessMultimedia();
            $businessMultimedia->business_id = $request->business_id;
            $businessMultimedia->type = $request->type;

            if($request->type == 'image' || $request->type == 'print'){
                $auxIMG = $request->image;

                if(!in_array(strtolower($auxIMG->extension()), ['jpg', 'jpeg', 'png']))
                    return response()->json(['errors' => 'Error la imagen debe ser jpg, jpeg o png'], 422);

                $path = createFolder($request->business_id, $request->type);

                $businessMultimedia->original_name    = $auxIMG->getClientOriginalName();
                $businessMultimedia->extension        = $auxIMG->extension();
                $businessMultimedia->size             = $auxIMG->getSize();
                $businessMultimedia->mime_type        = $auxIMG->getMimeType();
                $businessMultimedia->path             = $path[0];
                $auxName = Str::random(12). '.' . $auxIMG->extension();
                $businessMultimedia->large_image_path = $path[0] . '/' . $auxName;
                $auxIMG->move($path[1], $auxName);

                if($request->type == 'image') {
                    $fullPathNewImage = $path[0] . '/' . Str::random(12) . '.jpeg';
                    $image = new ImageResize($businessMultimedia->large_image_path);
                    $image->crop(524, 359, $allow_enlarge = true);
                    // $image->resize(524, 359);
                    $image->save($fullPathNewImage, IMAGETYPE_JPEG, 100);
                    $businessMultimedia->small_image_path = $fullPathNewImage;

                    $fullPathNewImage = $path[0] . '/' . Str::random(12) . '.jpeg';
                    $image = new ImageResize($businessMultimedia->large_image_path);
                    $image->resizeToWidth(1200);
                    $image->save($fullPathNewImage, IMAGETYPE_JPEG, 100);
                    $businessMultimedia->medium_image_path = $fullPathNewImage;
                }

                if(!$businessMultimedia->save())
                    return response()->json(['errors' => 'Error al guardar el Recurso Multimedia'], 422);

            }
            else
            {
                if($request->exists('type_client'))
                    $businessMultimedia->type_client = $request->type_client;

                $businessMultimedia->link_video = $request->link_video;

                if(!$businessMultimedia->save())
                    return response()->json(['errors' => 'Error al guardar el Recurso Multimedia'], 422);
            }

            addBusinessTimeline($businessMultimedia->business_id, 'BusinessMultimedia', 'create', $businessMultimedia->id);

            return response()->json(['status' => 'success'], 200);
        }

        return response()->json(['errors' => 'El Negocio no existe'], 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($idBusinessMultimedia)
    {
        /* Por ahora no tiene utilidad mostrar un solo recurso
         * ya que este está atado a un negocio (business)
         **/

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idBusinessMultimedia)
    {
        /* Un recurso Multimedia NO puede actualizarse,
         * solo agregar o borrar
         */
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($idBusinessMultimedia)
    {
        // Borra uno por vez

        if(BusinessMultimedia::where('id', $idBusinessMultimedia)->count() > 0)
        {
            $businessMultimedia = BusinessMultimedia::find($idBusinessMultimedia);

            if($businessMultimedia->type != 'video')
                deleteResourceBusinessMultimedia($businessMultimedia);

            addBusinessTimeline($businessMultimedia->business_id, 'BusinessMultimedia', 'delete', $businessMultimedia->id);

            if($businessMultimedia->delete())
            {
                return response()->json(['status' => 'success'], 200);
            }

            return response()->json(['errors' => 'No se pudo borrar el Elemento Multimedia de Negocio'], 422);
        }

        return response()->json(['errors' => 'El Elemento Multimedia del Negocio no existe'], 422);
    }

    public function downloadPrintScreenFile($idBusinessMultimedia)
    {

        $businessMultimedia = BusinessMultimedia::find($idBusinessMultimedia);

        if($businessMultimedia)
        {

            if($businessMultimedia->type != 'print')
                return response()->json(['El Recurso Multimedia que trata de descargar no es una Captura de Pantalla'], 422);

            $headers = array(
                "Content-Type: $businessMultimedia->mime_type",
                );

            return response()->download($businessMultimedia->large_image_path, $businessMultimedia->original_name, $headers);
        }

        return response()->json(['El Recurso Multimedia No existe'], 422);
    }


}
