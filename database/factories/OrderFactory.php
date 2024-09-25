<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(), // Creates a user for the order
            'product_id' => Product::factory(), // Creates a product for the order
            'quantity' => $this->faker->numberBetween(1, 5), // Random quantity
            'status' => 'pending', // Default status
            'delivery_status' => 'pending', // Default status
        ];
    }
}
