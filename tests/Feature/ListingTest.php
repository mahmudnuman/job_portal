<?php 
namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        // Use pagination with 10 listings per page
        $perPage = 9;

        $listings = Listing::paginate($perPage);

        // Return paginated listings with links and meta data
        return response()->json($listings);
    }

    public function show($id, Request $request)
    {
        $listing = Listing::findOrFail($id);

        // Check if viewed within last 24 hours using cache to prevent multiple view increments
        $key = 'viewed_listing_' . $id . '_' . ($request->ip() ?? 'guest');

        if (!Cache::has($key)) {
            $listing->increment('view_count');
            Cache::put($key, true, now()->addHours(24)); // Keep track for 24 hours
        }

        return response()->json($listing);
    }
}
