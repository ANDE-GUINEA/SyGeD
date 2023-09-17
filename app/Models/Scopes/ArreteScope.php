<?php

namespace App\Models\Scopes;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class ArreteScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::user()->worker) {
            # code...
            if (Auth::user()->worker->name == 'DEPARTEMENT') {
                $builder->where('init', Auth::user()->departement->name)->orderBy('updated_at', 'DESC');
            } else {
                $builder->whereNotNull('submit_at')->orderBy('updated_at', 'DESC');
            }
        }
    }
}
