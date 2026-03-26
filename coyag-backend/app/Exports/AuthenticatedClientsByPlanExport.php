<?php

namespace App\Exports;

use App\Services\StatisticService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AuthenticatedClientsByPlanExport implements FromCollection, WithHeadings
{
    private $clients;

    public function __construct($clients)
    {
        $this->clients = $clients;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->clients;
    }

    public function headings(): array
    {
        return ['ID Cliente', 'Email', 'Nombres', 'Apellidos', 'Teléfono Celular', 'Nombre de Servicio', 'Número de Autenticaciones'];
    }
}
