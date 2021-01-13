<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\ExpoMessage;

class NewLineNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($userName, $postName)
    {
        $this->userName = $userName;
        $this->postName = $postName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return [ExpoChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toExpoPush($notifiable)
    {
        return ExpoMessage::create()
        ->badge(1)
        ->enableSound()
        ->title('Recibiste un nuevo like!')
        ->body('A '.$this->userName.' le ha gustado tu publicación '.$this->postName);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
        ];
    }
}
