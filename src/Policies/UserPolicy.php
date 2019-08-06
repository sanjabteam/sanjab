<?php

namespace Sanjab\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @param  \Illuminate\Foundation\Auth\User $user
     * @param  \Illuminate\Foundation\Auth\User   $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        return $model->isNotA('super_admin') || $user->id == $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \Illuminate\Foundation\Auth\User $user
     * @param  \Illuminate\Foundation\Auth\User   $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        return $model->isNotA('super_admin') && $user->id != $model->id;
    }
}
