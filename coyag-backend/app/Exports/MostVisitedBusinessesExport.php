<?php

namespace App\Exports;

use App\Services\StatisticService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MostVisitedBusinessesExport implements FromCollection, WithHeadings
{
    private $businesses;

    public function __construct($businesses)
    {
        $this->businesses = $businesses;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->businesses;
    }

    public function headings(): array
    {
        return ['ID', 'ID CRM', 'Título', 'Traspaso', 'Alquiler', 'Área m2', 'Salida de Humo', 'Terraza', 'Nombre de Vendedor', 'Teléfono de Vendedor', 'Provincia', 'Municipio', 'Distrito', 'Barrio', 'Número de Visitas', 'Negocio Activo', 'Plataforma', 'URL', 'Fecha de Carga', 'Sectores'];
    }
}
