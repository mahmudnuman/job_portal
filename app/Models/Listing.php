<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'company_name', 'slug', 'description', 'meta_description', 'keywords', 
        'view_count', 'applications_count', 'expiry_date',
    ];

    /**
     * Get the applications for the listing.
     */
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Scope to get job listings that are not expired.
     */
    public function scopeActive($query)
    {
        return $query->where('expiry_date', '>', now());
    }
}
