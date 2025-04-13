<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ApplicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Application::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'listing_id' => Listing::factory(),  // Create a random listing as the foreign key
            'user_id' => User::factory(),  // Create a random user as the foreign key
            'email' => $this->faker->unique()->safeEmail,  // Random unique email for the application
            'applied_at' => $this->faker->dateTimeBetween('-1 month', 'now'),  // Random application date within the last month
        ];
    }
}
