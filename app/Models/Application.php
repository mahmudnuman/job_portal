<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id', 'user_id', 'email', 'applied_at',
    ];

    /**
     * Get the listing that the application is for.
     */
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * Get the user who applied for the listing.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ensure that a user can apply only once for a job.
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($application) {
            $existingApplication = Application::where('user_id', $application->user_id)
                                               ->where('listing_id', $application->listing_id)
                                               ->exists();

            if ($existingApplication) {
                throw new \Exception('User has already applied for this job.');
            }
        });
    }
}
