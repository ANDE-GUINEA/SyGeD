<?php

namespace App\Models\Scopes;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class MessageScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // dd(Auth::user()->departement->inbox->id);
        if (Auth::user()->departement) {
            # code...
            $builder->where('inbox_id', Auth::user()->departement->inbox->id);
        }
    }
}
