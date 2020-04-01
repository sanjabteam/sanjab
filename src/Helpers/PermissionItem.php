<?php

namespace Sanjab\Helpers;

/**
 * @method $this groupName (string $value)  name of permission group
 * @method $this order (int $value)         order in checkgroups
 */
class PermissionItem extends PropertiesHolder
{
    protected $properties = [
        'order' => 100,
    ];
    protected $getters = [
        'permissions',
    ];
    protected $permissions = [];

    /**
     * create new Menu item.
     *
     * @return static
     */
    public static function create($groupName = null)
    {
        $out = new static;
        if ($groupName) {
            $out->groupName($groupName);
        }

        return $out;
    }

    /**
     * Add permission to group.
     *
     * @param string $title  title of permission
     * @param string $name  name of permission
     * @param string|null $model  model of permission
     * @return $this
     */
    public function addPermission($title, $name, $model = null)
    {
        $this->permissions[] = compact('title', 'name', 'model');

        return $this;
    }

    /**
     * Ppermissions.
     *
     * @return array
     */
    public function permissions()
    {
        return $this->permissions;
    }
}
