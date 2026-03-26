<?php

namespace App\Imports;

use App\Models\Business;
use App\Models\Sector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\{Importable, ToModel, WithHeadingRow, WithValidation};

class UpdateBusinessSectorImport implements ToModel, WithHeadingRow, WithValidation
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
        Log::info('--------------------------');

        if((empty($row['id_crm']) && empty($row['web'])) || empty($row['sector']))
        {
            $this->writeError('Faltan campos');
            return null;
        }

        $business;

        if(!empty($row['web']))
            $business = Business::where('source_url', $row['web'])->first();
        else
            $business = Business::where('id_code', $row['id_crm'])->first();

        if(empty($business)){
            $this->writeError('No existe negocio');
            return null;
        }

        $arrSectors = explode(',', str_replace(' ', '', $row['sector']));

        if(count($arrSectors) == 0)
        {
            $this->writeError('Sin Sector');
            return null;
        }

        $business->sector()->detach();

        foreach ($arrSectors as $auxSector) {
            $pos = strpos($auxSector, '-');
            $numberSector = $pos !== false ? trim(substr($auxSector, 0, $pos))
                            : null;
            $sector = Sector::find($numberSector);

            if(empty($sector))
                continue;
            else
                $business->sector()->attach($sector->id);
        }


        Log::info("Actualizado Sector del Negocio - ID: {$business->id} - Nombre: {$business->name}");

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

    private function writeError(string $message) : void
    {
        Log::info("$message - Fila: {$this->currentRow} del excel");

        $this->arrReturn['unloaded_rows'] .= ($this->arrReturn['unloaded_rows'] == '')  ? $this->currentRow : ",{$this->currentRow}";
        $this->arrReturn['highest_row'] = $this->currentRow;
        $this->currentRow++;

        Log::info('--------------------------');
    }
}
