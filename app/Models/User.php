<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Scopes\DecretScoop;
use App\Models\Scopes\DecretScope;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens,
        TwoFactorAuthenticatable,
        HasFactory,
        Notifiable,
        HasRoles; //or HasFilamentShield

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    /**
     * Get the departement that owns the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function departement(): BelongsTo
    {
        return $this->belongsTo(Departement::class);
    }

    /**
     * Get the worker that owns the worker
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    /**
     * Get all of the decrets for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function decrets(): HasMany
    {
        return $this->hasMany(Decret::class);
    }

    /**
     * Get all of the validations for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function validations(): HasMany
    {
        return $this->hasMany(Validation::class);
    }

    /**
     * Get all of the types for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function types(): HasMany
    {
        return $this->hasMany(Type::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // && $this->hasVerifiedEmail()
        return str_ends_with($this->email, '@gouvernement.gov.gn');
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
}
