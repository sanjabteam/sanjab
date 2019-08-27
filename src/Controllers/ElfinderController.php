<?php

namespace Sanjab\Controllers;

use elFinder;
use elFinderConnector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Sanjab\Helpers\MenuItem;
use Sanjab\Helpers\PermissionItem;

class ElfinderController extends SanjabController
{
    /**
     * Show elfinder file manager.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        abort_unless(config('sanjab.elfinder.enabled') || $request->input('popup') == 'true', 404);
        if ($request->input('popup') != 'true') {
            $this->authorize('access_to_file_manager');
        }
        return view('sanjab::file_manager', ['popup' => $request->input('popup') == 'true']);
    }

    /**
     * Elfinder connector.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function connector(Request $request)
    {
        $allowedDisks = [];
        if ($request->filled('disks')) {
            $allowedDisks = $request->input('disks');
            if (is_array($allowedDisks) == false) {
                $allowedDisks = [$allowedDisks];
            }
        }
        $roots = [];
        foreach (config('sanjab.elfinder.disks') as $disk => $alias) {
            $filesystem = Storage::disk($disk);
            if (count($allowedDisks) == 0 || in_array($disk, $allowedDisks)) {
                if ($filesystem->getDriver()->getAdapter() instanceof \League\Flysystem\Adapter\Local) {
                    $roots[] = [
                        'driver'        => 'LocalFileSystem',
                        'path'          => $filesystem->getDriver()->getAdapter()->getPathPrefix(),
                        'URL'           => $filesystem->getDriver()->getConfig()->get('url'),
                        'accessControl' => 'access',
                        'alias'         => $alias,
                        'attributes'    => [
                            [
                                'pattern' => '/^(.*\/)?\.(.+)/',
                                'hidden' => true,
                            ],
                        ],
                    ];
                } else {
                    $information = [
                        'driver'        => 'Flysystem',
                        'filesystem'    => $filesystem->getDriver(),
                        'accessControl' => 'access',
                        'alias'         => $alias,
                        'tmbPath'    => 'thumbnails',
                        'attributes'    => [
                            [
                                'pattern' => '/^(.*\/)?\.(.+)/',
                                'hidden' => true,
                            ],
                        ],
                    ];
                    if (! empty($filesystem->getDriver()->getConfig()->get('url'))) {
                        $information['URL'] = $filesystem->getDriver()->getConfig()->get('url');
                    }
                    $roots[] = $information;
                }
            }
        }
        $conncetor = new elFinderConnector(new elFinder([
            'roots' => $roots,
        ]));
        $conncetor->run();
    }

    public static function routes(): void
    {
        Route::get('/filemanager', static::class.'@show')->name('file_manager');
        Route::any('/filemanager/connector', static::class.'@connector')->name('file_manager.connector');
    }

    public static function menus(): array
    {
        return [
            MenuItem::create(route('sanjab.file_manager'))
                        ->title(trans('sanjab::sanjab.file_manager'))
                        ->icon('folder')
                        ->hidden(function () {
                            return !config('sanjab.elfinder.enabled') || request()->user()->cannot('access_to_file_manager');
                        })
                        ->order(150)
        ];
    }

    public static function permissions(): array
    {
        return [
            PermissionItem::create(trans('sanjab::sanjab.file_manager'))
                            ->addPermission(trans('sanjab::sanjab.access_to_file_manager'), 'access_to_file_manager')
        ];
    }
}
