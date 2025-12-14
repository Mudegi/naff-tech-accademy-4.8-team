<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>O-Level Course Recommendations - {{ $user->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
        }
        .header h1 {
            color: #667eea;
            margin: 0 0 5px 0;
            font-size: 24px;
        }
        .header p {
            margin: 3px 0;
            color: #666;
        }
        .student-info {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .student-info table {
            width: 100%;
        }
        .student-info td {
            padding: 5px;
        }
        .summary-boxes {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-box {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 15px;
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
        }
        .summary-box .value {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
        }
        .summary-box .label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-top: 5px;
        }
        .section-title {
            background: #667eea;
            color: white;
            padding: 10px;
            margin: 20px 0 10px 0;
            font-size: 14px;
            font-weight: bold;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.data-table th {
            background: #f8f9fa;
            padding: 8px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
            font-size: 10px;
            font-weight: bold;
        }
        table.data-table td {
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
        }
        .course-card {
            border: 1px solid #dee2e6;
            padding: 12px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
        .course-card h3 {
            color: #667eea;
            margin: 0 0 8px 0;
            font-size: 13px;
        }
        .course-details {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .course-detail {
            display: table-cell;
            width: 25%;
            font-size: 10px;
        }
        .course-detail .label {
            color: #666;
            font-size: 9px;
            text-transform: uppercase;
        }
        .course-detail .value {
            font-weight: bold;
            color: #333;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            margin-right: 5px;
            color: white;
        }
        .badge-essential { background: #dc3545; }
        .badge-relevant { background: #17a2b8; }
        .badge-match { background: #28a745; }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #dee2e6;
            padding-top: 5px;
        }
        .page-break {
            page-break-after: always;
        }
        .university-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px;
            margin: 15px 0 10px 0;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>🎓 O-Level Course Recommendations Report</h1>
        <p>Based on Uganda Certificate of Education (UCE) Performance</p>
        <p>Generated on {{ now()->format('F j, Y') }}</p>
    </div>

    <!-- Student Information -->
    <div class="student-info">
        <table>
            <tr>
                <td><strong>Student Name:</strong></td>
                <td>{{ $user->name }}</td>
                <td><strong>Email:</strong></td>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <td><strong>Academic Year:</strong></td>
                <td>{{ $currentYear }}</td>
                <td><strong>Report Date:</strong></td>
                <td>{{ now()->format('F j, Y') }}</td>
            </tr>
        </table>
    </div>

    <!-- Summary Boxes -->
    <div class="summary-boxes">
        <div class="summary-box">
            <div class="value">{{ $aggregatePoints }}</div>
            <div class="label">Aggregate Points</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ $qualifyingCourses->count() }}</div>
            <div class="label">Qualifying Courses</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ $groupedByUniversity->count() }}</div>
            <div class="label">Universities</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ count($marksWithPoints) }}</div>
            <div class="label">Subjects Selected</div>
        </div>
    </div>

    <!-- Selected Subjects -->
    <div class="section-title">📚 Your Selected O-Level Subjects</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Grade</th>
                <th>Percentage</th>
                <th>Points</th>
            </tr>
        </thead>
        <tbody>
            @foreach($marksWithPoints as $mark)
                <tr>
                    <td>{{ $mark['subject_name'] }}</td>
                    <td><strong>{{ $mark['grade'] }}</strong></td>
                    <td>{{ $mark['numeric_mark'] ? $mark['numeric_mark'] . '%' : 'N/A' }}</td>
                    <td><strong>{{ $mark['points'] }}</strong></td>
                </tr>
            @endforeach
            <tr style="background: #f8f9fa; font-weight: bold;">
                <td colspan="3" style="text-align: right;">Total Aggregate Points:</td>
                <td>{{ $aggregatePoints }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Qualifying Courses -->
    @if($qualifyingCourses->isEmpty())
        <div class="section-title">⚠️ No Qualifying Courses Found</div>
        <p style="padding: 20px; background: #fff3cd; border: 1px solid #ffc107;">
            Based on your aggregate of <strong>{{ $aggregatePoints }} points</strong>, we couldn't find courses that match your current performance. 
            Consider improving your grades or selecting higher-scoring subjects to increase your options.
        </p>
    @else
        <div class="section-title">🏛️ Qualifying University Courses</div>
        
        @foreach($groupedByUniversity as $universityName => $courses)
            <div class="university-header">
                {{ $universityName }} - {{ $courses->count() }} Course(s)
            </div>

            @foreach($courses as $course)
                <div class="course-card">
                    <h3>{{ $course->course_name }}</h3>
                    
                    <div class="course-details">
                        <div class="course-detail">
                            <div class="label">Cut-off Points</div>
                            <div class="value">{{ $course->effective_cut_off }}</div>
                        </div>
                        <div class="course-detail">
                            <div class="label">Your Points</div>
                            <div class="value">{{ $aggregatePoints }}</div>
                        </div>
                        <div class="course-detail">
                            <div class="label">Points Above</div>
                            <div class="value">+{{ $course->points_difference }}</div>
                        </div>
                        <div class="course-detail">
                            <div class="label">Match Score</div>
                            <div class="value">{{ $course->match_score }}</div>
                        </div>
                    </div>

                    @if($course->essential_subjects && count($course->essential_subjects) > 0)
                        <div style="margin-top: 8px;">
                            <strong style="font-size: 10px;">Essential Subjects:</strong><br>
                            @foreach($course->essential_subjects as $subject)
                                <span class="badge badge-essential">{{ $subject }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if($course->relevant_subjects && count($course->relevant_subjects) > 0)
                        <div style="margin-top: 5px;">
                            <strong style="font-size: 10px;">Relevant Subjects:</strong><br>
                            @foreach($course->relevant_subjects as $subject)
                                <span class="badge badge-relevant">{{ $subject }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if($course->match_score >= 20)
                        <div style="margin-top: 8px;">
                            <span class="badge badge-match">🌟 Excellent Match</span>
                        </div>
                    @elseif($course->match_score >= 10)
                        <div style="margin-top: 8px;">
                            <span class="badge badge-match">⭐ Good Match</span>
                        </div>
                    @endif

                    @if($course->description)
                        <div style="margin-top: 8px; font-size: 10px; color: #666;">
                            {{ $course->description }}
                        </div>
                    @endif
                </div>
            @endforeach
        @endforeach
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This report was generated automatically by the NAFF Tech Academy System.</p>
        <p>For more information, contact your school administration.</p>
    </div>
</body>
</html>
