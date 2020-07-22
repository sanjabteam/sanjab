<?php

namespace Sanjab\Controllers;

use stdClass;
use Carbon\Carbon;
use Exception;
use Sanjab\Sanjab;
use Sanjab\Helpers\Action;
use Illuminate\Http\Request;
use Sanjab\Widgets\ShowWidget;
use Sanjab\Helpers\CrudProperties;
use Sanjab\Widgets\CheckboxWidget;
use Illuminate\Support\Facades\Auth;
use Sanjab\Helpers\NotificationItem;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends CrudController
{
    protected static function properties(): CrudProperties
    {
        return CrudProperties::create('notifications')
                ->title(trans('sanjab::sanjab.notification'))
                ->titles(trans('sanjab::sanjab.notifications'))
                ->model(DatabaseNotification::class)
                ->icon('notifications')
                ->defaultCards(false)
                ->defaultDashboardCards(false)
                ->creatable(false)
                ->editable(false)
                ->deletable(false)
                ->searchable(false)
                ->showable(false)
                ->itemFormat('%title')
                ->defaultOrder('read_at')
                ->defaultOrderDirection('asc')
                ->autoRefresh(10)
                ->globalSearch(false);
    }

    protected function init(string $type, Model $item = null): void
    {
        $this->widgets[] = CheckboxWidget::create('read', trans('sanjab::sanjab.readen'))
                            ->customModifyResponse(function (stdClass $response, Model $item) {
                                $response->read = $item->read_at != null;
                            });

        $this->widgets[] = ShowWidget::create('text', trans('sanjab::sanjab.text'))
                            ->customModifyResponse(function (stdClass $response, Model $item) {
                                $response->text = array_get($item->data, 'text');
                            });

        $this->actions[] = Action::create(trans('sanjab::sanjab.show'))
                            ->perItem(true)
                            ->variant('warning')
                            ->icon('remove_red_eye')
                            ->authorize(function ($notification) {
                                return array_get($notification->data, 'url') != null;
                            })
                            ->url(function ($notification) {
                                return route('sanjab.notifications.open-url', ['id' => $notification->id]);
                            })
                            ->target('_blank');
        $this->actions[] = Action::create(trans('sanjab::sanjab.i_read'))
                            ->perItem(true)
                            ->variant('success')
                            ->icon('check')
                            ->action('markAsReadNotification')
                            ->authorize(function ($notification) {
                                return $notification->read_at == null;
                            })
                            ->confirm(trans('sanjab::sanjab.are_you_sure'));
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->initCrud('index');

        // items for json ajax.
        if ($request->wantsJson()) {
            return $this->indexJson($request);
        }

        // view it self without items
        return view(
            'sanjab::crud.list',
            [
                'widgets' => $this->widgets,
                'actions' => $this->actions,
                'filterOptions' => $this->filters,
                'cards' => $this->cards,
                'properties' => $this->properties(),
            ]
        );
    }

    public static function notifications(): array
    {
        $output = [];
        if (! (Schema::hasTable('notifications') && in_array('Illuminate\Notifications\Notifiable', class_uses(Sanjab::userModel())))) {
            return $output;
        }
        $output[] = NotificationItem::create('notifications')
                ->title(trans('sanjab::sanjab.notifications'))
                ->badge(Auth::user()->unreadNotifications()->where('data', 'LIKE', '%"text":%')->count());
        $notifications = Auth::user()->unreadNotifications()->where('data', 'LIKE', '%"text":%')->limit(10)->get();
        if ($notifications->count() > 0) {
            $lastCreatedAt = $notifications->max('created_at')->timestamp;
        } else {
            $notifications = Auth::user()->notifications()->where('data', 'LIKE', '%"text":%')->latest()->limit(5)->get();
        }
        foreach ($notifications as $notification) {
            if (! isset($notification->data['text'])) {
                continue;
            }
            $url = array_get($notification->data, 'url');
            $output[0]->addItem(
                        $notification->data['text'],
                        $url ? route('sanjab.notifications.open-url', ['id' => $notification->id]) : '#',
                        ['id' => $notification->id, 'notificationSound' => array_get($notification->data, 'sound', false), 'notificationToast' => array_get($notification->data, 'toast', false)]
                    );
        }
        $output[0]->addDivider();
        if ($notifications->count() > 0 && $notifications->first()->read_at == null) {
            $output[0]->addItem(trans('sanjab::sanjab.i_read'), 'javascript:window.sanjabApp.$sanjabStore.commit("markAsRead")');
        }
        $output[0]->addItem(trans('sanjab::sanjab.all'), route('sanjab.modules.notifications.index'), ['active' => true]);

        return $output;
    }

    /**
     * Open notification url and mark it seen.
     *
     * @param string $id
     * @return mixed
     */
    public function openUrl($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();
        if (array_get($notification->data, 'url')) {
            return redirect($notification->data['url']);
        }

        return redirect()->back();
    }

    /**
     * Mark one notification as read.
     *
     * @param Notification $notification
     * @return array
     */
    public function markAsReadNotification(DatabaseNotification $notification)
    {
        $notification->markAsRead();

        return ['success' => true];
    }

    /**
     * Mark as Read all notifications.
     *
     * @param Request $request
     * @return mixed
     */
    public function markAsRead(Request $request)
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);
        if ($request->wantsJson()) {
            return ['success' => true];
        }

        return redirect()->back();
    }

    /**
     * Stream notifications real time.
     *
     * @param Request $request
     * @return mixed
     */
    public function stream(Request $request)
    {
        try {
            set_time_limit(600);
            ini_set('max_execution_time', 600);
        } catch (Exception $e) {
            // Prevent error when changing timelimit is not allowed.
        }

        $lastCreatedAt = $request->input('time');
        Session::save();
        $response = response()->stream(function () use ($request, $lastCreatedAt) {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            echo 'data: '.json_encode(['type' => 'start'])."\n\n";
            flush();
            if ($request->input('force') == 'true') {
                $notificationItems = Sanjab::notificationItems(true);
                echo 'data: '.json_encode(['type' => 'items', 'items' => $notificationItems])."\n\n";
                flush();
            }
            $lastResponseTime = time();
            while (true) {
                // Show new messages.
                $notifications = Auth::user()->unreadNotifications()->where('created_at', '>', Carbon::createFromTimestamp($lastCreatedAt))->get();
                if ($notifications->count() > 0) {
                    $lastCreatedAt = $notifications->max('created_at')->timestamp;
                    $notificationItems = Sanjab::notificationItems(true);
                    echo 'data: '.json_encode(['type' => 'items', 'items' => $notificationItems])."\n\n";
                    flush();
                    $lastResponseTime = time();
                }

                // Prevent Maximum execution time of N seconds exceeded error.
                if ((microtime(true) - LARAVEL_START) + 3 >= intval(ini_get('max_execution_time'))) {
                    echo 'data: '.json_encode(['type' => 'close'])."\n\n";
                    flush();

                    return;
                }

                // Prevent keep alive timeout
                if (time() - $lastResponseTime >= 10) {
                    echo "data: []\n\n";
                    flush();
                    $lastResponseTime = time();
                }

                sleep(1);
            }
        });
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }

    protected function queryScope(Builder $query)
    {
        $query->where('data', 'LIKE', '%"text":%')->whereHasMorph('notifiable', [Sanjab::userModel()], function ($query) {
            $query->where('id', Auth::id());
        });
    }

    public static function menus(): array
    {
        return [];
    }

    public static function permissions(): array
    {
        return [];
    }

    public static function routes(): void
    {
        parent::routes();
        Route::get('/notifications/stream', static::class.'@stream')->name('notifications.stream');
        Route::get('/notifications/mark-as-read', static::class.'@markAsRead')->name('notifications.mark-as-read');
        Route::get('/notifications/{id}/open-url', static::class.'@openUrl')->name('notifications.open-url');
    }
}
