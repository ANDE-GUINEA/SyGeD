<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departement extends Model
{
    use HasFactory, HasSlug, SoftDeletes;

    protected $guarded = [];

    // public static function boot()
    // {
    //     parent::boot();
    //     static::creating(function ($model) {
    //         $user = Auth::user();
    //         $model->user_id = $user->id;
    //     });
    //     // static::updating(function ($model) {
    //     //     $user = Auth::user();
    //     //     $model->updated_by = $user->id;
    //     // });
    // }
    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * Get all of the users for the Departement
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the inbox associated with the Departement
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function inbox(): HasOne
    {
        return $this->hasOne(Inbox::class);
    }
}
