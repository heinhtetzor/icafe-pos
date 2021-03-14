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
	    DB::table('admin_accounts')->insert([
	        'username' => 'admin',	        
	        'password' => Hash::make('12345'),
	    ]);
    }
}
