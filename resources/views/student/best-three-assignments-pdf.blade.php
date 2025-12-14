<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Best 3 Assignments - {{ $user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 20px;
            font-size: 11px;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            color: #1a1a1a;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .header h2 {
            color: #6b7280;
            font-size: 14px;
            font-weight: 400;
        }
        
        .student-info {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 20px;
        }
        
        .student-info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-weight: 600;
            color: #374151;
            font-size: 10px;
            margin-bottom: 3px;
        }
        
        .info-value {
            color: #1a1a1a;
            font-size: 11px;
        }
        
        .assignment-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        
        .assignment-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .assignment-title {
            flex: 1;
        }
        
        .assignment-title h3 {
            color: #1a1a1a;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .assignment-meta {
            color: #6b7280;
            font-size: 10px;
        }
        
        .grade-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 16px;
            color: white;
        }
        
        .grade-excellent {
            background: #10b981;
        }
        
        .grade-good {
            background: #3b82f6;
        }
        
        .grade-average {
            background: #f59e0b;
        }
        
        .grade-below-average {
            background: #ef4444;
        }
        
        .assignment-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            font-weight: 600;
            color: #6b7280;
            font-size: 9px;
            margin-bottom: 3px;
        }
        
        .detail-value {
            color: #1a1a1a;
            font-size: 10px;
        }
        
        .feedback-section {
            background: #f9fafb;
            border-left: 3px solid #2563eb;
            padding: 10px;
            margin-top: 10px;
            border-radius: 4px;
        }
        
        .feedback-label {
            font-weight: 600;
            color: #374151;
            font-size: 10px;
            margin-bottom: 5px;
        }
        
        .feedback-text {
            color: #1a1a1a;
            font-size: 10px;
            line-height: 1.5;
        }
        
        .rank-badge {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #2563eb;
            color: white;
            text-align: center;
            line-height: 30px;
            font-weight: 700;
            font-size: 14px;
            margin-right: 10px;
        }
        
        .rank-1 {
            background: #fbbf24;
        }
        
        .rank-2 {
            background: #94a3b8;
        }
        
        .rank-3 {
            background: #cd7f32;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 9px;
        }
        
        @page {
            margin: 1cm;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Best 3 Assignments Report</h1>
        <h2>{{ $user->name }}</h2>
        @if($school)
            <h2 style="font-size: 12px; margin-top: 5px;">{{ $school->name }}</h2>
        @endif
        <p style="font-size: 10px; color: #6b7280; margin-top: 5px;">Generated on {{ now()->format('F d, Y') }}</p>
    </div>

    <div class="student-info">
        <div class="student-info-grid">
            <div class="info-item">
                <span class="info-label">Student Name</span>
                <span class="info-value">{{ $user->name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Email</span>
                <span class="info-value">{{ $user->email ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Phone</span>
                <span class="info-value">{{ $user->phone_number ?? 'N/A' }}</span>
            </div>
            @if($user->student && $user->student->registration_number)
            <div class="info-item">
                <span class="info-label">Registration Number</span>
                <span class="info-value">{{ $user->student->registration_number }}</span>
            </div>
            @endif
            @if($user->student && $user->student->class)
            <div class="info-item">
                <span class="info-label">Class</span>
                <span class="info-value">{{ $user->student->class }}</span>
            </div>
            @endif
            <div class="info-item">
                <span class="info-label">Total Graded Assignments</span>
                <span class="info-value">{{ $assignments->count() }} of Top 3</span>
            </div>
        </div>
    </div>

    @foreach($assignments as $index => $assignment)
        <div class="assignment-card">
            <div class="assignment-header">
                <div class="assignment-title">
                    <span class="rank-badge rank-{{ $index + 1 }}">{{ $index + 1 }}</span>
                    <h3>{{ $assignment->resource->title }}</h3>
                    <div class="assignment-meta">
                        @if($assignment->resource->topic)
                            Topic: {{ $assignment->resource->topic->name }} | 
                        @endif
                        Submitted: {{ $assignment->submitted_at->format('M d, Y') }}
                    </div>
                </div>
                <div>
                    @php
                        $gradeClass = 'grade-average';
                        if ($assignment->grade >= 90) $gradeClass = 'grade-excellent';
                        elseif ($assignment->grade >= 80) $gradeClass = 'grade-good';
                        elseif ($assignment->grade >= 70) $gradeClass = 'grade-average';
                        else $gradeClass = 'grade-below-average';
                    @endphp
                    <span class="grade-badge {{ $gradeClass }}">{{ $assignment->grade }}%</span>
                </div>
            </div>

            <div class="assignment-details">
                <div class="detail-item">
                    <span class="detail-label">Subject</span>
                    <span class="detail-value">{{ $assignment->resource->subject->name ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Term</span>
                    <span class="detail-value">{{ $assignment->resource->term->name ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Grade Level</span>
                    <span class="detail-value">{{ $assignment->resource->grade_level ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Status</span>
                    <span class="detail-value">{{ ucfirst($assignment->status) }}</span>
                </div>
                @if($assignment->resource->teacher)
                <div class="detail-item">
                    <span class="detail-label">Teacher</span>
                    <span class="detail-value">{{ $assignment->resource->teacher->name }}</span>
                </div>
                @endif
                <div class="detail-item">
                    <span class="detail-label">Reviewed Date</span>
                    <span class="detail-value">{{ $assignment->reviewed_at ? $assignment->reviewed_at->format('M d, Y') : 'Not reviewed' }}</span>
                </div>
            </div>

            @if($assignment->teacher_feedback)
                <div class="feedback-section">
                    <div class="feedback-label">Teacher Feedback:</div>
                    <div class="feedback-text">{{ $assignment->teacher_feedback }}</div>
                </div>
            @endif
        </div>
    @endforeach

    <div class="footer">
        <p>This report contains your top 3 highest-scoring assignments.</p>
        <p>Generated by {{ config('app.name') }} on {{ now()->format('F d, Y \a\t H:i A') }}</p>
    </div>
</body>
</html>

