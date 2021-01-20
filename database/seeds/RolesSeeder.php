<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->truncate();
        DB::table('roles')->insert([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        DB::table('roles')->insert([
            'name' => 'sales-manager',
            'guard_name' => 'web',
        ]);
        DB::table('roles')->insert([
            'name' => 'non-shopify-users',
            'guard_name' => 'web',
        ]);
    }
}
