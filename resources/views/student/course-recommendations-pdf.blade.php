<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Course Recommendations Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }
        .header h1 {
            color: #667eea;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .header p {
            color: #666;
            font-size: 14px;
        }
        .student-info {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .student-info h2 {
            color: #1a1a1a;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
        }
        .info-label {
            font-weight: 600;
            color: #6b7280;
        }
        .info-value {
            color: #1a1a1a;
            font-weight: 500;
        }
        .summary-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .summary-box h2 {
            font-size: 18px;
            margin-bottom: 15px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        .summary-item {
            text-align: center;
        }
        .summary-label {
            font-size: 11px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 24px;
            font-weight: 700;
        }
        .marks-section {
            margin-bottom: 25px;
        }
        .marks-section h2 {
            color: #1a1a1a;
            font-size: 16px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .marks-table th {
            background: #667eea;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
        }
        .marks-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        .marks-table tr:nth-child(even) {
            background: #f9fafb;
        }
        .university-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .university-header {
            background: #667eea;
            color: white;
            padding: 15px;
            border-radius: 8px 8px 0 0;
            margin-bottom: 0;
        }
        .university-header h2 {
            font-size: 18px;
            margin: 0;
        }
        .university-header p {
            font-size: 12px;
            opacity: 0.9;
            margin-top: 5px;
        }
        .course-card {
            border: 1px solid #e5e7eb;
            border-top: none;
            padding: 15px;
            background: white;
        }
        .course-card:last-child {
            border-radius: 0 0 8px 8px;
        }
        .course-title {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
        }
        .course-meta {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin: 10px 0;
            padding: 10px;
            background: #f9fafb;
            border-radius: 6px;
        }
        .meta-item {
            text-align: center;
        }
        .meta-label {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 3px;
        }
        .meta-value {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
        }
        .meta-value.positive {
            color: #059669;
        }
        .meta-value.negative {
            color: #dc2626;
        }
        .subjects-list {
            margin-top: 10px;
            font-size: 11px;
        }
        .subjects-list strong {
            color: #6b7280;
        }
        .subjects-list span {
            display: inline-block;
            background: #e0e7ff;
            color: #4338ca;
            padding: 3px 8px;
            border-radius: 4px;
            margin: 2px;
            font-size: 10px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Course Recommendations Report</h1>
        <p>Generated on {{ now()->format('F d, Y') }}</p>
    </div>

    <!-- Student Information -->
    <div class="student-info">
        <h2>Student Information</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Name:</span>
                <span class="info-value">{{ $user->name }}</span>
            </div>
            @if($user->email)
            <div class="info-item">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $user->email }}</span>
            </div>
            @endif
            @if($user->phone_number)
            <div class="info-item">
                <span class="info-label">Phone:</span>
                <span class="info-value">{{ $user->phone_number }}</span>
            </div>
            @endif
            @if($user->school)
            <div class="info-item">
                <span class="info-label">School:</span>
                <span class="info-value">{{ $user->school->name }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Summary -->
    <div class="summary-box">
        <h2>Academic Summary</h2>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Aggregate Points</div>
                <div class="summary-value">{{ number_format($aggregatePoints, 1) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Principal Passes</div>
                <div class="summary-value">{{ $principalPasses }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Qualifying Courses</div>
                <div class="summary-value">{{ $qualifyingCourses->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Academic Year</div>
                <div class="summary-value">{{ $currentYear }}</div>
            </div>
        </div>
    </div>

    <!-- Student Marks -->
    @if($studentMarks->count() > 0)
    <div class="marks-section">
        <h2>UACE Principal Passes</h2>
        <table class="marks-table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Grade</th>
                    <th>Points</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studentMarks as $mark)
                    <tr>
                        <td>{{ $mark->subject_name }}</td>
                        <td>{{ $mark->grade }}</td>
                        <td>{{ $mark->points ?? 0 }}</td>
                        <td>
                            @if($mark->is_essential) Essential @endif
                            @if($mark->is_relevant) Relevant @endif
                            @if($mark->is_desirable) Desirable @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Course Recommendations -->
    @if($qualifyingCourses->isEmpty())
        <div style="text-align: center; padding: 40px; background: #f9fafb; border-radius: 8px;">
            <h3 style="color: #6b7280; margin-bottom: 10px;">No Course Recommendations Available</h3>
            <p style="color: #9ca3af;">
                @if($aggregatePoints == 0)
                    Please add your UACE marks to get course recommendations.
                @else
                    Your current aggregate points ({{ number_format($aggregatePoints, 1) }}) do not meet the minimum requirements for any courses.
                @endif
            </p>
        </div>
    @else
        @foreach($groupedByUniversity as $universityName => $courses)
            <div class="university-section">
                <div class="university-header">
                    <h2>{{ $universityName }}</h2>
                    <p>{{ $courses->count() }} {{ Str::plural('course', $courses->count()) }} available</p>
                </div>

                @foreach($courses as $course)
                    <div class="course-card">
                        <div class="course-title">{{ $course->course_name }}</div>
                        @if($course->faculty)
                            <div style="font-size: 11px; color: #6b7280; margin-bottom: 10px;">{{ $course->faculty }}</div>
                        @endif

                        <div class="course-meta">
                            <div class="meta-item">
                                <div class="meta-label">Cut-Off Points</div>
                                @if($course->cut_off_format === 'makerere' && $course->program_category === 'stem')
                                    <div class="meta-value" style="font-size: 0.75rem;">
                                        <div>M: {{ $course->cut_off_points_male ? number_format($course->cut_off_points_male, 1) : 'N/A' }}</div>
                                        <div>F: {{ $course->cut_off_points_female ? number_format($course->cut_off_points_female, 1) : 'N/A' }}</div>
                                    </div>
                                @else
                                    <div class="meta-value">{{ number_format($course->effective_cut_off ?? $course->cut_off_points, 1) }}</div>
                                @endif
                            </div>
                            <div class="meta-item">
                                <div class="meta-label">Your Points</div>
                                <div class="meta-value">{{ number_format($aggregatePoints, 1) }}</div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-label">Difference</div>
                                @php
                                    $effectiveCutOff = $course->effective_cut_off ?? $course->cut_off_points;
                                    $difference = $aggregatePoints - $effectiveCutOff;
                                @endphp
                                <div class="meta-value {{ $difference >= 0 ? 'positive' : 'negative' }}">
                                    {{ $difference >= 0 ? '+' : '' }}{{ number_format($difference, 1) }}
                                </div>
                            </div>
                        </div>

                        @if($course->essential_subjects)
                            <div class="subjects-list">
                                <strong>Essential Subjects:</strong>
                                @foreach($course->essential_subjects as $subject)
                                    <span>{{ $subject }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if($course->relevant_subjects)
                            <div class="subjects-list">
                                <strong>Relevant Subjects:</strong>
                                @foreach($course->relevant_subjects as $subject)
                                    <span>{{ $subject }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if($course->additional_requirements)
                            <div style="margin-top: 10px; padding: 8px; background: #fef3c7; border-left: 3px solid #f59e0b; border-radius: 4px; font-size: 10px; color: #78350f;">
                                <strong>Additional Requirements:</strong> {{ $course->additional_requirements }}
                            </div>
                        @endif

                        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 11px; color: #6b7280;">
                                Duration: {{ $course->duration_years ?? 'N/A' }} {{ $course->duration_years ? Str::plural('Year', $course->duration_years) : '' }}
                            </span>
                            @php
                                $effectiveCutOff = $course->effective_cut_off ?? $course->cut_off_points;
                                $isQualified = $aggregatePoints >= $effectiveCutOff;
                            @endphp
                            <span style="font-size: 11px; font-weight: 600; color: {{ $isQualified ? '#059669' : '#dc2626' }};">
                                {{ $isQualified ? '✓ Qualified' : '✗ Not Qualified' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    @endif

    <div class="footer">
        <p>This report was generated automatically by the Course Recommendation System.</p>
        <p>For more information, visit your dashboard or contact your school administration.</p>
    </div>
</body>
</html>

