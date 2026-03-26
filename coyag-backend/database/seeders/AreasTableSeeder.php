<?php
namespace Database\Seeders;
use App\Models\Area;
use Illuminate\Database\Seeder;
class AreasTableSeeder extends Seeder {
    public function run() {
        $areas = [
            [
                'name' => 'Cohen y Aguirre',
                'image' => '/logos/logo-cya.png',
                'description' => '',
            ],
            [
                'name' => 'Videoportal de Negocios',
                'image' => '/logos/logo-vpdn.png',
                'description' => '',
            ],
            [
                'name' => 'Mi Abuelo era Español',
                'image' => '/logos/logo-maee.png',
                'description' => '',
            ],
            [
                'name' => 'España te Homologa',
                'image' => '/logos/logo-eath.png',
                'description' => '',
            ],
            [
                'name' => 'Tu Visa por Fondos',
                'image' => '/logos/logo-tvpf.png',
                'description' => '',
            ],
        ];
        foreach ($areas as $area) {
            Area::create($area);
        }
    }
}