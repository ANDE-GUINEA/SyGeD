<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Archive extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $casts = [
        'references' => 'array',
        'confidential' => 'array',
        'autres' => 'array',
    ];
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $user = Auth::user();
            $model->user_id = $user->id;
            $model->init = $user->departement->name;
        });

        // static::updating(function ($model) {
        //     $user = Auth::user();
        //     $model->init = $user->departement->name;
        // });

        // static::addGlobalScope(new DecretScope);
    }

    /**
     * Get the user that owns the Finance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the type that owns the Decret
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    /**
     * Get the decret that owns the Archive
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function decret(): BelongsTo
    {
        return $this->belongsTo(Decret::class);
    }

    /**
     * Get the arrete that owns the Archive
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function arrete(): BelongsTo
    {
        return $this->belongsTo(Arrete::class);
    }
}
