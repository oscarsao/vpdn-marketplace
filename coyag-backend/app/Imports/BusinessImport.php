<?php

/**
 * Este Import contempla cargas completas, cargas parciales y actualización de negocio
 *
 * Si el negocio NO existe en la BD, procederá a crearlo
 * 
 * SI ES TRASPASO 
 * SI ES OBRA NUEVA
 * SI ES SOLO ALQUILER
 * SI ES SOLO VENTA
 */

namespace App\Imports;

use App\Models\Business;
use App\Models\District;
use App\Models\Municipality;
use App\Models\Neighborhood;
use App\Models\Sector;
use App\Traits\CorrectNameTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\{Importable, WithEvents, ToModel, WithHeadingRow, PersistRelations, WithUpserts, WithUpsertColumns, RegistersEventListeners};

use Illuminate\Support\Facades\DB;

class BusinessImport implements ToModel, WithHeadingRow, PersistRelations, WithUpserts, WithUpsertColumns, WithEvents {
    use Importable, CorrectNameTrait, RegistersEventListeners;

    private $row;
    private $run_name;
    private $arrReturn;
    private $updateImage;   
    private $updateField;
    private $provinces;
    private $municipalities;
    private $districts;
    private $neighborhoods;

    public function __construct(array &$arrReturn, array $partialImportConfiguration, array $auxiliarGeographicArrays, $run_name = "") {
        $this->row = 2;
        $this->run_name = $run_name;
        $this->arrReturn = &$arrReturn;
        $this->updateImage   = $partialImportConfiguration["update_image"];
        $this->updateField   = $partialImportConfiguration["update_field"];
        $this->provinces      =  $auxiliarGeographicArrays['provinces'];
        $this->neighborhoods  =  $auxiliarGeographicArrays['neighborhoods'];
        $this->districts      =  $auxiliarGeographicArrays['districts'];
        $this->municipalities =  $auxiliarGeographicArrays['municipalities'];
    }

    public static function afterImport(AfterImport $event) {
        $businessesWithoutSectors = Business::whereNotNull('random_string')->get();

        foreach ($businessesWithoutSectors as $business) {
            Log::info(' - Business Found: ' . $business->id . ' - ' . $business->name . ' - ' . $business->random_string);
            if (!empty($business->random_string)) {
                $sectors = explode(';', $business->random_string);
                foreach ($sectors as $sector) {
                    DB::insert('INSERT INTO business_sector (business_id, sector_id) VALUES (?, ?)', [$business->id, $sector]);
                }
                $business->random_string = null;
                $business->save();
            }
        }
    }


    public function uniqueBy() {
        return 'id_business_platform';
    }
    public function upsertColumns() {
        return ['name', 'amount_requested_by_seller', 'investment', 'rental', 'contact_name', 'contact_landline', 'size', 'facade_size', 'description', 'lat', 'lng', 'link_map', 'source_timestamp', 'garage', 'storage', 'pool', 'rooms', 'bathrooms', 'floors', 'floor', 'year_built', 'courtyard', 'elevator', 'new_or_used', 'price_per_sqm', 'flag_smoke_outlet', 'flag_terrace', 'business_images_string', 'business_videos_string', 'flag_active', 'municipality_id', 'district_id', 'neighborhood_id', 'employee_id', 'business_type_id', 'id_business_platform', 'source_platform', 'source_url', 'source_timestamp'];
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row) {
        // Descarto los que no tengan una URL, Imagenes, o provincia o municipio. Eso si toma en cuenta filas que no debe
        if (empty($row['web']) || empty($row['images_ppal']) || empty($row['provincia']) || $row['provincia'] === '' || empty($row['municipio']) || $row['municipio'] === '' ) {
            Log::info('# SKIP - lacks any of web, images_ppal, provincia, municipio');
            $this->arrReturn['unloaded_rows'] .= ($this->arrReturn['unloaded_rows'] == '')  ? $this->row : ",{$this->row}";
            $this->arrReturn['total_rows']  = $this->row - 1;
            $this->arrReturn['highest_row'] = $this->row;
            $this->row++;
            return null;
        }

        Log::info('# ' . $this->run_name . ' - ' . ($this->row - 1). ': ' . $row['web']);
        
        /*
            Los valores de Provincia, Municipio, Distrito y Barrio están unificados
        */

        $provinceName     = $this->provinceCorrectName($row['provincia']);
        $municipalityName = $this->municipalityCorrectName($row['municipio']);
        $districtName     = $this->districtCorrectName($row['distrito']);
        $neighborhoodName = $this->neighborhoodCorrectName($row['barrio']);
        
        $provinceID     = $this->findIdByName($this->provinces, $provinceName);
        $municipalityID = $this->findIdByName($this->municipalities, $municipalityName);
        $districtID     = $this->findIdByName($this->districts, $districtName);
        $neighborhoodID = $this->findIdByName($this->neighborhoods, $neighborhoodName);

        $province = (object) array ('id' => $provinceID, 'name' => $provinceName );
        
        Log::info(' - Prvcia: ' . $provinceID . ' = "' . $provinceName.'" (' . $row['provincia'] . ')' . " | id => $province->id 'name' => $province->name"  );

        if (empty($provinceID)) {
            Log::info(" - SI NO HAY PROVINCIA ESTO FALLARA - SKIP  | id => $province->id 'name' => $province->name");
            return null;
        } 
               
        if (!empty($provinceID) && empty($municipalityID) && !empty($municipalityName)) {
            $municipality = new Municipality();
            $municipality->name = ucwords(strtolower($municipalityName));
            $municipality->province_id = $provinceID;
            $municipality->save();
            $municipalityID = $municipality->id;
            Log::info(' + Mncpio: ' . $municipalityID . ' = ' . $municipalityName);
        } else if (!empty($municipalityID)) {
            $municipality = (object) array ('id' => $municipalityID, 'name' => $municipalityName );
            Log::info(' - Mncpio: ' . $municipalityID . ' = ' . $municipalityName);
        } else {
            $municipality = null;
            Log::info(" - SI NO HAY MUNICIPIO ESTO FALLARA - SKIP");
            return null;
        }

        if (!empty($municipalityID) && empty($districtID) && !empty($districtName) ) {
            $district = new District();
            $district->name = ucwords(strtolower($districtName));
            $district->municipality_id = $municipalityID;
            $district->save();
            $districtID = $district->id;
            Log::info(' + Dstrto: ' . $districtID . ' = ' . $districtName);
        } else if (!empty($districtID)) {
            Log::info(' - Dstrto: ' . $districtID . ' = ' . $districtName);
            $district = (object) array ('id' => $districtID, 'name' => $districtName );
        } else {
            $district = null;
        }

        if (!empty($districtID) &&  empty($neighborhoodID) && !empty($neighborhoodName) ) {
            $neighborhood = new Neighborhood();
            $neighborhood->name = ucwords(strtolower($municipalityName));
            $neighborhood->district_id = $districtID;
            $neighborhood->save();
            $neighborhoodID = $neighborhood->id;
            Log::info(' + Barrio: ' . $neighborhoodID . ' = ' . $neighborhoodName);
        } else if (!empty($neighborhoodID)) {
            $neighborhood = (object) array ('id' => $neighborhoodID, 'name' => $neighborhoodName );
            Log::info(' - Barrio: ' . $neighborhoodID . ' = ' . $neighborhoodName);
        } else {
            $neighborhood = null;
        }
        /*
            Los valores de Provincia, Municipio, Distrito y Barrio están unificados
        */




        /*
            Los valores de Plataforma
        */
        $website = $row['web'];
        $sourcePlatform = '';
        $idBusinessPlatform = '';
        if (strpos($website, "idealista") !== false) {
            $sourcePlatform = 'Idealista';
            if (substr($website, -1) == '/') $website =  substr($website, 0, strlen($website) -1);
            $arrwebsite = explode('/', $website);
            $idIdealista = end($arrwebsite);
            if (is_numeric($idIdealista)) $idBusinessPlatform = $idIdealista;
        } else if (strpos($website, "milanuncios") !== false) {
            $sourcePlatform = 'Milanuncios';
            $auxArr = explode('-', $website);
            $idMilanuncios = end($auxArr);
            $idMilanuncios = str_replace('.htm', '', $idMilanuncios);
            if (is_numeric($idMilanuncios)) $idBusinessPlatform = $idMilanuncios;
        } else if (strpos($website, "belbex") !== false) {
            $sourcePlatform = 'Belbex';
            if (substr($website, -1) == '/') $website =  substr($website, 0, strlen($website) -1);
            $auxArr = explode('/', $website);
            $idBelbex = $auxArr[count($auxArr) - 2];
            if (strpos($idBelbex, "-") === false) $idBusinessPlatform = $idBelbex;
        } else if (strpos($website, "fotocasa") !== false) {
            $sourcePlatform = 'Fotocasa';
            $auxArr = explode('/', $website);
            $idFotocasa = $auxArr[count($auxArr) - 2];
            if (is_numeric($idFotocasa)) $idBusinessPlatform = $idFotocasa;
        } else if (strpos($website, "mundofranquicia") !== false) {
            $sourcePlatform = 'mundoFranquicia';
        }
        /*
            Los valores de Plataforma
        */

        $facadeSize = !empty($row['fachada']) ? (is_numeric($row['fachada']) ? $row['fachada'] . ' m lineales' : $row['fachada'] ) : null;

        $size = trim(str_replace(['m2', 'm²'], '', $row['area_m2']));
        if (!is_numeric($size) || empty($row['area_m2'])) $size = null;

        $precioTraspaso = null;
        if (!empty($row["traspaso"])) {
            if ( in_array(strtolower($row["traspaso"]), ['precio a consultar', 'a consultar', 'venta']) ) {
                $precioTraspaso = null;
            } else {
                $precioTraspaso = str_replace('€', '', str_replace('.', '', $row["traspaso"]));
                $precioTraspaso = str_replace("'", '', str_replace('.', '', $precioTraspaso));
                if (!is_numeric($precioTraspaso)) $precioTraspaso = null;
            }
        }

        $precioAlquiler = null;
        if (!empty($row["alquiler"])) {
            if (strtolower(in_array(strtolower($row["alquiler"]), ['precio a consultar', 'a consultar', 'venta']))) {
                $precioAlquiler = null;
            } else {
                $precioAlquiler = str_replace('€', '', str_replace('.', '', $row["alquiler"]));
                $precioAlquiler = str_replace("'", '', str_replace('.', '', $precioAlquiler));
                $precioAlquiler = str_replace([' más Iva', ' más iva', ' + iva', '+IVA'], '', $precioAlquiler);
                $precioAlquiler = preg_replace('/[^0-9]/', '', $precioAlquiler);
                if (!is_numeric($precioAlquiler)) $precioAlquiler = null;
            }
        }

        // Evitando obtener un Serial Number al leer del excel
        if (!empty($row['fechapublicacion'])) {
            $sourceTimestamp = is_int($row['fechapublicacion']) ? Carbon::createFromTimestamp(($row['fechapublicacion'] - 25569) * 86400)->format('Y-m-d') : $row['fechapublicacion'];
        } else {
            $sourceTimestamp = null;
        }

        if (empty($row['titulo']) || (strpos($row['titulo'], "€") !== false) || (strpos($row['titulo'], "euros") !== false)) {
            if (!empty($municipality)) $title = 'Excelente oportunidad en ' . $municipality->name;
        } else {
            $title = $row['titulo'];
        }
        
        // Revisa directamente por id_business_platform
        Log::info(' - Searching Business by ID Business Platform: ' . $sourcePlatform . ' - ' . $idBusinessPlatform);

        $business = null;

        if (!empty($idBusinessPlatform) && !empty($sourcePlatform)) {
            $business = Business::where('id_business_platform', $idBusinessPlatform)->where('source_platform', $sourcePlatform)->first();
        }

        if (empty($business)) {
            $business = new Business();
            $business->id_code = Business::max('id_code') + 1; // ID (público) // FFS
            Log::info(' - Business not found - Creating a new One');
        } else {
            Log::info(' - Business Found: ' . $business->id . ' - ' . $business->name . ' - ' . $business->idBusinessPlatform);
        }

        $business->province_id                = $province->id;
        $business->municipality_id            = $municipality->id;
        $business->district_id                = empty($district)     ? null : $district->id;
        $business->neighborhood_id            = empty($neighborhood) ? null : $neighborhood->id;
        $business->business_type_id           = empty($row['property_type']) ? 1 : $row['property_type'];
        $business->employee_id                = Auth::user()->employee->id;
        $business->name                       = strlen($title) > 255 ? mb_substr($title, 0, 252) . '...' : $title;
        $business->amount_requested_by_seller = $precioTraspaso;
        $business->investment                 = $precioTraspaso;
        $business->rental                     = $precioAlquiler;
        $business->contact_name               = empty($row['nom_vende']) ? null : $row['nom_vende'];
        $business->contact_landline           = empty($row['tel_vende']) ? null : $row['tel_vende'];
        $business->size                       = $size;
        $business->facade_size                = $facadeSize;
        $business->description                = empty($row['descrip']) ? null : $row['descrip'];
        $business->lat                        = empty($row['latitud']) ? null : $row['latitud'];
        $business->lng                        = empty($row['longitud']) ? null : $row['longitud'];
        $business->link_map                   = empty($row['latitud']) || empty($row['longitud']) ? null : "https://www.google.com/maps/place/{$row['latitud']},{$row['longitud']}";
        $business->id_business_platform       = $idBusinessPlatform;
        $business->source_platform            = $sourcePlatform;
        $business->source_url                 = $website;
        $business->source_timestamp           = $sourceTimestamp;

        $business->fecha_de_entrega           = empty($row['fecha_de_entrega']) ? null : $row['fecha_de_entrega'];

        $business->garage       = empty($row['garage'])     ? null : $row['garage'];
        $business->storage      = empty($row['storage'])    ? null : $row['storage'];
        $business->pool         = empty($row['pool'])       ? null : $row['pool'];
        $business->rooms        = empty($row['rooms'])      ? null : $row['rooms'];
        $business->bathrooms    = empty($row['bathrooms'])  ? null : $row['bathrooms'];
        $business->floors       = empty($row['floors'])     ? null : $row['floors'];
        $business->floor        = empty($row['floor'])      ? null : $row['floor'];
        $business->year_built   = empty($row['year_built']) ? null : $row['year_built'];
        $business->courtyard    = empty($row['courtyard'])  ? null : $row['courtyard'];
        $business->elevator     = empty($row['elevator']) || $row['elevator'] === 'Sin ascensor' ? null : $row['elevator'];
        $business->new_or_used  = empty($row['new_or_used'])? null : $row['new_or_used'];

        $business->job_id       = empty($row['job_id'])      ? null : $row['job_id'];
        $business->collector_id = empty($row['collector_id'])? null : $row['collector_id'];

        if ((empty($business->rental ) || empty($business->size )) || $business->rental == -1 || $business->rental == null) {
            $business->price_per_sqm = null;
        } else {
            $business->price_per_sqm =  round($business->rental / $business->size, 2);
        }

        $business->flag_smoke_outlet = empty($row['salida_humos']) ? null : (in_array(mb_strtolower($row['salida_humos']), ['si', 'sí']) ? true : false);
        $business->flag_terrace = empty($row['terraza']) ? null : (in_array(mb_strtolower($row['terraza']), ['si', 'sí']) ? true : false);

        $business_images_string = [];
        $imagesPpal = str_replace([" ","'",'"'], '', $row['images_ppal']);
        $arrImages = explode(';', $imagesPpal);
        foreach($arrImages as $urlImage) {
            if ($sourcePlatform == 'Milanuncios') {
                $pos = strpos($urlImage, 'rule=');
                if ( $pos === false) $urlImage .= '?rule=detail_640x480';
            }
            $business_images_string[] = $urlImage;
        }
        $business_images_string = implode(';', $business_images_string);
        $business_images_string = ltrim($business_images_string, ';');

        $business->business_images_string = $business_images_string;

        $business_videos_string = !empty($row['videos']) ? str_replace([" ","'",'"'], '', $row['videos']) : null;

        $business->business_videos_string = $business_videos_string;

        $business->flag_active = true;

        $auxSectors = [];        
        $arrSectors = explode(',', str_replace(' ', '', $row['sector']));
        foreach ($arrSectors as $auxSector) {
            $pos = strpos($auxSector, '-');
            $numberSector = $pos !== false ? trim(substr($auxSector, 0, $pos)) : null;
            if (!empty($numberSector) && is_numeric($numberSector)) $auxSectors[] = $numberSector;       
            /*  
                Not available yet in 3.1 gg
                $sector = Sector::find($numberSector);
                if (!empty($sector)) {
                    DB::insert('INSERT INTO business_sector (business_id, sector_id) VALUES (?, ?)', [$business->id, $sector->id]);
                    $business->sector()->attach($sector->id);
                    $business->setRelation('sector', ["sector_id" => $sector->id] );
                    $business->setRelation('sector', ['business_id' => $business->id,"sector_id" => $sector->id] );
                }
            */
        }
        $business->random_string = implode(';', $auxSectors);
        $this->arrReturn['loaded_rows'] .= ($this->arrReturn['loaded_rows'] == '') ? $this->row : ",{$this->row}";
        $this->arrReturn['charged_business'] .= ($this->arrReturn['charged_business'] == '') ? "{$business->id}" : ",{$business->id}";
        $this->arrReturn['total_rows']  = $this->row - 1;
        $this->arrReturn['highest_row'] = $this->row;
        $this->row++;

        $whereis = config('app.debug') ? "http://localhost:8081/videoportal/#" : "https://videoportaldenegocios.es/videoportal/#";
        Log::info(" - Saving $business->id - $whereis/negocio/$business->id_code - $whereis/admin/editar-negocio/$business->id");
        return $business;
    }
    private function findIdByName($objects, $name) {
      foreach ($objects as $object) {
        if (strtolower($object->name) === strtolower($name)) return $object->id;
      }
      return null;
    }
}
