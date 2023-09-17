<?php

namespace App\Models;

use App\Models\Scopes\DecretScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Decret extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['options'];
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

        static::addGlobalScope(new DecretScope);
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
     * Get the inbox that owns the Decret
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inbox(): BelongsTo
    {
        return $this->belongsTo(Inbox::class);
    }

    /**
     * Get all of the validations for the Decret
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function validations(): HasMany
    {
        return $this->hasMany(Validation::class);
    }

    /**
     * Get all of the dossiers for the Decret
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dossiers(): HasMany
    {
        return $this->hasMany(Dossier::class);
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
     * Get all of the messages for the Worker
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get all of the archives for the Decret
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function archives(): HasMany
    {
        return $this->hasMany(Archive::class);
    }

    /**
     * Get all of the pubiliers for the Decret
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function publiers(): HasMany
    {
        return $this->hasMany(Publie::class);
    }
}
