<?php

use App\Models\Business;
use App\Models\BusinessMultimedia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use \Gumlet\ImageResize;

function addResourceBusinessMultimedia(Business $business, Request $request)
{
    $errorsMultimedia = array();

    for($i = 1; $i <= 8; $i++)
    {
        $aux = "print_$i";
        if($request->exists($aux))
        {
            addBusinessImage($business, $request, $aux, 'print', $errorsMultimedia);
        }
    }

    for($i = 1; $i <= 16; $i++)
    {
        $aux = "image_$i";

        if($request->exists($aux))
        {
            addBusinessImage($business, $request, $aux, 'image', $errorsMultimedia);
        }
    }

    if($request->exists('video_free'))
        addBusinessVideo($request, $business->id, "usuario.free");

    if($request->exists('video_registrado'))
        addBusinessVideo($request, $business->id, "cliente.registrado");

    if($request->exists('video_lite'))
        addBusinessVideo($request, $business->id, "usuario.lite");

    if($request->exists('video_estandar'))
        addBusinessVideo($request, $business->id, "usuario.estandar");

    if($request->exists('video_premium'))
        addBusinessVideo($request, $business->id, "usuario.premium");

    return $errorsMultimedia;
}

function addBusinessVideo(request $request, $idBusiness, $typeClient)
{
    $auxVideo;
    $aux;

    switch($typeClient)
    {
        case "usuario.free":    $auxVideo = "video_free";
                                $aux = "free";
                                break;

        case "cliente.registrado": $auxVideo = "video_registrado";
                                $aux = "registrado";
                                break;

        case "usuario.lite":    $auxVideo = "video_lite";
                                $aux = "lite";
                                break;

        case "usuario.estandar": $auxVideo = "video_estandar";
                                $aux = "estandar";
                                break;

        case "usuario.premium": $auxVideo = "video_premium";
                                $aux = "premium";
                                break;
    }

    $businessMultimedia = new BusinessMultimedia();
    $businessMultimedia->business_id    = $idBusiness;
    $businessMultimedia->type           = 'video';
    $businessMultimedia->link_video     = $request->$auxVideo;
    $businessMultimedia->type_client    = $typeClient;

    if(!$businessMultimedia->save()) {
        $auxError = "video-$aux-errorGuardado";
        array_push($errorsMultimedia, $auxError);
    }
}

function addBusinessImage(Business $business, Request $request, $aux, $type, &$errorsMultimedia)
{
    $auxIMG = $request->$aux;
    if(in_array(strtolower($auxIMG->extension()), ['jpg', 'jpeg', 'png'])){

        $path = createFolder($business->id, $type);

        $businessMultimedia = new BusinessMultimedia();

        $businessMultimedia->business_id = $business->id;

        $businessMultimedia->type             = $type;
        $businessMultimedia->original_name    = $auxIMG->getClientOriginalName();
        $businessMultimedia->extension        = $auxIMG->extension();
        $businessMultimedia->size             = $auxIMG->getSize();
        $businessMultimedia->mime_type        = $auxIMG->getMimeType();
        $businessMultimedia->path             = $path[0];
        $auxName = Str::random(12) . '.' . $auxIMG->extension();
        $businessMultimedia->large_image_path = $path[0] . '/' . $auxName;
        $auxIMG->move($path[1], $auxName);

        if($type == 'image') {
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

        if(!$businessMultimedia->save()) {
            $auxError = $type."-errorGuardado-" . $aux;
            array_push($errorsMultimedia, $auxError);
        }
    }
    else{
        $auxError = $type."-errorExtensión-" . $auxIMG->getClientOriginalName();
        array_push($errorsMultimedia, $auxError);
    }
}

function createFolder($idBusiness, $type)
{
    $auxPath = "files/business-multimedia/$idBusiness/$type";
    $path = public_path($auxPath);
    if (!is_dir($path)) {
        Storage::makeDirectory($path);
    }

    return array($auxPath, $path);
}

function deleteResourceBusinessMultimedia(BusinessMultimedia $businessMultimedia)
{
    if(File::exists(public_path($businessMultimedia->large_image_path))){
        File::delete(public_path($businessMultimedia->large_image_path));
    }else{
        Log::error("Error - COYAG ->  Error deleteResourceBusinessMultimedia: No borró el archivo $businessMultimedia->full_path ID $businessMultimedia->id");
    }

    if(File::exists(public_path($businessMultimedia->medium_image_path))){
        File::delete(public_path($businessMultimedia->medium_image_path));
    }else{
        Log::error("Error - COYAG ->  Error deleteResourceBusinessMultimedia: No borró el archivo $businessMultimedia->full_path ID $businessMultimedia->id");
    }

    if(File::exists(public_path($businessMultimedia->small_image_path))){
        File::delete(public_path($businessMultimedia->small_image_path));
    }else{
        Log::error("Error - COYAG ->  Error deleteResourceBusinessMultimedia: No borró el archivo $businessMultimedia->full_path ID $businessMultimedia->id");
    }
}
