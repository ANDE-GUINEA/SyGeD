<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Type extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     `decret_id`,
    //     `title`,
    //     `document`,
    //     `user_id`,

    // ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $user = Auth::user();
            $model->user_id = $user->id;
            // $model->init = $user->departement->name;
        });

        // static::updating(function ($model) {
        //     $user = Auth::user();
        //     $model->init = $user->departement->name;
        // });
    }

    /**
     * Get the user that owns the Dossier
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Get all of the decrets for the Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function decrets(): HasMany
    {
        return $this->hasMany(Decret::class);
    }

    /**
     * Get all of the arretes for the Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function arretes(): HasMany
    {
        return $this->hasMany(Arrete::class);
    }
}
