<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Arrete;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArretePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_arrete');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Arrete  $arrete
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Arrete $arrete): bool
    {
        return $user->can('view_arrete');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user): bool
    {
        return $user->can('create_arrete');
    }
    /**
     * Determine whether the user can soumettre models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function soumettre(User $user, Arrete $arrete)
    {
        if (auth()->user()->departement) {
            if (
                auth()->user()->departement->name ==
                $arrete->init &&
                auth()->user()->departement->inbox->id ===
                $arrete->inbox->id
                && $arrete->okPRIMATURE == false
                && $arrete->Signé == false
                && $arrete->Submit == false
            ) {
                return $user->can('soumettre_arrete');
            }
        } else {
            return $user->can('soumettre_arrete');
        }
    }
    /**
     * Determine whether the user can valide models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function valide(User $user, Arrete $arrete)
    {
        if (auth()->user()->departement && auth()->user()->worker) {
            if (
                (auth()->user()->worker->name === "SGG" || auth()->user()->worker->name === "PRIMATURE") &&
                auth()->user()->departement->inbox->id ===
                $arrete->inbox->id
                && $arrete->Signé == false
                && $arrete->Submit == true
            ) {
                return $user->can('valide_arrete');
            }
        } else {
            return $user->can('valide_arrete');
        }
    }
    /**
     * Determine whether the user can publish models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function  pulbish(User $user, Arrete $arrete)
    {
        if (auth()->user()->departement && auth()->user()->worker) {
            if (
                (auth()->user()->worker->name === "SGG") &&
                auth()->user()->departement->inbox->id ===
                $arrete->inbox->id
                && $arrete->Signé == true
                && $arrete->Publié == false
                && $arrete->Submit == true
            ) {
                return $user->can('pulbish_arrete');
            }
        } else {
            return $user->can('pulbish_arrete');
        }
    }
    /**
     * Determine whether the user can retourne models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function retourne(User $user, Arrete $arrete)
    {
        if (auth()->user()->departement && auth()->user()->worker) {
            if (
                (auth()->user()->worker->name === "SGG" || auth()->user()->worker->name === "PRIMATURE") &&
                auth()->user()->departement->inbox->id ===
                $arrete->inbox->id && $arrete->Signé == false
                && $arrete->Submit == true
            ) {
                return $user->can('retourne_arrete');
            }
        } else {
            return $user->can('retourne_arrete');
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Arrete  $arrete
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Arrete $arrete)
    {
        if (auth()->user()->departement) {
            if (
                auth()->user()->departement->name ===
                $arrete->init &&
                auth()->user()->departement->inbox->id ===
                $arrete->inbox->id && $arrete->okPRIMATURE == false
                && $arrete->okSGG == false
                && $arrete->Submit == false
            ) {
                return $user->can('update_arrete');
            }
        } else {
            return $user->can('update_decret');
        }
    }
    /**
     * Determine whether the user can signe the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Arrete  $arrete
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function signe(User $user, Arrete $arrete)
    {
        if (auth()->user()->departement) {
            if (
                auth()->user()->departement->name ===
                $arrete->init &&
                auth()->user()->departement->inbox->id ===
                $arrete->inbox->id && $arrete->okPRIMATURE == true
                && $arrete->Signé == false
                && $arrete->Submit == true
            ) {
                return $user->can('signe_arrete');
            }
        } else {
            return $user->can('signe_decret');
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Arrete  $arrete
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Arrete $arrete): bool
    {
        return $user->can('delete_arrete');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_arrete');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Arrete  $arrete
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Arrete $arrete): bool
    {
        return $user->can('force_delete_arrete');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_arrete');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Arrete  $arrete
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Arrete $arrete): bool
    {
        return $user->can('restore_arrete');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_arrete');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Arrete  $arrete
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function replicate(User $user, Arrete $arrete): bool
    {
        return $user->can('replicate_arrete');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_arrete');
    }
}
