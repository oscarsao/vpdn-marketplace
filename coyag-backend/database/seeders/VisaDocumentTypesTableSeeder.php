<?php

namespace Database\Seeders;

use App\Models\VisaDocumentType;
use Illuminate\Database\Seeder;

class VisaDocumentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VisaDocumentType::create([
            'name'                  =>  'Formulario de solicitud de visado nacional',
        ]);

        VisaDocumentType::create([
            'name'                  =>  'Pasaporte',
        ]);

        VisaDocumentType::create([
            'name'                  =>  'Impreso de solicitud de autorización de RNL Modelo EX-01',
        ]);

        VisaDocumentType::create([
            'name'                  =>  'IMPRESO Modelo 790 -> Justificativo de haber pagado todas las tasas',
        ]);

        VisaDocumentType::create([
            'name'                  =>  'Acreditación de recursos económicos',
        ]);

        VisaDocumentType::create([
            'name'                  =>  'Acta de Nacimiento',
        ]);

        VisaDocumentType::create([
            'name'                  =>  'Acta de Matrimonio',
        ]);

        VisaDocumentType::create([
            'name'                  =>  'Patria Potestad',
        ]);

        VisaDocumentType::create([
            'name'                  =>  'Autorización de los Padres',
        ]);

        VisaDocumentType::create([
            'name'                  =>  'Seguro Médico',
        ]);

        VisaDocumentType::create([
            'name'                  =>  'Certificado de Antecedentes Penales Apostillados',
        ]);

        VisaDocumentType::create([
            'name'                  =>  'Certificado Médico',
        ]);

        VisaDocumentType::create([
            'name'                  =>  'Constancia de Inscripción en la Universidad o Centro de Estudios',
        ]);

    }
}
