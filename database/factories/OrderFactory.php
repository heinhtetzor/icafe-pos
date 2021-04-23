<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Order;
use App\Waiter;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "table_id" => 'express',
            "waiter_id" => Waiter::all()->random()->id,
            "status" => 1,
            "created_at" => $this->faker->dateTimeBetween("-2 years", "now")
        ];
    }
}