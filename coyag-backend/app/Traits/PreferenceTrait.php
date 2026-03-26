<?php

namespace App\Traits;

use App\Models\Business;
use App\Models\BusinessClient;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

trait PreferenceTrait {
    /*
        Recordando que las Preferencias de Negocios de los Clientes se encuentran
        en la relación business_clients, no confundir con recomendaciones
    */

    public static function checkBusinessClientPreference(Business $business, $isNotification = false) {

        // TODO: Esto debe desaparecer, buscar donde se hace el llamado y editar o reusar lo que se necesite

        $arrClients = [];

        foreach (BusinessClient::all() as $bClient)
        {
            $auxBool = false;

            // Filtro por listado de Provincias

            if(
                !empty($business->municipality) &&
                in_array($business->municipality->province_id, explode(',', $bClient->provinces_list))
            ) $auxBool = true;

            // Filtro por Monto de Inversión

            if(($bClient->min_investment  != null) && ($bClient->max_investment  != null))
            {
                if(($bClient->min_investment <= $business->investment) && ($business->investment <= $bClient->max_investment))
                    $auxBool = true;
            }
            else
            {
                if(($bClient->min_investment != null) && $bClient->min_investment <= $business->investment)
                    $auxBool = true;

                if(($bClient->max_investment != null) && ($business->investment <= $bClient->max_investment))
                    $auxBool = true;
            }

            // Filtro por Listado de Sector

            // Sectores a los que pertenece el negocio
            $bSectors = DB::table('business_sector')
                                ->where('business_id', $business->id)
                                ->get();

            // Sectores que seleccionó el cliente
            $arrSectors = explode(',', $bClient->sectors_list);

            foreach($bSectors as $bSector) {
                if(in_array($bSector->sector_id, $arrSectors)) {
                    $auxBool = true;
                    break;
                }
            }

            // Filtro por Listado de Tipos de Negocio

            if(in_array($business->business_type_id, explode(',', $bClient->business_types_list)))
                $auxBool = true;


            if($auxBool)
            {
                array_push($arrClients, $bClient->client_id);

                if($isNotification)
                    notificationNegocioCumpleConPreferenciasDelCliente($bClient->client_id, $business);
            }

        }

        return $arrClients;

    }


    public function getBusinessListofAClient($idClient) : array {
        /**
         * Obtiene el listado de negocios de un cliente con los
         * cuales hace match acorde a su preferencia
         */

         $bClient = BusinessClient::where('client_id', $idClient)->first();

         if(empty($bClient) || empty( $bClient->business_list))
            return Business::where('flag_active', true)->inRandomOrder()->limit(12)->pluck('id')->toArray();
         else
            return $bClient->business_list;


         return $bClient->business_list;

    }

    public function updateBusinessListForAClient($idClient): void {
        /**
         * Actualiza el listado de negocios de un cliente
         * con los cuales hace match acorde a su preferencia
         */

        $bClient = BusinessClient::where('client_id', $idClient)->first();

        if(empty($bClient))
            return;

        $listBusiness = $this->getListBusinessMatch($bClient);

        // Evito guardar arreglos vacíos, prefiero que sean null
        $bClient->business_list = empty($listBusiness)  ? null
                                                        : $listBusiness;


        $bClient->save();

    }

    public function updateBusinessListForAllClients(): void {
        /**
         * Actualiza el listado de negocios de todos los clientes
         * con los cuales hace match acorde a su preferencia
         */

        $clients = Client::leftJoin('added_services', 'added_services.client_id', '=', 'clients.id')
                            ->leftJoin('services', 'services.id', '=', 'added_services.service_id')
                            ->where('services.type', 'Plan')
                            ->where('added_services.flag_active_plan', true)
                            ->pluck('clients.id')
                            ->toArray();

        foreach (Client::whereIn('id', $clients)->get() as $client) {
            if(empty($client->businessClient)) continue;
            $listBusiness = $this->getListBusinessMatch($client->businessClient);
            // Evito guardar arreglos vacíos, prefiero que sean null
            $client->businessClient->business_list = empty($listBusiness) ? null : $listBusiness;
            $client->businessClient->save();
        }

    }

    private function getListBusinessMatch($bClient) : array {
        /**
         * Devuelve los negocios con los cuales hace match un cliente
         */

        $arrBusiness = [];

        if(
            empty($bClient->provinces_list) &&
            empty($bClient->min_investment) &&
            empty($bClient->max_investment) &&
            empty($bClient->sectors_list) &&
            empty($bClient->business_types_list)
        ) return $arrBusiness;

        foreach (Business::where('flag_active', true)->get() as $business) {
            // Filtro por listado de Provincias
            if(!empty($bClient->provinces_list)) {
                if (
                    !empty($business->municipality) &&
                    !in_array($business->municipality->province_id, explode(',', $bClient->provinces_list))
                ) continue;
            }

            // Filtro por Monto de Inversión Mínima
            if(!empty($bClient->min_investment)) {
                if($bClient->min_investment > $business->investment) continue;
            }

            // Filtro por Monto de Inversión Máxima
            if(!empty($bClient->max_investment)) {
                if($bClient->max_investment < $business->investment) continue;
            }

            // Sectores a los que pertenece el negocio
            if(!empty($bClient->sectors_list)) {
                $bSectors = DB::table('business_sector')->where('business_id', $business->id)->get();
                // Sectores que seleccionó el cliente
                $arrSectors = explode(',', $bClient->sectors_list);
                $auxBool = false;
                foreach($bSectors as $bSector) {
                    if(in_array($bSector->sector_id, $arrSectors)) {
                        $auxBool = true;
                        break;
                    }
                }
                if(!$auxBool) continue;
            }

            if(!empty($bClient->business_types_list)) {
                // Filtro por Listado de Tipos de Negocio
                if(!in_array($business->business_type_id, explode(',', $bClient->business_types_list))) continue;
            }
            array_push($arrBusiness, $business->id);
        }
        return $arrBusiness;
    }
}
