<?php

namespace App\Policies;

use App\Models\PuntoEmision;
use App\Models\User;

class PuntoEmisionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->esAdministrador();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PuntoEmision $puntoEmision): bool
    {
        return $user->esAdministrador();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->esAdministrador();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PuntoEmision $puntoEmision): bool
    {
        return $user->esAdministrador();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PuntoEmision $puntoEmision): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PuntoEmision $puntoEmision): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PuntoEmision $puntoEmision): bool
    {
        return false;
    }
}
