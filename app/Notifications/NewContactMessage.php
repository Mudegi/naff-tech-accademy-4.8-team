<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactMessage extends Notification implements ShouldQueue
{
    use Queueable;

    protected $contactMessage;

    /**
     * Create a new notification instance.
     */
    public function __construct(ContactMessage $contactMessage)
    {
        $this->contactMessage = $contactMessage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Contact Message from ' . $this->contactMessage->name)
            ->greeting('Hello!')
            ->line('You have received a new contact message from ' . $this->contactMessage->name)
            ->line('Subject: ' . $this->contactMessage->subject)
            ->line('Message: ' . $this->contactMessage->message)
            ->line('Contact Information:')
            ->line('Email: ' . $this->contactMessage->email)
            ->line('Phone: ' . ($this->contactMessage->phone ?? 'Not provided'))
            ->action('View Message', route('admin.contact-messages.show', $this->contactMessage))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
