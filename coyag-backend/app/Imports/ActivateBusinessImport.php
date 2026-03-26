<?php

namespace App\Imports;

use App\Models\Business;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\{Importable, ToModel, WithHeadingRow, WithValidation};


class ActivateBusinessImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * Activa los negocios cuyo ID CRM (id_code) se encuentre en el archivo
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
        if(empty($row['id_crm']))
        {
            $this->arrReturn['unloaded_rows'] .= ($this->arrReturn['unloaded_rows'] == '')  ? $this->currentRow : ",{$this->currentRow}";
            $this->arrReturn['highest_row'] = $this->currentRow;
            Log::info('Campos vacíos en la Fila ' .  $this->currentRow . ' del excel');
            $this->currentRow++;
            return null;
        }

        $business = Business::where('id_code', $row['id_crm'])->first();

        if(empty($business)){
            Log::info('No existe Negocio con ID CRM: ' . $row['id_crm']);
            $this->arrReturn['unloaded_rows'] .= ($this->arrReturn['unloaded_rows'] == '')  ? $this->currentRow : ",{$this->currentRow}";
            $this->arrReturn['highest_row'] = $this->currentRow;
            $this->currentRow++;
            return null;
        }

        $business->flag_active = true;
        $business->save();

        $this->arrReturn['loaded_rows'] .= ($this->arrReturn['loaded_rows'] == '')  ? $this->currentRow : ",{$this->currentRow}";
        $this->arrReturn['highest_row'] = $this->currentRow;
        $this->arrReturn['update_business_id_crm'] .= ($this->arrReturn['update_business_id_crm'] == '')  ? $business->id_code : ",{$business->id_code}";
        $this->currentRow++;

        return $business;

    }

    public function rules() : array
    {
        return [
            //'name'    => Rule::in(['max:64']),
            //'price'     => Rule::in(['numeric']),
        ];
    }
}
