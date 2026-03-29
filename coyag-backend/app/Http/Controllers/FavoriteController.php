<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\FavoriteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FavoriteController extends Controller
{

    private $favoriteService;

    public function __construct() {
        $this->favoriteService = new FavoriteService();
    }

    /**
     * List the authenticated client's favorite businesses.
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $client = $user->client ?? null;
            if (!$client) {
                return response()->json([
                    'status'         => 'success',
                    'favorites_list' => [],
                ]);
            }

            $favorites = $client->businesses()
                ->where('flag_active', 1)
                ->get(['businesses.id', 'businesses.id_code', 'businesses.name', 'businesses.investment', 'businesses.rental', 'businesses.size', 'businesses.lat', 'businesses.lng', 'businesses.business_images_string']);

            return response()->json([
                'status'         => 'success',
                'favorites_list' => $favorites,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'         => 'success',
                'favorites_list' => [],
            ]);
        }
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeFavorite(Request $request)
    {
        /**
         * Método solo usado por el cliente
         */
        $validator = Validator::make($request->all(),
        [
            'business_id'     =>  'required|numeric|exists:businesses,id',
            'flag_favorite'   =>  'required|numeric|min:0|max:1'
        ],
        [
            'business_id.required'     =>  'El ID del negocio es requerido',
            'business_id.numeric'      =>  'El ID del negocio debe ser del tipo numérico',
            'business_id.exists'       =>  'El Negocio no existe',
            'flag_favorite.required'   =>  'El Flag Favorite es requerido',
            'flag_favorite.numeric'    =>  'El Flag Favorite debe ser numérico',
            'flag_favorite.min'        =>  'El Flag Favorite no puede ser menor a 0 (False)',
            'flag_favorite.max'        =>  'El Flag Favorite no puede ser mayor a 1 (Verdadero)',
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        if($request->flag_favorite)
            Auth::user()->client->businesses()->attach($request->business_id);
        else
            Auth::user()->client->businesses()->detach($request->business_id);

        return response()->json(['status'   => 'success'], 200);
    }

    public function getFavoritesList(Request $request, $idClient)
    {

        $data = $this->favoriteService->getFavoritesListOfAClient($request, $idClient);

        if($data->status == 'success')
            return response()->json([
                'status'            => $data->status,
                'favorites_list'    => $data->favoritesList
            ], $data->code);
        else
            return response()->json([
                'errors'   =>  $data->errors
            ], $data->code);

    }
}
