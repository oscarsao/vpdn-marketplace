<?php

namespace App\Exports;

use App\Services\StatisticService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClientsByPlanExport implements FromCollection, WithHeadings
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
        return [
            'ID Cliente',
            'Email',
            'Nombres',
            'Apellidos',
            'Teléfono Celular',
            'Nombre Plan',
            'Primera Nacionalidad',
            'Activo',
            'Método de Registro',
            'Inicio Plan',
            'Fin Plan',
            'Número de Autenticaciones',
            'Negocios Visitados',
            'Pregunta 1',
            'Pregunta 2',
            'Pregunta 3',
            'Pregunta 4',
            'Pregunta 5 Min Inv',
            'Pregunta 5 Max Inv'
        ];
    }
}
