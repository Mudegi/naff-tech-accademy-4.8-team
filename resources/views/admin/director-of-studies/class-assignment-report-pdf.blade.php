<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Assignment Report - {{ $class->name }}</title>
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
            font-size: 10px;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            color: #1a1a1a;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .header h2 {
            color: #6b7280;
            font-size: 14px;
            font-weight: 400;
        }
        
        .class-info {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 20px;
        }
        
        .class-info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-weight: 600;
            color: #374151;
            font-size: 9px;
            margin-bottom: 3px;
        }
        
        .info-value {
            color: #1a1a1a;
            font-size: 10px;
        }
        
        .student-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .student-header {
            background: #2563eb;
            color: white;
            padding: 10px 15px;
            border-radius: 6px 6px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .student-name {
            font-size: 14px;
            font-weight: 700;
        }
        
        .student-meta {
            font-size: 9px;
            opacity: 0.9;
        }
        
        .average-grade {
            background: white;
            color: #2563eb;
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 12px;
        }
        
        .student-details {
            background: #f9fafb;
            padding: 10px 15px;
            border: 1px solid #e5e7eb;
            border-top: none;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            font-size: 9px;
        }
        
        .assignment-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-top: none;
            padding: 12px 15px;
            page-break-inside: avoid;
        }
        
        .assignment-card:last-child {
            border-radius: 0 0 6px 6px;
        }
        
        .assignment-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .assignment-title {
            flex: 1;
        }
        
        .assignment-title h4 {
            color: #1a1a1a;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 3px;
        }
        
        .assignment-meta {
            color: #6b7280;
            font-size: 8px;
        }
        
        .grade-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 12px;
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
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 8px;
            font-size: 8px;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            font-weight: 600;
            color: #6b7280;
            font-size: 8px;
            margin-bottom: 2px;
        }
        
        .detail-value {
            color: #1a1a1a;
            font-size: 9px;
        }
        
        .feedback-section {
            background: #f9fafb;
            border-left: 3px solid #2563eb;
            padding: 8px;
            margin-top: 8px;
            border-radius: 4px;
            font-size: 8px;
        }
        
        .feedback-label {
            font-weight: 600;
            color: #374151;
            font-size: 8px;
            margin-bottom: 3px;
        }
        
        .feedback-text {
            color: #1a1a1a;
            font-size: 8px;
            line-height: 1.4;
        }
        
        .rank-badge {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #2563eb;
            color: white;
            text-align: center;
            line-height: 20px;
            font-weight: 700;
            font-size: 10px;
            margin-right: 6px;
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
            font-size: 8px;
        }
        
        .summary-stats {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 20px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            text-align: center;
        }
        
        .stat-item {
            display: flex;
            flex-direction: column;
        }
        
        .stat-value {
            font-size: 18px;
            font-weight: 700;
            color: #1e40af;
        }
        
        .stat-label {
            font-size: 9px;
            color: #6b7280;
            margin-top: 3px;
        }
        
        @page {
            margin: 1cm;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Class Assignment Report</h1>
        <h2>{{ $class->name }}</h2>
        @if($school)
            <h2 style="font-size: 12px; margin-top: 5px;">{{ $school->name }}</h2>
        @endif
        <p style="font-size: 9px; color: #6b7280; margin-top: 5px;">Generated on {{ now()->format('F d, Y') }} by {{ $generatedBy }}</p>
    </div>

    <div class="class-info">
        <div class="class-info-grid">
            <div class="info-item">
                <span class="info-label">Class Name</span>
                <span class="info-value">{{ $class->name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Grade Level</span>
                <span class="info-value">{{ $class->grade_level }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Term</span>
                <span class="info-value">{{ $class->term }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Total Students</span>
                <span class="info-value">{{ $studentsWithAssignments->count() }}</span>
            </div>
        </div>
    </div>

    <div class="summary-stats">
        <div class="stat-item">
            <div class="stat-value">{{ $studentsWithAssignments->count() }}</div>
            <div class="stat-label">Students with Assignments</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $topCount }}</div>
            <div class="stat-label">Top Assignments per Student</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ number_format($studentsWithAssignments->avg(function($item) { return $item['assignments']->avg('grade'); }), 1) }}%</div>
            <div class="stat-label">Average Grade</div>
        </div>
    </div>

    @foreach($studentsWithAssignments as $studentData)
        <div class="student-section">
            <div class="student-header">
                <div>
                    <div class="student-name">{{ $studentData['student']->name }}</div>
                    <div class="student-meta">
                        @if($studentData['student']->email)
                            {{ $studentData['student']->email }}
                        @endif
                        @if($studentData['student']->student && $studentData['student']->student->registration_number)
                            | Reg: {{ $studentData['student']->student->registration_number }}
                        @endif
                    </div>
                </div>
                <div class="average-grade">
                    Avg: {{ number_format($studentData['assignments']->avg('grade'), 1) }}%
                </div>
            </div>

            <div class="student-details">
                <div class="info-item">
                    <span class="info-label">Email</span>
                    <span class="info-value">{{ $studentData['student']->email ?? 'N/A' }}</span>
                </div>
                @if($studentData['student']->student && $studentData['student']->student->registration_number)
                <div class="info-item">
                    <span class="info-label">Registration Number</span>
                    <span class="info-value">{{ $studentData['student']->student->registration_number }}</span>
                </div>
                @endif
                @if($studentData['student']->student && $studentData['student']->student->class)
                <div class="info-item">
                    <span class="info-label">Class</span>
                    <span class="info-value">{{ $studentData['student']->student->class }}</span>
                </div>
                @endif
            </div>

            @foreach($studentData['assignments'] as $index => $assignment)
                <div class="assignment-card">
                    <div class="assignment-header">
                        <div class="assignment-title">
                            <span class="rank-badge rank-{{ min($index + 1, 3) }}">{{ $index + 1 }}</span>
                            <h4>{{ $assignment->resource->title }}</h4>
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
        </div>
    @endforeach

    <div class="footer">
        <p>This report contains the top {{ $topCount }} highest-scoring assignments for each student in {{ $class->name }}.</p>
        <p>Generated by {{ config('app.name') }} on {{ now()->format('F d, Y \a\t H:i A') }}</p>
    </div>
</body>
</html>

