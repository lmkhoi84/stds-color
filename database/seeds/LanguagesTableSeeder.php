<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('languages')->insert([
            'name' => 'en',
            'status' => 1,
        ]);

        DB::table('languages')->insert([
            'name' => 'vi',
            'status' => 1,
        ]);

        DB::table('languages_translations')->insert([
            'languages_id' => 1,
            'languages_name' => 'Tiếng Anh',
            'locale' => 'vi',
        ]);

        DB::table('languages_translations')->insert([
            'languages_id' => 1,
            'languages_name' => 'English',
            'locale' => 'en',
        ]);

        DB::table('languages_translations')->insert([
            'languages_id' => 2,
            'languages_name' => 'Tiếng Việt',
            'locale' => 'vi',
        ]);

        DB::table('languages_translations')->insert([
            'languages_id' => 2,
            'languages_name' => 'Vietnamese',
            'locale' => 'en',
        ]);
    }
}
