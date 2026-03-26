<?php

namespace App\Imports;

use App\Models\Business;
use App\Models\District;
use App\Models\Municipality;
use App\Models\Sector;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\{Importable, ToModel, WithHeadingRow, WithValidation};

class UpdateBusinessImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    private $row;
    private $arrReturn;

    public function __construct(array &$arrReturn)
    {
        $this->arrReturn = &$arrReturn;
        $this->row = 2;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $business = Business::where('id', $row['id'])
                    ->where('id_code', $row['id_crm'])
                    ->first();

        if(empty($business)){
            $this->arrReturn['unloaded_rows'] .= ($this->arrReturn['unloaded_rows'] == '')  ? $this->row : ",{$this->row}";
            $this->arrReturn['total_rows'] = $this->row - 1;
            $this->arrReturn['highest_row'] = $this->row;
            $this->row++;
            return null;
        }

        $business->sector()->detach();

        $arrSectors = explode(';', str_replace(' ', '', $row['sector']));

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

        $this->arrReturn['loaded_rows'] .= ($this->arrReturn['loaded_rows'] == '')  ? $this->row
                                                                                : ",{$this->row}";
        $this->arrReturn['total_rows'] = $this->row - 1;
        $this->arrReturn['highest_row'] = $this->row;
        $this->row++;

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
