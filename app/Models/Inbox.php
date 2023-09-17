<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inbox extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $user = Auth::user();
            $model->user_id = $user->id;
        });
        // static::updating(function ($model) {
        //     $user = Auth::user();
        //     $model->updated_by = $user->id;
        // });
    }


    /**
     * Get the user that owns the Boite
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the departement that owns the Inbox
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function departement(): BelongsTo
    {
        return $this->belongsTo(Departement::class);
    }

    /**
     * Get all of the decrets for the Inbox
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function decrets(): HasMany
    {
        return $this->hasMany(Decret::class);
    }
    /**
     * Get all of the messages for the Inbox
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get all of the arretes for the Inbox
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function arretes(): HasMany
    {
        return $this->hasMany(Arrete::class);
    }
}
