<?php

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    // DB::table('admin_accounts')->insert(
        // [
	    //     'username' => 'admin',	        
	    //     'password' => Hash::make('12345'),
        //     'store_id' => null
	    // ]);
	    DB::table('admin_accounts')->insert(
        [
	        'username' => 'admin-tmg',
	        'password' => Hash::make('12345'),
            'store_id' => 1,
	    ]);
	    DB::table('admin_accounts')->insert(
        [
	        'username' => 'admin-kc',
	        'password' => Hash::make('12345'),
            'store_id' => 2,
	    ]);
	    DB::table('admin_accounts')->insert(
        [
	        'username' => 'admin-zyt',
	        'password' => Hash::make('12345'),
            'store_id' => 3,
	    ]);
	    DB::table('admin_accounts')->insert(
        [
	        'username' => 'admin-pk',
	        'password' => Hash::make('12345'),
            'store_id' => 4,
	    ]);
	    DB::table('admin_accounts')->insert(
        [
	        'username' => 'admin-ibeer',
	        'password' => Hash::make('12345'),
            'store_id' => 5,
	    ]);
	    DB::table('admin_accounts')->insert(
        [
	        'username' => 'admin-slk',	        
	        'password' => Hash::make('12345'),
            'store_id' => 6,
	    ]);
    }
}
