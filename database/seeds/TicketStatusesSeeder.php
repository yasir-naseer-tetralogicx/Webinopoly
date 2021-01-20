<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('ticket_statuses')->insert([
            'status'=>'New',
            'color' => 'Yellow',
        ]);
        DB::table('ticket_statuses')->insert([
            'status'=>'Waiting Client Feedback',
            'color' => 'Orange',
        ]);
        DB::table('ticket_statuses')->insert([
            'status'=>'Waiting Support Feedback',
            'color' => 'Purple',
        ]);
        DB::table('ticket_statuses')->insert([
            'status'=>'Closed',
            'color' => 'Black',
        ]);
        DB::table('ticket_statuses')->insert([
            'status'=>'Completed',
            'color' => 'Green',
        ]);

    }
}
