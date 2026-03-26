<?php


namespace App\Services;

use \stdClass;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteService
{

    public function getFavoritesListOfAClient(Request $request, $idClient)
    {
        $client = Client::find($idClient);

        $data = new stdClass();

        if(!$client)
        {
            $data->code     = 422;
            $data->status   = 'errors';
            $data->errors   = ['client_id' => ['El Cliente no existe']];
            return $data;
        }

        $favoritesList =  DB::table('favorites')
                                ->leftJoin('businesses', 'businesses.id', '=', 'favorites.business_id')
                                ->leftJoin('municipalities', 'municipalities.id', '=', 'businesses.municipality_id')
                                ->leftJoin('provinces', 'provinces.id', '=', 'municipalities.province_id')
                                ->leftJoin('business_types', 'business_types.id', '=', 'businesses.business_type_id')
                                ->where('favorites.client_id', $idClient)
                                ->select('favorites.id as id_favorite', 'favorites.created_at as created_at_favorite',
                                            'businesses.id as id_business', 'businesses.id_code as id_code_business',
                                            'businesses.name as name_business', 'businesses.flag_active as flag_active_business',
                                            'municipalities.id as id_municipality',  'municipalities.name as name_municipality',
                                            'provinces.id as id_province',  'provinces.name as name_province',
                                            'business_types.id as id_business_type', 'business_types.name as name_business_type')
                                ->get();

        foreach ($favoritesList as $key => $favorite)
        {
            $sectorBusiness = DB::table('business_sector')
                                ->leftJoin('sectors', 'sectors.id', '=', 'business_sector.sector_id')
                                ->where('business_id', $favorite->id_business)
                                ->select('sectors.id as id', 'sectors.name as name')
                                ->get();

            $sectorName = '';

            foreach ($sectorBusiness as $key => $sector) {
                $sectorName = $sectorName == '' ? "{$sector->id}-{$sector->name}"
                                                    : "{$sectorName},{$sector->id}-{$sector->name}";
            }

            $favorite->sector_name = $sectorName;
        }

        $data->code     = 200;
        $data->status   = 'success';
        $data->favoritesList = $favoritesList;

        return $data;
    }

}
