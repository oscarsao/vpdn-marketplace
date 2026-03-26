<?php

namespace Database\Seeders;

use App\Models\UserCommentType;
use Illuminate\Database\Seeder;

class UserCommentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserCommentType::create(['name' => 'Extranjería']);
        UserCommentType::create(['name' => 'Marketing']);
        UserCommentType::create(['name' => 'Negocios']);
        UserCommentType::create(['name' => 'VideoPortal']);
    }
}
