<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Validation extends Model
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
     * Get the user that owns the Validation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the decret that owns the Validation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function decret(): BelongsTo
    {
        return $this->belongsTo(Decret::class);
    }
}
