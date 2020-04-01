<?php

namespace Sanjab\Policies;

use Silber\Bouncer\Database\Role;
use Illuminate\Foundation\Auth\User;
use Illuminate\Auth\Access\HandlesAuthorization;

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
