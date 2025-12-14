<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactMessageReceived extends Notification implements ShouldQueue
{
    use Queueable;

    protected $contactMessage;

    public function __construct(ContactMessage $contactMessage)
    {
        $this->contactMessage = $contactMessage;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Thank you for contacting Naf Academy')
            ->greeting('Hello ' . $this->contactMessage->name . '!')
            ->line('Thank you for reaching out to Naf Academy. We have received your message and will get back to you as soon as possible.')
            ->line('Here is a copy of your message:')
            ->line('Subject: ' . $this->contactMessage->subject)
            ->line('Message: ' . $this->contactMessage->message)
            ->line('If you have any additional questions, please don\'t hesitate to contact us.')
            ->line('Best regards,')
            ->line('Naf Academy Team');
    }
} 