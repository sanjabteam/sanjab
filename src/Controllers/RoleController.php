<?php

namespace Sanjab\Controllers;

use App\User;
use Sanjab\Widgets\TextWidget;
use Sanjab\Helpers\CrudProperties;
use Sanjab\Widgets\PasswordWidget;
use Sanjab\Controllers\CrudController;
use Illuminate\Database\Eloquent\Model;
use Silber\Bouncer\Database\Role;
use Sanjab;
use Bouncer;
use Sanjab\Widgets\CheckboxGroupWidget;
use Illuminate\Http\Request;
use Sanjab\Helpers\PermissionItem;
use Sanjab\Widgets\CheckboxWidget;
use stdClass;

class RoleController extends CrudController
{
    protected static function properties(): CrudProperties
    {
        return CrudProperties::create('roles')
                ->title(trans('sanjab::sanjab.role'))
                ->titles(trans('sanjab::sanjab.roles'))
                ->model(Role::class)
                ->icon('person')
                ->defaultCards(false)
                ->defaultDashboardCards(false);
    }

    protected function init(string $type, Model $item = null): void
    {
        if ($item == null || $item->name != 'super_admin') {
            $this->widgets[] = TextWidget::create('name', trans('sanjab::sanjab.name'))
                            ->rules('required|string|regex:/[A-Za-z0-9_]+/|unique:bouncer_roles,name,'.optional($item)->id.',id');
        }
        $this->widgets[] = TextWidget::create('title', trans('sanjab::sanjab.title'))
                            ->rules('required|string');

        if ($item == null || $item->name != 'super_admin') {
            foreach (Sanjab::permissionItems() as $key => $permissionItem) {
                $checkboxGroup = CheckboxGroupWidget::create('permissions_'.$key, $permissionItem->groupName)
                    ->all(true)
                    ->customStore(function ($request, $data) {
                    })
                    ->customPostStore(function (Request $request, Model $data) use ($key, $permissionItem) {
                        foreach ($permissionItem->permissions() as $permission) {
                            Bouncer::disallow($data->name)->to($permission['name'], $permission['model']);
                        }
                        if (is_array($request->input('permissions_'.$key))) {
                            foreach ($permissionItem->permissions() as $permission) {
                                if (in_array($permission['name']."__".str_replace("\\", "___", $permission['model']), $request->input('permissions_'.$key))) {
                                    Bouncer::allow($data->name)->to($permission['name'], $permission['model']);
                                }
                            }
                        }
                    })
                    ->customModifyResponse(function (stdClass $response, Model $item) use ($key, $permissionItem) {
                        $currentPermissions = [];
                        foreach ($permissionItem->permissions() as $permission) {
                            if ($item && $item->can($permission['name'], $permission['model'])) {
                                $currentPermissions[] = $permission['name']."__".str_replace("\\", "___", $permission['model']);
                            }
                        }
                        $response->{ 'permissions_'.$key } = $currentPermissions;
                    });

                foreach ($permissionItem->permissions() as $permission) {
                    $checkboxGroup->addOption($permission['name']."__".str_replace("\\", "___", $permission['model']), $permission['title']);
                }
                $this->widgets[] = $checkboxGroup;
            }
        }
    }

    public static function permissions(): array
    {
        $permissions = parent::permissions();
        $permissions[] = PermissionItem::create(trans('sanjab::sanjab.dashboard'))
                        ->order(50)
                        ->addPermission(trans('sanjab::sanjab.access_to_admin_panel'), 'access_sanjab');
        return $permissions;
    }
}
