<?php

namespace Sanjab\Tests\App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TestNotification extends Notification
{
    use Queueable;

    /**
     * @var string
     */
    protected $text = null;

    /**
     * @var string
     */
    protected $url = null;

    /**
     * @var boolean
     */
    protected $sound = false;

    /**
     * @var boolean
     */
    protected $toast = false;

    /**
     * Create a new notification instance.
     *
     * @param string $text
     * @param string $url
     * @param boolean $sound
     * @param boolean $toast
     */
    public function __construct(string $text, string $url = '#', bool $sound = false, bool $toast = false)
    {
        $this->text = $text;
        $this->url = $url;
        $this->sound = $sound;
        $this->toast = $toast;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'text'  => $this->text,
            'url'   => $this->url,
            'sound' => $this->sound,
            'toast' => $this->toast,
        ];
    }
}
