<?php

use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tables')->insert(
        [
            'id' => 99999,
            'name' => 'Express'
        ]);
    }
}
