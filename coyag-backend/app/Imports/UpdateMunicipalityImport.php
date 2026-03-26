<?php

namespace App\Imports;

use App\Models\Municipality;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\{Importable, ToModel, WithHeadingRow, WithValidation};

class UpdateMunicipalityImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * Solo actualiza rent_per_capita y demographic_data
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
        Log::info('--------------------------');

        if(empty($row['municipio']))
        {
            $this->arrReturn['unloaded_rows'] .= ($this->arrReturn['unloaded_rows'] == '')  ? $this->currentRow : ",{$this->currentRow}";
            $this->arrReturn['highest_row'] = $this->currentRow;
            Log::info('Sin campo municipio en la Fila ' .  $this->currentRow . ' del excel');
            $this->currentRow++;
            Log::info('--------------------------');
            return null;
        }

        $municipalityName = null;
        $pos = strpos($row['municipio'], '(');

        if($pos !== false)
        {
            $name = $row['municipio'];
            $pos2 = strpos($name, ')');
            $pre = substr($name, $pos + 1, $pos2 - $pos - 1);
            $municipalityName = $pre . ' ' . trim(substr($name, 0, $pos - 1));
        }
        else
            $municipalityName = trim($row['municipio']);

        $municipality = Municipality::whereRaw('LOWER(name) = ?', strtolower($municipalityName))
                                        ->first();

        if(empty($municipality))
        {
            $this->arrReturn['unloaded_rows'] .= ($this->arrReturn['unloaded_rows'] == '')  ? $this->currentRow : ",{$this->currentRow}";
            $this->arrReturn['highest_row'] = $this->currentRow;
            Log::info("Municipio NO existe: $municipalityName");
            $this->currentRow++;
            Log::info('--------------------------');
            return null;
        }

        Log::info("Municipio existe: $municipalityName");
        $this->arrReturn['update_municipality_id'] .= ($this->arrReturn['update_municipality_id'] == '')  ? "{$municipality->id}"
                                                                                                        : ",{$municipality->id}";

        $municipality->rent_per_capita = number_format($row['renta'], 2, ',', '.') . ' €';
        $municipality->demographic_data = 'Población: ' . number_format($row['poblacion'], 0, ',', '.');

        $municipality->save();

        Log::info('--------------------------');


        $this->arrReturn['loaded_rows'] .= ($this->arrReturn['loaded_rows'] == '')  ? $this->currentRow : ",{$this->currentRow}";
        $this->arrReturn['highest_row'] = $this->currentRow;
        $this->currentRow++;

        return $municipality;


    }

    public function rules() : array
    {
        return [
            //'name'    => Rule::in(['max:64']),
            //'price'     => Rule::in(['numeric']),
        ];
    }

}
