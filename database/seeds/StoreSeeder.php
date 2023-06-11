<?php

use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('stores')->insert(
        [
            'id' => 1,
	        'name' => 'icafe-tmg',	        
	        'status' => 'A',
	    ]);
        DB::table('stores')->insert(
        [
            'id' => 2,
	        'name' => 'icafe-kc',	        
	        'status' => 'A',
	    ]);
        DB::table('stores')->insert(
        [
            'id' => 3,
	        'name' => 'icafe-zyt',
	        'status' => 'A',
	    ]);
        DB::table('stores')->insert(
        [
            'id' => 4,
	        'name' => 'icafe-pk',
	        'status' => 'A',
	    ]);
        DB::table('stores')->insert(
        [
            'id' => 5,
	        'name' => 'ibeer',	        
	        'status' => 'A',
	    ]);
        DB::table('stores')->insert(
        [
            'id' => 6,
	        'name' => 'slk',
	        'status' => 'A',
	    ]);
    }
}
