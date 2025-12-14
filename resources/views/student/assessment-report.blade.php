<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Report - {{ $assignment->resource->title }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 15px;
            background: #fff;
            font-size: 12px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .header h1 {
            color: #1a1a1a;
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }
        
        .header h2 {
            color: #6b7280;
            margin: 5px 0 0 0;
            font-size: 14px;
            font-weight: 400;
        }
        
        .main-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .left-column, .right-column {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .report-info {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 5px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 3px 0;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #374151;
        }
        
        .info-value {
            color: #6b7280;
        }
        
        .section {
            margin-bottom: 10px;
        }
        
        .section-title {
            color: #1a1a1a;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #3b82f6;
        }
        
        .assignment-details {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px;
            font-size: 11px;
        }
        
        .grade-section {
            background: #f0f9ff;
            border: 1px solid #0ea5e9;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
        }
        
        .grade-value {
            font-size: 32px;
            font-weight: 700;
            color: #0ea5e9;
            margin: 5px 0;
        }
        
        .grade-label {
            font-size: 14px;
            color: #0369a1;
            font-weight: 600;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-submitted {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .status-reviewed {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-graded {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .feedback-section {
            background: #f9fafb;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 10px;
        }
        
        .feedback-text {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 8px;
            margin-top: 5px;
            font-style: italic;
            color: #374151;
            font-size: 11px;
            max-height: 80px;
            overflow: hidden;
        }
        
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }
        
        .no-feedback {
            color: #9ca3af;
            font-style: italic;
            font-size: 11px;
        }
        
        .no-grade {
            color: #9ca3af;
            font-style: italic;
            font-size: 11px;
        }
        
        .compact-text {
            font-size: 11px;
            line-height: 1.3;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Assessment Report</h1>
        <h2>{{ $assignment->resource->title }}</h2>
    </div>

    <div class="main-content">
        <div class="left-column">
            <div class="section">
                <h3 class="section-title">Student Information</h3>
                <div class="report-info">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Name:</span>
                            <span class="info-value">{{ $assignment->student->name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Subject:</span>
                            <span class="info-value">{{ $assignment->resource->subject->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Grade Level:</span>
                            <span class="info-value">Grade {{ $assignment->resource->grade_level ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Term:</span>
                            <span class="info-value">{{ $assignment->resource->term->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Teacher:</span>
                            <span class="info-value">{{ $assignment->resource->teacher->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Submitted:</span>
                            <span class="info-value">{{ $assignment->submitted_at->format('M d, Y') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Status:</span>
                            <span class="info-value">
                                <span class="status-badge status-{{ $assignment->status }}">
                                    {{ ucfirst($assignment->status) }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section">
                <h3 class="section-title">Assignment Details</h3>
                <div class="assignment-details compact-text">
                    <p><strong>Title:</strong> {{ $assignment->resource->title }}</p>
                    @if($assignment->resource->description)
                        <p><strong>Description:</strong> {{ Str::limit($assignment->resource->description, 100) }}</p>
                    @endif
                    <p><strong>File Type:</strong> {{ strtoupper($assignment->assignment_file_type) }}</p>
                    @if($assignment->reviewed_at)
                        <p><strong>Reviewed:</strong> {{ $assignment->reviewed_at->format('M d, Y') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="right-column">
            @if($assignment->grade)
            <div class="section">
                <h3 class="section-title">Grade</h3>
                <div class="grade-section">
                    <div class="grade-label">Your Grade</div>
                    <div class="grade-value">{{ $assignment->grade }}%</div>
                    <div class="grade-label">
                        @if($assignment->grade >= 90)
                            Excellent Work!
                        @elseif($assignment->grade >= 80)
                            Good Job!
                        @elseif($assignment->grade >= 70)
                            Well Done!
                        @elseif($assignment->grade >= 60)
                            Keep Improving!
                        @else
                            Keep Working Hard!
                        @endif
                    </div>
                </div>
            </div>
            @else
            <div class="section">
                <h3 class="section-title">Grade</h3>
                <div class="assignment-details">
                    <p class="no-grade">This assignment has not been graded yet.</p>
                </div>
            </div>
            @endif

            @if($assignment->teacher_feedback)
            <div class="section">
                <h3 class="section-title">Teacher Feedback</h3>
                <div class="feedback-section">
                    <div class="feedback-text">
                        {{ $assignment->teacher_feedback }}
                    </div>
                </div>
            </div>
            @else
            <div class="section">
                <h3 class="section-title">Teacher Feedback</h3>
                <div class="feedback-section">
                    <p class="no-feedback">No feedback has been provided yet.</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="footer">
        <p>Generated on {{ now()->format('M d, Y H:i A') }}</p>
        <p>Naf Academy - Assessment Report</p>
    </div>
</body>
</html>
