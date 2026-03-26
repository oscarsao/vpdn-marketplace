<?php

namespace App\Services;

use App\Models\Business;
use App\Models\District;
use App\Models\Municipality;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MunicipalityUDService
{

    /**
     * Proceso:
     *
     * 1 - Detectar Municipio Repetido
     *      - Obtener el ID del Municipio incorrecto
     *      - Obtener el ID del Municipio correcto
     *
     * 2 - Agregar error en: CorrectNameTrait
     *      Evitará que se siga cargando dicho error en la importación de negocios
     *
     * 3 - Actualizar municipality_id de Distritos
     *      Del ID incorrecto al correcto
     *
     * 4 - Actualizar municipality_id de Negocios
     *      Del ID incorrecto al correcto
     *
     * 5 - Eliminar Municipio Duplicado
     */

    /**
     * MunicipalityUDService === MunicipalityUpdateDataService
     *
     * Service que servirá para actualizar los municipios, especialmente,
     * eliminar los duplicados
     *
     * Esta clase será referenciada desde UpdateDataController
     */

    public function madridProvince()
    {
        /**
         * ID de Provincia de Madrid = 31
         */
        $this->noticePoster('Inicio de actualización de los Municipios de la Provincia de Madrid');

        // Municipio Correcto: Alameda del Valle
        Log::info("Actualizando: 3 - Alameda del Valle");
        $this->updateProcess([315], 3);

        // Municipio Correcto: Alcorcón
        Log::info("Actualizando: 7 - Alcorcón");
        $this->updateProcess([307], 7);

        // Municipio Correcto: Algete
        Log::info("Actualizando: 9 - Algete");
        $this->updateProcess([278, 331], 9);

        // Municipio Correcto: El Álamo
        Log::info("Actualizando: 4 - El Álamo");
        $this->updateProcess([282, 341], 4);

        // Municipio Correcto: Alcalá de Henares
        Log::info("Actualizando: 5 - Alcalá de Henares");
        $this->updateProcess([248, 336, 334], 5);

        // Municipio Correcto: Alcobendas
        Log::info("Actualizando: 6 - Alcobendas");
        $this->updateProcess([234, 268, 276, 310], 6);

        // Municipio Correcto: Arganda del Rey
        Log::info("Actualizando: 14 - Arganda del Rey");
        $this->updateProcess([263], 14);

        // Municipio Correcto: Arroyomolinos
        Log::info("Actualizando: 15 - Arroyomolinos");
        $this->updateProcess([261, 342], 15);

        // Municipio Correcto: Ciempozuelos
        Log::info("Actualizando: 42 - Ciempozuelos");
        $this->updateProcess([232], 42);

        // Municipio Correcto: Colmenarejo
        Log::info("Actualizando: 49 - Colmenarejo");
        $this->updateProcess([1547], 49);

        // Municipio Correcto: Coslada
        Log::info("Actualizando: 51 - Coslada");
        $this->updateProcess([285], 51);

        // Municipio Correcto: Cubas de la Sagra
        Log::info("Actualizando: 52 - Cubas de la Sagra");
        $this->updateProcess([256, 274], 52);

        // Municipio Correcto: El Boalo
        Log::info("Actualizando: 23 - El Boalo");
        $this->updateProcess([314], 23);

        // Municipio Correcto: Estremera
        Log::info("Actualizando: 55 - Estremera");
        $this->updateProcess([237], 55);

        // Municipio Correcto: Fuente el Saz de Jarama
        Log::info("Actualizando: 59 - Fuente el Saz de Jarama");
        $this->updateProcess([319], 59);

        // Municipio Correcto: Getafe
        Log::info("Actualizando: 65 - Getafe");
        $this->updateProcess([269, 321, 324], 65);

        // Municipio Correcto: Griñón
        Log::info("Actualizando: 66 - Griñón");
        $this->updateProcess([280], 66);

        // Municipio Correcto: Guadalix de la Sierra
        Log::info("Actualizando: 67 - Guadalix de la Sierra");
        $this->updateProcess([243], 67);

        // Municipio Correcto: Humanes de Madrid
        Log::info("Actualizando: 73 - Humanes de Madrid");
        $this->updateProcess([255, 238], 73);

        Log::info("Actualizando: 74 - Leganés");
        $this->updateProcess([259, 317], 74);

        // Municipio Correcto: Las Rozas de Madrid
        Log::info("Actualizando: 124 - Las Rozas de Madrid");
        $this->updateProcess([241, 323, 312], 124);

        // Municipio Correcto: Lozoyuela-Navas-Sieteiglesias
        Log::info("Actualizando: 77 - Lozoyuela-Navas-Sieteiglesias");
        $this->updateProcess([258], 77);

        // Municipio Correcto: Madrid
        Log::info("Actualizando: 79 - Madrid");
        $this->updateProcess([260, 306, 309, 242, 244, 245, 249, 252, 253, 254, 272, 273, 275, 288, 316, 343, 804, 2545, 1546, 267, 279, 281, 308, 335, 1545], 79);

        // Municipio Correcto: Majadahonda
        Log::info("Actualizando: 80 - Majadahonda");
        $this->updateProcess([287], 80);

        // Municipio Correcto: Moraleja de Enmedio
        Log::info("Actualizando: 88 - Moraleja de Enmedio");
        $this->updateProcess([240], 88);

        // Municipio Correcto: Moralzarzal
        Log::info("Actualizando: 89 - Moralzarzal");
        $this->updateProcess([257], 89);

        // Municipio Correcto: Parla
        Log::info("Actualizando: 102 - Parla");
        $this->updateProcess([270], 102);

        // Municipio Correcto: Rivas-Vaciamadrid
        Log::info("Actualizando: 120 - Rivas-Vaciamadrid");
        $this->updateProcess([239, 271, 283], 120);

        // Municipio Correcto: San Agustín del Guadalix
        Log::info("Actualizando: 126 -  San Agustín del Guadalix");
        $this->updateProcess([264, 266, 803], 126);

        // Municipio Correcto: San Fernando de Henares
        Log::info("Actualizando: 127 - San Fernando de Henares");
        $this->updateProcess([286], 127);

        // Municipio Correcto: San Lorenzo de El Escorial
        Log::info("Actualizando: 128 -  San Lorenzo de El Escorial");
        $this->updateProcess([318], 128);

        // Municipio Correcto: San Sebastián de los Reyes
        Log::info("Actualizando: 131 - San Sebastián de los Reyes");
        $this->updateProcess([265, 311, 322, 1580], 131);

        // Municipio Correcto: Soto del Real
        Log::info("Actualizando: 139 -  Soto del Real");
        $this->updateProcess([333], 139);

        // Municipio Correcto: Torrejón de Ardoz
        Log::info("Actualizando: 143 - Torrejón de Ardoz");
        $this->updateProcess([320], 143);

        // Municipio Correcto: Tres Cantos
        Log::info("Actualizando: 150 -  Tres Cantos");
        $this->updateProcess([246], 150);

        // Municipio Correcto: Valdeolmos-Alalpardo
        Log::info("Actualizando: 158 -  Valdeolmos-Alalpardo");
        $this->updateProcess([284], 158);

        // Municipio Correcto: Velilla de San Antonio
        Log::info("Actualizando: 163 -  Velilla de San Antonio");
        $this->updateProcess([313], 163);

        // Municipio Correcto: Villalbilla
        Log::info("Actualizando: 168 -  Villalbilla");
        $this->updateProcess([479], 168);

        // Municipio Correcto: Villanueva de la Cañada
        Log::info("Actualizando: 172 -  Villanueva de la Cañada");
        $this->updateProcess([344], 172);


        $this->noticePoster('Fin de actualización de los Municipios de Madrid (Provincia)');
    }


    private function updateProcess(array $idDuplicateMunicipalities, $idCorrectMunicipality)
    {
        $correctMunicipality = Municipality::find($idCorrectMunicipality);

        if(!$correctMunicipality)
        {
            Log::info("El Municipio con ID { $idCorrectMunicipality } no existe");
            return;
        }


        // 1 - Actualizar municipality_id de Distritos por el correcto

        District::whereIn('municipality_id', $idDuplicateMunicipalities)
                ->update(['municipality_id' => $correctMunicipality->id]);


        // 2 - Actualizar municipality_id de Negocios por el correcto

        Business::whereIn('municipality_id', $idDuplicateMunicipalities)
                        ->update(['municipality_id' => $correctMunicipality->id]);

        // 3 - Eliminar Municipio Duplicado

        Municipality::whereIn('id', $idDuplicateMunicipalities)
                        ->forceDelete();

    }

    private function noticePoster(String $text)
    {
        $today = Carbon::now()->format('d-m-Y H:i:s');

        Log::info('===================================================================');
        Log::info($text);
        Log::info('Fecha: ' . $today);
        Log::info('===================================================================');
    }

}
