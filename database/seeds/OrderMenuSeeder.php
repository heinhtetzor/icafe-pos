<?php

use App\Menu;
use App\Order;
use App\Waiter;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class OrderMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        foreach (range(1, 500000) as $i) {
            $date = $faker->dateTimeBetween('-2 years', 'now');                        

            $price_arr = [
                100, 150, 200 ,300, 400, 500, 600, 800, 1000, 1200, 2000
            ];

            DB::table('order_menus')->insert([
                "order_id" => rand(24, 149445),
                "price" => $price_arr[array_rand($price_arr)],
                "menu_id" => rand(1, 71),
                "waiter_id" => rand(1, 2),
                "quantity" => rand(1,10),
                "status" => 1,
                "is_foc" => 0,
                "created_at" => $date, 
                "updated_at" => $date
            ]);
        }
    }
}
