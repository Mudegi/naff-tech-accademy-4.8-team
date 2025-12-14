<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MissingAssignmentAlert extends Notification implements ShouldQueue
{
    use Queueable;

    protected $student;
    protected $overdueAssignments;

    /**
     * Create a new notification instance.
     */
    public function __construct($student, $overdueAssignments)
    {
        $this->student = $student;
        $this->overdueAssignments = $overdueAssignments;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $count = count($this->overdueAssignments);
        
        $message = (new MailMessage)
            ->subject('📝 Missing Assignment Alert - ' . $this->student->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->student->name . ' has **' . $count . ' overdue assignment(s)** that have not been submitted.')
            ->line('')
            ->line('**Overdue Assignments:**');

        // List overdue assignments
        foreach ($this->overdueAssignments as $assignment) {
            $daysOverdue = now()->diffInDays($assignment->due_date);
            $message->line('• **' . $assignment->title . '** - Due: ' . $assignment->due_date->format('M d, Y') . ' (' . $daysOverdue . ' days overdue)');
        }

        $message->line('')
            ->line('**Impact of Missing Assignments:**')
            ->line('• Lower overall grade average')
            ->line('• Difficulty catching up with coursework')
            ->line('• Potential gaps in understanding material')
            ->line('')
            ->line('**What You Can Do:**')
            ->line('1. Talk to your child about the missing work')
            ->line('2. Help them create a schedule to complete overdue assignments')
            ->line('3. Contact the teacher to discuss submission possibilities')
            ->line('4. Ensure your child has the resources needed to complete the work')
            ->action('View All Assignments', route('parent.children.show', $this->student->id))
            ->line('Consistent submission of assignments is crucial for academic success.');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'student_id' => $this->student->id,
            'student_name' => $this->student->name,
            'overdue_count' => count($this->overdueAssignments),
            'assignments' => $this->overdueAssignments->map(function($a) {
                return [
                    'title' => $a->title,
                    'due_date' => $a->due_date->format('Y-m-d')
                ];
            }),
            'type' => 'missing_assignment'
