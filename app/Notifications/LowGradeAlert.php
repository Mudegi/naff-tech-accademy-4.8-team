<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowGradeAlert extends Notification implements ShouldQueue
{
    use Queueable;

    protected $student;
    protected $assignment;
    protected $grade;
    protected $totalMarks;

    /**
     * Create a new notification instance.
     */
    public function __construct($student, $assignment, $grade, $totalMarks)
    {
        $this->student = $student;
        $this->assignment = $assignment;
        $this->grade = $grade;
        $this->totalMarks = $totalMarks;
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
        $percentage = ($this->grade / $this->totalMarks) * 100;
        $assignmentTitle = $this->assignment->title ?? 'Assignment';
        
        return (new MailMessage)
            ->subject('⚠️ Low Grade Alert - ' . $this->student->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We wanted to inform you that ' . $this->student->name . ' received a below-average grade on a recent assignment.')
            ->line('')
            ->line('**Assignment:** ' . $assignmentTitle)
            ->line('**Grade:** ' . $this->grade . ' / ' . $this->totalMarks . ' (' . round($percentage, 1) . '%)')
            ->line('')
            ->line('This grade is below the 50% threshold, which may indicate:')
            ->line('• Difficulty understanding the material')
            ->line('• Need for additional support or tutoring')
            ->line('• Possible attendance or engagement issues')
            ->line('')
            ->line('**Recommended Actions:**')
            ->line('1. Discuss the assignment with your child')
            ->line('2. Contact the subject teacher for feedback')
            ->line('3. Consider arranging extra lessons or study sessions')
            ->line('4. Review other recent performance in this subject')
            ->action('View Full Performance Report', route('parent.children.show', $this->student->id))
            ->line('Early intervention can make a significant difference in your child\'s academic success.');
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
            'assignment_title' => $this->assignment->title ?? 'Assignment',
            'grade' => $this->grade,
            'total_marks' => $this->totalMarks,
            'percentage' => ($this->grade / $this->totalMarks) * 100,
            'type' => 'low_grade'
