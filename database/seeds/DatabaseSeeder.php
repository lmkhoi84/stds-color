<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(StructureTableSeeder::class);
        $this->call(Structure_Translations_Seeder::class);
        $this->call(LanguagesTableSeeder::class);
    }
}
