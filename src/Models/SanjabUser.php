<?php

namespace Sanjab\Models;

use Silber\Bouncer\Database\Models;
use Illuminate\Database\Eloquent\Builder;
use Silber\Bouncer\Database\HasRolesAndAbilities;

trait SanjabUser
{
    use HasRolesAndAbilities;

    public function scopeWhereCanModel(Builder $query, $ability, string $model = null)
    {
        $query->where(function ($query) use ($ability, $model) {
            // direct
            $query->whereHas('abilities', function ($query) use ($ability, $model) {
                $query->byName($ability);
                if ($model) {
                    $query->whereIn(Models::table('abilities').'.entity_type', ['*', $model])->whereNull(Models::table('abilities').'.entity_id');
                }
            });
            // through roles
            $query->orWhereHas('roles', function ($query) use ($ability, $model) {
                $query->whereHas('abilities', function ($query) use ($ability, $model) {
                    $query->byName($ability);
                    if ($model) {
                        $query->whereIn(Models::table('abilities').'.entity_type', ['*', $model])->whereNull(Models::table('abilities').'.entity_id');
                    }
                });
            });
        });
    }
}
