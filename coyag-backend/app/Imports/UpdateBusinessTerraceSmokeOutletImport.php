<?php

namespace App\Imports;

use App\Models\Business;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\{Importable, ToModel, WithHeadingRow, WithValidation};

class UpdateBusinessTerraceSmokeOutletImport implements ToModel, WithHeadingRow, WithValidation
{
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
        if(empty($row['web']))
        {
            $this->arrReturn['unloaded_rows'] .= ($this->arrReturn['unloaded_rows'] == '')  ? $this->currentRow : ",{$this->currentRow}";
            $this->arrReturn['highest_row'] = $this->currentRow;
            Log::info('Sin campo web en la Fila ' .  $this->currentRow . ' del excel');
            $this->currentRow++;
            return null;
        }

        $business = Business::where('source_url', $row['web'])->first();

        if(empty($business)){
            Log::info('No existe Negocio con source_url (web): ' . $row['web'] . ' en la Fila ' .  $this->currentRow . ' del excel');
            $this->arrReturn['unloaded_rows'] .= ($this->arrReturn['unloaded_rows'] == '')  ? $this->currentRow : ",{$this->currentRow}";
            $this->arrReturn['highest_row'] = $this->currentRow;
            $this->currentRow++;
            return null;
        }


        $business->flag_smoke_outlet = empty($row['salida_humos'])  ? null
                                                                    : (in_array(mb_strtolower($row['salida_humos']), ['si', 'sí']) ? true : false);

        $business->flag_terrace = empty($row['terraza'])    ? null
                                                            : (in_array(mb_strtolower($row['terraza']), ['si', 'sí']) ? true : false);


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
