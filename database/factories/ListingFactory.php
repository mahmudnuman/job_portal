<?php

namespace Database\Factories;

use App\Models\Listing;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ListingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Listing::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->jobTitle,  // Random job title
            'company_name' => $this->faker->company,  // Random company name
            'slug' => Str::slug($this->faker->jobTitle . ' at ' . $this->faker->company),  // SEO-friendly slug
            'description' => $this->faker->paragraph,  // Random job description
            'meta_description' => $this->faker->sentence,  // Meta description
            'keywords' => implode(',', $this->faker->words(5)),  // Random job-related keywords
            'view_count' => $this->faker->numberBetween(0, 1000),  // Random view count
            'applications_count' => $this->faker->numberBetween(0, 100),  // Random applications count
            'expiry_date' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),  // Random expiry date within next month
        ];
    }
}
