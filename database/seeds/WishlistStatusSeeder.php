<?php

use Illuminate\Database\Seeder;

class WishlistStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('wishlist_statuses')->insert([
            'name'=>'Open',
            'color' => 'Yellow',
        ]);
        DB::table('wishlist_statuses')->insert([
            'name'=>'Approved',
            'color' => 'Orange',
        ]);
        DB::table('wishlist_statuses')->insert([
            'name'=>'Accepted',
            'color' => 'Purple',
        ]);
        DB::table('wishlist_statuses')->insert([
            'name'=>'Rejected',
            'color' => 'Red',
        ]);
        DB::table('wishlist_statuses')->insert([
            'name'=>'Completed',
            'color' => 'Green',
        ]);
    }
}
