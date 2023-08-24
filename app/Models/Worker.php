<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Worker extends Model
{
    use HasFactory, SoftDeletes;

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
     * Get the user associated with the Worker
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}