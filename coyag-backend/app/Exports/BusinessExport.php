<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BusinessExport implements FromArray, WithHeadings
{
    private $businesses;

    public function __construct($businesses)
    {
        $this->businesses = $businesses;
    }

    /**
    * @return array
    */
    public function array(): array
    {
        return $this->businesses;
    }

    public function headings(): array
    {
        return [
            'Negocio',
            'Inversión',
            'Alquiler',
            'Contacto',
            'Teléfono',
            'Fuente',
            'Link',
            'Actualizado',
            
            'Provincia',
            'Municipio',
            'Distrito',
            'Barrio',

            'Habitaciones',
            'Baños',
            'Sector',
        ];
    }
}
