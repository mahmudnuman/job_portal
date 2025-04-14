<?php 
namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ListingController extends Controller
{
    public function index()
    {
        return Listing::select('id', 'title', 'slug', 'company_name', 'view_count', 'applications_count')->get();
    }

    public function show($id, Request $request)
    {
        $listing = Listing::findOrFail($id);
    
        $key = 'viewed_listing_' . $id . '_' . ($request->ip() ?? 'guest');
    
        // Check if viewed within last 24 hours
        if (!Cache::has($key)) {
            $listing->increment('view_count');
            Cache::put($key, true, now()->addHours(24)); // Keep track for 24 hours
        }
    
        return response()->json($listing);
    }
}
