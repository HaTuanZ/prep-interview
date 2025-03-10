<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuspiciousLoginNotification extends Notification
{
    use Queueable;

    protected $ip;
    protected $deviceId;
    protected $location;
    protected $mfaCode;
    /**
     * Create a new notification instance.
     */
    public function __construct($ip, $deviceId, $location, $mfaCode)
    {
        $this->ip = $ip;
        $this->deviceId = $deviceId;
        $this->location = $location;
        $this->mfaCode = $mfaCode;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Suspicious Login Alert!')
            ->line("There was a login from IP: {$this->ip}")
            ->line("Device: {$this->deviceId}")
            ->line("Location: {$this->location}")
            ->line("Code: {$this->mfaCode}");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ip'       => $this->ip,
            'deviceId' => $this->deviceId,
            'location' => $this->location,
            'mfaCode' => $this->mfaCode,
            'message'  => 'Suspicious login detected!'
        ];
    }
}
