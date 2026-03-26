<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessMultimedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MaintenanceController extends Controller
{
    public function oldBusinessCD()
    {
        /**
         * Elimina las imágenes de BD y Disco con ID menor o igual a 595
         */

        for ($i=2; $i <= 595; $i++) {
            $arrBusinessMultimedia = BusinessMultimedia::where('business_id', $i)
                        ->where('type', 'image')
                        ->get();

            foreach($arrBusinessMultimedia as $bM)
            {
                deleteResourceBusinessMultimedia($bM);
                $bM->delete();
            }
        }

        return response()->json(['status' => 'success'], 200);
    }

    public function maxImagesCD(Request $request)
    {
        /*
            Elimina el excedente de imágenes de un negocio, por ende recibe,
            el máximo de imagenes que pueden tener los negocios
        */

        $validator = Validator::make($request->all(), [
            'max_images'            =>  'required|numeric|between:8,12',
        ],
        [
            'max_images.required'       =>  'max_images es Requerido',
            'max_images.numeric'        =>  'max_images debe ser del tipo numérico',
            'max_images.between'        =>  'max_images debe estar entre 8 y 12 (ambos incluidos)',
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);


        $countBM = BusinessMultimedia::where('type', 'image')
                        ->select('business_id', DB::raw('count(*) as total_bm'))
                        ->groupBy('business_id')
                        ->get();

        foreach ($countBM as $key => $value) {
            if($value->total_bm > $request->max_images) {
                $arrBusinessMultimedia = BusinessMultimedia::where('business_id', $value->business_id)
                                        ->where('type', 'image')
                                        ->orderBy('business_multimedia.id', 'DESC')
                                        ->take($value->total_bm - $request->max_images)
                                        ->get();

                foreach($arrBusinessMultimedia as $bM)
                {
                    deleteResourceBusinessMultimedia($bM);
                    $bM->delete();
                }
            }

        }

        return response()->json(['status' => 'success'], 200);

    }


}
