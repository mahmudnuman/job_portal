<?php namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ApplicationController extends Controller
{
    public function apply(Request $request, $listingId)
    {
        $user = auth('api')->user();
        $listing = Listing::findOrFail($listingId);

        // Prevent multiple applications by same user
        if (Application::where('user_id', $user->id)->where('listing_id', $listingId)->exists()) {
            return response()->json(['error' => 'Already applied'], 400);
        }

        $application = Application::create([
            'user_id'    => $user->id,
            'listing_id' => $listingId,
        ]);

        $listing->increment('applications_count');

        // Send email (configure mail properly in .env)
        Mail::raw("New applicant: {$user->name} ({$user->email})", function ($message) use ($listing) {
            $message->to('company@example.com')->subject("New Application for {$listing->title}");
        });

        return response()->json(['message' => 'Application submitted'], 201);
    }
}
