<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StructureTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //1
        DB::table('structure')->insert([
            'structure_url' => 'home',
            'parent_id' => 0,
            'page_type' => 1,
            'sort' => 1,
            'level' => 1,
            'status' => 1,
            'icon' => 'mdi-home',
            'created_user' => 1
        ]);
        //2
        DB::table('structure')->insert([
            'structure_url' => 'structure',
            'parent_id' => 0,
            'page_type' => 1,
            'sort' => 2,
            'level' => 1,
            'status' => 1,
            'icon' => 'mdi-file-tree',
            'created_user' => 1
        ]);
        //3
        DB::table('structure')->insert([
            'structure_url' => 'multi-languages',
            'parent_id' => 0,
            'page_type' => 1,
            'sort' => 3,
            'level' => 1,
            'status' => 1,
            'icon' => 'mdi-earth',
            'created_user' => 1
        ]);
        //4
        DB::table('structure')->insert([
            'structure_url' => 'categories',
            'parent_id' => 0,
            'page_type' => 1,
            'sort' => 4,
            'level' => 1,
            'status' => 1,
            'icon' => 'mdi-folder',
            'created_user' => 1
        ]);
        //5
        DB::table('structure')->insert([
            'structure_url' => 'products',
            'parent_id' => 0,
            'page_type' => 1,
            'sort' => 5,
            'level' => 1,
            'status' => 1,
            'icon' => 'mdi-file',
            'created_user' => 1
        ]);
        //6
        DB::table('structure')->insert([
            'structure_url' => 'users',
            'parent_id' => 0,
            'page_type' => 1,
            'sort' => 98,
            'level' => 1,
            'status' => 1,
            'icon' => 'mdi-account-multiple',
            'created_user' => 1
        ]);
        //7
        DB::table('structure')->insert([
            'structure_url' => 'account',
            'parent_id' => 0,
            'page_type' => 1,
            'sort' => 99,
            'level' => 1,
            'status' => 1,
            'icon' => 'mdi-account',
            'created_user' => 1
        ]);
        //8
        DB::table('structure')->insert([
            'structure_url' => 'change-language',
            'parent_id' => 0,
            'page_type' => 3,
            'sort' => 901,
            'level' => 1,
            'status' => 1,
            'icon' => 'mdi-translate',
            'created_user' => 1
        ]);
        //9
        DB::table('structure')->insert([
            'structure_url' => 'login',
            'parent_id' => 0,
            'page_type' => 3,
            'sort' => 902,
            'level' => 1,
            'status' => 1,
            'icon' => 'mdi-account-check',
            'created_user' => 1
        ]);
        //10
        DB::table('structure')->insert([
            'structure_url' => 'register',
            'parent_id' => 0,
            'page_type' => 3,
            'sort' => 903,
            'level' => 1,
            'status' => 1,
            'icon' => 'mdi-account-plus',
            'created_user' => 1
        ]);
        //11
        DB::table('structure')->insert([
            'structure_url' => 'forgot-password',
            'parent_id' => 0,
            'page_type' => 3,
            'sort' => 904,
            'level' => 1,
            'status' => 1,
            'icon' => 'mdi-account-key',
            'created_user' => 1
        ]);
        //12
        DB::table('structure')->insert([
            'structure_url' => 'add',
            'parent_id' => 2,
            'page_type' => 3,
            'sort' => 2,
            'level' => 2,
            'status' => 1,
            'icon' => 'mdi-file',
            'created_user' => 1
        ]);
        //13
        DB::table('structure')->insert([
            'structure_url' => 'edit',
            'parent_id' => 2,
            'page_type' => 3,
            'sort' => 3,
            'level' => 2,
            'status' => 1,
            'icon' => '',
            'created_user' => 1
        ]);
        //14
        DB::table('structure')->insert([
            'structure_url' => 'delete',
            'parent_id' => 2,
            'page_type' => 3,
            'sort' => 4,
            'level' => 2,
            'status' => 1,
            'icon' => '',
            'created_user' => 1
        ]);
        //15
        DB::table('structure')->insert([
            'structure_url' => 'languages',
            'parent_id' => 3,
            'page_type' => 1,
            'sort' => 1,
            'level' => 2,
            'status' => 1,
            'icon' => 'mdi-earth',
            'created_user' => 1
        ]);
        //16
        DB::table('structure')->insert([
            'structure_url' => 'translations',
            'parent_id' => 3,
            'page_type' => 1,
            'sort' => 2,
            'level' => 2,
            'status' => 1,
            'icon' => 'mdi-translate',
            'created_user' => 1
        ]);
        //17
        DB::table('structure')->insert([
            'structure_url' => 'edit',
            'parent_id' => 16,
            'page_type' => 3,
            'sort' => 1,
            'level' => 3,
            'status' => 1,
            'icon' => '',
            'created_user' => 1
        ]);
        //18
        DB::table('structure')->insert([
            'structure_url' => 'add',
            'parent_id' => 15,
            'page_type' => 3,
            'sort' => 1,
            'level' => 3,
            'status' => 1,
            'icon' => 'mdi-file',
            'created_user' => 1
        ]);
        //19
        DB::table('structure')->insert([
            'structure_url' => 'edit',
            'parent_id' => 15,
            'page_type' => 3,
            'sort' => 2,
            'level' => 3,
            'status' => 1,
            'icon' => '',
            'created_user' => 1
        ]);
        //20
        DB::table('structure')->insert([
            'structure_url' => 'delete',
            'parent_id' => 15,
            'page_type' => 3,
            'sort' => 3,
            'level' => 3,
            'status' => 1,
            'icon' => '',
            'created_user' => 1
        ]);

        //21
        DB::table('structure')->insert([
            'structure_url' => 'users-group',
            'parent_id' => 6,
            'page_type' => 1,
            'sort' => 1,
            'level' => 2,
            'status' => 1,
            'icon' => 'mdi-folder-account',
            'created_user' => 1
        ]);

        //22
        DB::table('structure')->insert([
            'structure_url' => 'users-list',
            'parent_id' => 6,
            'page_type' => 1,
            'sort' => 2,
            'level' => 2,
            'status' => 1,
            'icon' => 'mdi-account-multiple-plus',
            'created_user' => 1
        ]);

        //23
        DB::table('structure')->insert([
            'structure_url' => 'add',
            'parent_id' => 21,
            'page_type' => 3,
            'sort' => 1,
            'level' => 3,
            'status' => 1,
            'icon' => '',
            'created_user' => 1
        ]);

        //24
        DB::table('structure')->insert([
            'structure_url' => 'edit',
            'parent_id' => 21,
            'page_type' => 3,
            'sort' => 2,
            'level' => 3,
            'status' => 1,
            'icon' => '',
            'created_user' => 1
        ]);
        //25
        DB::table('structure')->insert([
            'structure_url' => 'delete',
            'parent_id' => 21,
            'page_type' => 3,
            'sort' => 3,
            'level' => 3,
            'status' => 1,
            'icon' => '',
            'created_user' => 1
        ]);
        //26
        DB::table('structure')->insert([
            'structure_url' => 'add',
            'parent_id' => 22,
            'page_type' => 3,
            'sort' => 1,
            'level' => 3,
            'status' => 1,
            'icon' => '',
            'created_user' => 1
        ]);
        //27
        DB::table('structure')->insert([
            'structure_url' => 'edit',
            'parent_id' => 22,
            'page_type' => 3,
            'sort' => 2,
            'level' => 3,
            'status' => 1,
            'icon' => '',
            'created_user' => 1
        ]);
        //28
        DB::table('structure')->insert([
            'structure_url' => 'delete',
            'parent_id' => 22,
            'page_type' => 3,
            'sort' => 3,
            'level' => 3,
            'status' => 1,
            'icon' => '',
            'created_user' => 1
        ]);
        //29
        DB::table('structure')->insert([
            'structure_url' => 'en',
            'parent_id' => 8,
            'page_type' => 3,
            'sort' => 1,
            'level' => 3,
            'status' => 1,
            'icon' => '',
            'created_user' => 1
        ]);
        //30
        DB::table('structure')->insert([
            'structure_url' => 'vi',
            'parent_id' => 8,
            'page_type' => 2,
            'sort' => 3,
            'level' => 3,
            'status' => 1,
            'icon' => '',
            'created_user' => 1
        ]);
        //31
        DB::table('structure')->insert([
            'structure_url' => 'change-status',
            'parent_id' => 21,
            'page_type' => 3,
            'sort' => 4,
            'level' => 3,
            'status' => 1,
            'icon' => '',
            'created_user' => 1
        ]);
        //32
        DB::table('structure')->insert([
            'structure_url' => 'change-status',
            'parent_id' => 22,
            'page_type' => 3,
            'sort' => 4,
            'level' => 3,
            'status' => 1,
            'icon' => '',
            'created_user' => 1
        ]);
        //33
        DB::table('structure')->insert([
            'structure_url' => 'change-status',
            'parent_id' => 15,
            'page_type' => 3,
            'sort' => 4,
            'level' => 3,
            'status' => 1,
            'icon' => '',
            'created_user' => 1
        ]);
    }
}
