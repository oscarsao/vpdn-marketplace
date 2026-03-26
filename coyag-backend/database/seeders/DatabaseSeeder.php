<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Model::unguard();

            $this->call([
                PermissionsTableSeeder::class,
                RolesTableSeeder::class,
                ConnectRelationshipsSeeder::class,
                DepartmentsTableSeeder::class,
                ContinentsTableSeeder::class,
                CountriesTableSeeder::class,
                TitulationsTableSeeder::class,
                HeadquartersTableSeeder::class,
                UsersTableSeeder::class,
                ClientsTableSeeder::class,
                EmployeesTableSeeder::class,
                AutonomousCommunitiesTableSeeder::class,
                ProvincesTableSeeder::class,
                BusinessTypesTableSeeder::class,
                MunicipalitiesTableSeeder::class,
                DistrictsTableSeeder::class,
                SectorsTableSeeder::class,
                NotificationTypesTableSeeder::class,
                AssignedAdvisorsTableSeeder::class,
                VideoCallTypesTableSeeder::class,
                ServicesTableSeeder::class,
                AddedServicesTableSeeder::class,
                UserCommentTypesTableSeeder::class,
                VisaTypesTableSeeder::class,
                FamilyTypesTableSeeder::class,
                VisaDocumentTypesTableSeeder::class,
            ]);

        Model::reguard();
    }
}
