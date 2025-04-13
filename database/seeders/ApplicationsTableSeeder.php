<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Application;
use App\Models\Listing;
use App\Models\User;

class ApplicationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all listings
        $listings = Listing::all();
        
        // Get all users
        $users = User::all();

        // Generate applications for each user and each listing
        foreach ($listings as $listing) {
            foreach ($users as $user) {
                // Ensure each user only applies once for each listing
                Application::factory()->create([
                    'listing_id' => $listing->id,
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
