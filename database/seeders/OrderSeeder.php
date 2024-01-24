<?php

namespace Database\Seeders;

use App\Order;
use App\Waiter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        foreach (range(1, 200000) as $i) {
            $date = $faker->dateTimeBetween('-2 years', 'now');
            DB::table('orders')->insert([
                "table_id" => "express",
                "waiter_id" => Waiter::all()->random()->id,
                "status" => 1,
                "invoice_no" => $faker->word(),
                "created_at" => $date, 
                "updated_at" => $date
            ]);
        }
    }
}
