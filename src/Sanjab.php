<?php

namespace Sanjab;

use Carbon\Carbon;
use Exception;
use EditorJS\EditorJS;
use Sanjab\Cards\Card;
use TusPhp\Cache\FileStore;
use TusPhp\Events\TusEvent;
use Sanjab\Helpers\MenuItem;
use EditorJS\EditorJSException;
use Sanjab\Helpers\SearchResult;
use Sanjab\Helpers\PermissionItem;
use Illuminate\Support\Facades\Log;
use TusPhp\Tus\Server as TusServer;
use Illuminate\Support\Facades\Auth;
use Sanjab\Helpers\NotificationItem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class Sanjab
{
    const SANJAB_CONTROLLERS = [
        \Sanjab\Controllers\NotificationController::class,
        \Sanjab\Controllers\AuthController::class,
        \Sanjab\Controllers\RoleController::class,
        \Sanjab\Controllers\QuillController::class,
        \Sanjab\Controllers\EditorJsController::class,
        \Sanjab\Controllers\UppyWidgetController::class,
        \Sanjab\Controllers\RelationWidgetController::class,
        \Sanjab\Controllers\CheckboxWidgetController::class,
        \Sanjab\Controllers\TranslationController::class,
        \Sanjab\Controllers\ElfinderController::class,
        \Sanjab\Controllers\SearchController::class,
        \Sanjab\Controllers\IconController::class,
        \Sanjab\Controllers\SelectiveCardController::class,
    ];

    /**
     * Menu items.
     *
     * @var MenuItem[]
     */
    protected static $menuItems = null;

    /**
     * Menu items.
     *
     * @var NotificationItem[]
     */
    protected static $notificationItems = null;

    /**
     * Permission items.
     *
     * @var PermissionItem[]
     */
    protected static $permissionItems = [];

    /**
     * User provided permission items.
     *
     * @var PermissionItem[]
     */
    protected static $customPermissionItems = [];

    /**
     * Dashboard cards.
     *
     * @var Card[]
     */
    protected static $dashboardCards = null;

    /**
     * Fontawesome icons.
     *
     * @var string[]
     */
    protected static $fontawesomeIcons = null;

    /**
     * Array of controllers.
     *
     * @return array
     */
    public static function controllers(): array
    {
        return array_filter(
            array_merge(config('sanjab.controllers'), static::SANJAB_CONTROLLERS),
            function ($controller) {
                if (class_exists($controller) && is_subclass_of($controller, \Sanjab\Controllers\SanjabController::class)) {
                    return true;
                }
                Log::error("'$controller' is not a valid sanjab controller.");

                return false;
            }
        );
    }

    /**
     * All controllers menu items.
     *
     * @return MenuItem[]
     * @throws Exception
     */
    public static function menuItems(): array
    {
        if (! Auth::check()) {
            return [];
        }
        if (static::$menuItems != null) {
            return static::$menuItems;
        }
        static::$menuItems = [];
        $index = 0;
        foreach (static::controllers() as $controller) {
            foreach ($controller::menus() as $menuItemKey => $menuItem) {
                if (! $menuItem instanceof MenuItem) {
                    throw new Exception("Some menu item in '$controller' is not a MenuItem type.");
                }
                $menuItem->key = $index++;
                if ($menuItem->hasChildren() == false || ! isset(static::$menuItems[$menuItem->title])) {
                    static::$menuItems[$menuItem->title] = $menuItem;
                } else {
                    foreach ($menuItem->getChildren() as $childItem) {
                        static::$menuItems[$menuItem->title]
                                ->addChild($childItem);
                    }
                }
            }
        }
        static::$menuItems = array_filter(static::$menuItems, function ($menuItem) {
            return ! $menuItem->isHidden();
        });
        usort(static::$menuItems, function ($a, $b) {
            if ($a->order == $b->order) {
                return $a->key > $b->key ? 1 : -1;
            }

            return $a->order > $b->order ? 1 : -1;
        });
        static::$menuItems = array_values(static::$menuItems);

        return static::$menuItems;
    }

    /**
     * All controllers menu items.
     *
     * @param bool $forceRefresh  force use lastest version instead of cached data.
     * @return NotificationItem[]
     * @throws Exception
     */
    public static function notificationItems($forceRefresh = false): array
    {
        if (! Auth::check()) {
            return [];
        }
        if (! (static::$notificationItems == null || $forceRefresh)) {
            return static::$notificationItems;
        }
        static::$notificationItems = [];
        foreach (static::controllers() as $controller) {
            foreach ($controller::notifications() as $notificationItem) {
                if (! $notificationItem instanceof NotificationItem) {
                    throw new Exception("Some permission item in '$controller' is not a NotificationItem type.");
                }
                static::$notificationItems[] = $notificationItem;
            }
        }
        static::$notificationItems = array_filter(static::$notificationItems, function ($notificationItem) {
            return ! $notificationItem->isHidden();
        });
        usort(static::$notificationItems, function ($a, $b) {
            return $a->order > $b->order;
        });

        return static::$notificationItems;
    }

    /**
     * Add custom permission to role permissions.
     *
     * @param PermissionItem $permissionItem
     * @return void
     */
    public static function addPermission(PermissionItem $permissionItem)
    {
        static::$customPermissionItems[] = $permissionItem;
    }

    /**
     * All controllers permission items.
     *
     * @return PermissionItem[]
     * @throws Exception
     */
    public static function permissionItems(): array
    {
        if (static::$permissionItems != null) {
            return static::$permissionItems;
        }
        static::$permissionItems = static::$customPermissionItems;
        foreach (static::controllers() as $controller) {
            foreach ($controller::permissions() as $permissionItem) {
                if (! $permissionItem instanceof PermissionItem) {
                    throw new Exception("Some permission item in '$controller' is not a PermissionItem type.");
                }
                if (! isset(static::$permissionItems[$permissionItem->groupName])) {
                    static::$permissionItems[$permissionItem->groupName] = $permissionItem;
                } else {
                    foreach ($permissionItem->permissions() as $permission) {
                        static::$permissionItems[$permissionItem->groupName]
                                ->addPermission($permission['title'], $permission['name'], $permission['model']);
                    }
                }
            }
        }
        static::$permissionItems = array_values(static::$permissionItems);
        usort(static::$permissionItems, function ($a, $b) {
            return $a->order > $b->order;
        });

        return static::$permissionItems;
    }

    /**
     * All controllers permission items.
     *
     * @return Card[]
     * @throws Exception
     */
    public static function dashboardCards(): array
    {
        if (static::$dashboardCards != null) {
            return static::$dashboardCards;
        }
        static::$dashboardCards = [];
        foreach (static::controllers() as $controller) {
            foreach ($controller::dashboardCards() as $dashboardCard) {
                if (! $dashboardCard instanceof Card) {
                    throw new Exception("Some dashboard card item in '$controller' is not a Card type.");
                }
                static::$dashboardCards[] = $dashboardCard;
            }
        }
        usort(static::$dashboardCards, function ($a, $b) {
            return $a->order > $b->order;
        });

        return static::$dashboardCards;
    }

    /**
     * Search globally in all controllers.
     *
     * @return SearchResult[]
     * @throws Exception
     */
    public static function search(string $search): array
    {
        $results = [];
        foreach (static::controllers() as $controller) {
            foreach ($controller::globalSearch($search) as $searchResult) {
                if (! $searchResult instanceof SearchResult) {
                    throw new Exception("Some search result in '$controller' is not a SearchResult type.");
                }
                $searchResult->setProperty('search', $search);
                $results[] = $searchResult;
                if (count($results) > 50) {
                    break 2;
                }
            }
        }
        usort($results, function ($a, $b) {
            return $a->order > $b->order;
        });

        return $results;
    }

    /**
     * Random image info for background.
     *
     * @return array|null
     */
    public static function image()
    {
        return Cache::remember('sanjab_background_details', now()->addHours(6), function () {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => config('sanjab.theme.images', 'https://sanjabteam.github.io/unsplash/images.json'),
                CURLOPT_TIMEOUT => 30,
                CURLOPT_RETURNTRANSFER => true,
            ]);
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return;
            }
            $response = json_decode($response, true);
            if (! is_array($response)) {
                return;
            }
            $out = array_random($response);
            if (is_array($out) && isset($out['image']) && isset($out['link']) && isset($out['author'])) {
                return $out;
            }
        });
    }

    /**
     * Clear unused uploads by uppy.
     *
     * @return void
     */
    public static function clearUploadCache()
    {
        foreach (Storage::disk('local')->files('temp') as $file) {
            if (! preg_match('/temp[\\/\\\\](.+)_tus_php.server.cache/', $file, $matches)) {
                continue;
            }
            // if last modified was more than 24 hours ago.
            if (time() > Storage::disk('local')->lastModified($file) + 86400) {
                Storage::disk('local')->delete($file);
                Storage::disk('local')->deleteDirectory('temp/'.$matches[1]);
            } else {
                $tusServer = static::createTusServer($matches[1]);
                foreach ($tusServer->getCache()->keys() as $cacheKey) {
                    $fileMeta = $tusServer->getCache()->get($cacheKey, true);
                    if (
                        (isset($fileMeta['expires_at']) && isset($fileMeta['file_path'])) &&
                        (
                            empty($fileMeta['expires_at']) ||
                            Carbon::parse($fileMeta['expires_at'])->lt(now('GMT')->subHours(24)) && $tusServer->getCache()->delete($cacheKey) && is_writable($fileMeta['file_path'])
                        )
                    ) {
                        unlink($fileMeta['file_path']);
                    }
                }
            }
        }
    }

    /**
     * Create TUS server based on session id.
     *
     * @param string $sessionId
     * @return TusServer
     */
    public static function createTusServer(string $sessionId)
    {
        if (! Storage::disk('local')->exists('temp/'.$sessionId)) {
            Storage::disk('local')->makeDirectory('temp/'.$sessionId);
        }

        $server = new TusServer(
            new FileStore(Storage::disk('local')->path('temp/'), $sessionId.'_tus_php.server.cache')
        );

        $server->event()->addListener('tus-server.upload.complete', function (TusEvent $event) {
            $uploadedFiles = Session::get('sanjab_uppy_files');
            $uploadedFiles[$event->getFile()->getKey()] = $event->getFile()->details();
            Session::put('sanjab_uppy_files', $uploadedFiles);
        });

        $server
            ->setApiPath('/admin/helpers/uppy/upload')
            ->setUploadDir(Storage::disk('local')->path('temp/'.$sessionId));

        return $server;
    }

    /**
     * Add a controller to controllers config.
     *
     * @param string $controller
     * @return void
     */
    public static function addControllerToConfig(string $controller)
    {
        if (! file_exists(config_path('sanjab.php'))) {
            throw new Exception('Sanjab config not found.');
        }
        if (! class_exists($controller)) {
            $controller = ltrim($controller, '\\');
            if (class_exists('App\Http\Controllers\Admin\Crud\\'.$controller)) {
                $controller = 'App\Http\Controllers\Admin\Crud\\'.$controller;
            } elseif (class_exists('App\Http\Controllers\Admin\Setting\\'.$controller)) {
                $controller = 'App\Http\Controllers\Admin\Setting\\'.$controller;
            } elseif (class_exists('App\Http\Controllers\Admin\\'.$controller)) {
                $controller = 'App\Http\Controllers\Admin\\'.$controller;
            } else {
                $controller = 'App\Http\Controllers\\'.$controller;
            }
        }
        if (! (class_exists($controller) && is_subclass_of($controller, \Sanjab\Controllers\SanjabController::class))) {
            return;
        }
        $regex = "/\\'controllers\\' => ((\[|array\s*\()[^\]\)]*\s*(\]|\)))/";
        $config = file_get_contents(config_path('sanjab.php'));
        preg_match_all($regex, $config, $controllerResult);
        $controllerResult = $controllerResult[1][0];
        $controllerResult = eval('return '.$controllerResult.';');
        $controllerResult[] = $controller;
        $controllerResult = array_unique($controllerResult);
        $controllerResult = array_map(function ($controller) {
            return $controller.'::class';
        }, $controllerResult);
        $controllerResult = "'controllers' => [\n        ".implode(",\n        ", $controllerResult).",\n    ]";
        file_put_contents(config_path('sanjab.php'), preg_replace($regex, $controllerResult, $config));
    }

    /**
     * Get font awesome icons as array.
     *
     * @return string[]
     */
    public static function fontawesomeIcons()
    {
        if (static::$fontawesomeIcons == null) {
            static::$fontawesomeIcons = json_decode(file_get_contents(sanjab_path('/resources/json/fontawesome.json')), true);
        }

        return static::$fontawesomeIcons;
    }

    /**
     * Convert editor.js data to html.
     *
     * @return string
     */
    public static function editorJsToHtml(array $data)
    {
        $out = '';
        try {
            $editor = new EditorJS(json_encode($data), file_get_contents(sanjab_path('resources/json/editorjs.json')));

            $blocks = $editor->getBlocks();
            foreach ($blocks as $block) {
                $out .= view('sanjab::helpers.editorjs.'.$block['type'], ['data' => $block['data']]);
            }
            $out = view('sanjab::helpers.editorjs.base', ['data' => $out])->render();
        } catch (EditorJSException $exception) {
            throw $exception;
        }

        return $out;
    }

    /**
     * Default user model class.
     *
     * @return string
     */
    public static function userModel()
    {
        return config('auth.providers.'.config('auth.guards.'.config('auth.defaults.guard').'.provider').'.model');
    }
}
