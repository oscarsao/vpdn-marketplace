<?php

namespace App\Imports;

use App\Models\Business;
use App\Models\BusinessMultimedia;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\{Importable, ToModel, WithHeadingRow, WithValidation};

class ActivateBusinessURLImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * Activa los negocios cuyo URL (source_url) se encuentre en el archivo
     */

     use Importable;

     private $currentRow;
     private $arrReturn;

     public function __construct(array &$arrReturn)
     {
         $this->arrReturn = &$arrReturn;
         $this->currentRow = 2;
     }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if(empty($row['url'])) {
            $this->arrReturn['unloaded_rows'] .= ($this->arrReturn['unloaded_rows'] == '')  ? $this->currentRow : ",{$this->currentRow}";
            $this->arrReturn['highest_row'] = $this->currentRow;
            $this->currentRow++;
            return null;
        }

        $ID = false;
        $BDID = false;
        $auxString = $row['url'];

        if(strpos($auxString, "idealista") !== false) {
            if(substr($auxString, -1) == '/') $auxString =  substr($auxString, 0, strlen($auxString) -1);
            $arrAuxString = explode('/', $auxString);
            $idIdealista = end($arrAuxString);
            if(is_numeric($idIdealista)) {
                $ID = $idIdealista;
                $BDID = '#ia#' . $idIdealista;
            }
        } else if(strpos($auxString, "milanuncios") !== false) {
            $auxArr = explode('-', $auxString);
            $idMilanuncios = end($auxArr);
            $idMilanuncios = str_replace('.htm', '', $idMilanuncios);
            if(is_numeric($idMilanuncios)) {
                $ID = $idMilanuncios;
                $BDID = '#ma#' . $idMilanuncios;
            }
        } else if(strpos($auxString, "fotocasa") !== false) {
            $auxArr = explode('/', $auxString);
            $idFotocasa = $auxArr[count($auxArr) - 2];
            if(is_numeric($idFotocasa)) {
                $ID = $idFotocasa;
                $BDID = '#fc#' . $idFotocasa;
            }
        } else if(strpos($auxString, "belbex") !== false) {
            if(substr($auxString, -1) == '/') $auxString =  substr($auxString, 0, strlen($auxString) -1);
            $auxArr = explode('/', $auxString);
            $idBelbex = $auxArr[count($auxArr) - 2];
            if(strpos($idBelbex, "-") === false) {
                $ID = $idBelbex;
                $BDID = '#bx#' . $idBelbex;
            }
        } else {
            $this->arrReturn['unloaded_rows'] .= ($this->arrReturn['unloaded_rows'] == '')  ? $this->currentRow : ",{$this->currentRow}";
            $this->arrReturn['highest_row'] = $this->currentRow;
            $this->currentRow++;
            return null;
        }

        // Log::info('- Fila ' .  $this->currentRow . ': ' . $ID);
        $this->arrReturn['loaded_rows'] .= ($this->arrReturn['loaded_rows'] == '')  ? $this->currentRow : ",{$this->currentRow}";
        $this->arrReturn['highest_row'] = $this->currentRow;
        $this->arrReturn['update_business_id']   .= ($this->arrReturn['update_business_id']   == '')  ? $ID   : ",{$ID}";
        $this->arrReturn['update_business_bdid'] .= ($this->arrReturn['update_business_bdid'] == '')  ? $BDID : ",{$BDID}";
        $this->currentRow++;
    }

    public function rules() : array {
        return [ ];
    }
}
