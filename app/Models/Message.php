<?php

namespace App\Models;

use App\Models\Scopes\MessageScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
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

        static::addGlobalScope(new MessageScope);
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
     * Get the inbox that owns the Message
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inbox(): BelongsTo
    {
        return $this->belongsTo(Inbox::class);
    }

    /**
     * Get the decret that owns the Message
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function decret(): BelongsTo
    {
        return $this->belongsTo(Decret::class);
    }
}
