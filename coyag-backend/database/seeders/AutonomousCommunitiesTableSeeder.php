<?php

namespace Database\Seeders;

use App\Models\AutonomousCommunity;
use Illuminate\Database\Seeder;

class AutonomousCommunitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        AutonomousCommunity::create(['name' => 'Andalucía']);

        AutonomousCommunity::create(['name' => 'Aragón']);

        AutonomousCommunity::create(['name' => 'Principado de Asturias']);

        AutonomousCommunity::create(['name' => 'Islas Baleares']);

        AutonomousCommunity::create(['name' => 'Canarias']);

        AutonomousCommunity::create(['name' => 'Cantabria']);

        AutonomousCommunity::create(['name' => 'Castilla-La Mancha']);

        AutonomousCommunity::create(['name' => 'Castilla y León']);

        AutonomousCommunity::create(['name' => 'Cataluña']);

        AutonomousCommunity::create(['name' => 'Comunidad Valenciana']);

        AutonomousCommunity::create(['name' => 'Extremadura']);

        AutonomousCommunity::create(['name' => 'Galicia']);

        AutonomousCommunity::create(['name' => 'La Rioja']);

        AutonomousCommunity::create(['name' => 'Comunidad de Madrid']);

        AutonomousCommunity::create(['name' => 'Región de Murcia']);

        AutonomousCommunity::create(['name' => 'Comunidad Foral de Navarra']);

        AutonomousCommunity::create(['name' => 'País Vasco']);
    }
}
