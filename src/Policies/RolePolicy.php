<?php

namespace Sanjab\Policies;

use Illuminate\Foundation\Auth\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Silber\Bouncer\Database\Role;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Role $model
     * @return mixed
     */
    public function delete(User $user, Role $model)
    {
        return $model->name != 'super_admin';
    }
}
