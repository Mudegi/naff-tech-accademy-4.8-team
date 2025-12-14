<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklyPerformanceSummary extends Notification implements ShouldQueue
{
    use Queueable;

    protected $student;
    protected $performanceData;

    /**
     * Create a new notification instance.
     */
    public function __construct($student, $performanceData)
    {
        $this->student = $student;
        $this->performanceData = $performanceData;
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
        $overallAvg = $this->performanceData['overall_average'];
        $trend = $this->performanceData['trend'] ?? 'stable';
        
        $message = (new MailMessage)
            ->subject('Weekly Performance Summary - ' . $this->student->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Here is the weekly performance summary for ' . $this->student->name . '.');

        // Overall performance
        $message->line('**Overall Performance:** ' . $overallAvg . '% (Grade ' . $this->performanceData['letter_grade'] . ')');
        
        // Trend indicator
        if ($trend === 'improving') {
            $message->line('📈 Great news! Performance is **improving**!');
        } elseif ($trend === 'declining') {
            $message->line('📉 Performance is **declining**. Consider reaching out to teachers.');
        } else {
            $message->line('➡️ Performance is **stable**.');
        }

        // Detailed breakdown
        $message->line('')
            ->line('**This Week:**')
            ->line('• Assignments: ' . $this->performanceData['assignments_count'] . ' completed')
            ->line('• Average Assignment Grade: ' . $this->performanceData['assignment_avg'] . '%')
            ->line('• Exams Recorded: ' . $this->performanceData['exams_count'])
            ->line('• Average Exam Grade: ' . $this->performanceData['exam_avg'] . '%');

        // Pending work alert
        if ($this->performanceData['pending_assignments'] > 0) {
            $message->line('⚠️ **' . $this->performanceData['pending_assignments'] . ' assignment(s)** awaiting grading');
        }

        $message->action('View Full Performance Report', route('parent.children.show', $this->student->id))
            ->line('Thank you for staying engaged in your child\'s education!');

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
            'overall_average' => $this->performanceData['overall_average'],
            'type' => 'weekly_summary'
