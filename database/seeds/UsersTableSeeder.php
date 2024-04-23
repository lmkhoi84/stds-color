<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'full_name' => 'Administrator',
            'email' => 'admin@stdsvn.com',
            'password' => bcrypt('@ Hoi lam chi'),
            'group' => 1,
            'status' => 1,
            'avatar' => 'lmkhoi.jpg'
        ]);
        DB::table('users')->insert([
            'full_name' => 'Tester',
            'email' => 'tester@stdsvn.com',
            'password' => bcrypt('11 22 33'),
            'group' => 2,
            'status' => 1,
            'avatar' => ''
        ]);
    }
}
