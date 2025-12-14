<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Scores Report - {{ $user->name }}</title>
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .stat-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
        }
        
        .stat-value {
            font-size: 18px;
            font-weight: 700;
            color: #3b82f6;
            margin-bottom: 2px;
        }
        
        .stat-label {
            font-size: 10px;
            color: #6b7280;
            font-weight: 500;
        }
        
        .grade-distribution {
            background: #f0f9ff;
            border: 1px solid #0ea5e9;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 15px;
        }
        
        .grade-distribution h3 {
            color: #0369a1;
            font-size: 14px;
            margin: 0 0 8px 0;
            text-align: center;
        }
        
        .distribution-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
        }
        
        .distribution-item {
            text-align: center;
            padding: 5px;
            background: #fff;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
        }
        
        .distribution-count {
            font-size: 14px;
            font-weight: 700;
            color: #0369a1;
        }
        
        .distribution-label {
            font-size: 9px;
            color: #6b7280;
        }
        
        .assignments-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
        }
        
        .assignments-table th,
        .assignments-table td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            text-align: left;
        }
        
        .assignments-table th {
            background: #f8fafc;
            font-weight: 600;
            color: #374151;
        }
        
        .assignments-table tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .grade-excellent {
            color: #059669;
            font-weight: 700;
        }
        
        .grade-good {
            color: #0ea5e9;
            font-weight: 700;
        }
        
        .grade-average {
            color: #f59e0b;
            font-weight: 700;
        }
        
        .grade-below-average {
            color: #f97316;
            font-weight: 700;
        }
        
        .grade-poor {
            color: #ef4444;
            font-weight: 700;
        }
        
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            color: #9ca3af;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Student Scores Report</h1>
        <h2>Teacher: {{ $user->name }} | Generated: {{ now()->format('M d, Y H:i A') }}</h2>
    </div>

    @if($totalAssignments > 0)
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ $totalAssignments }}</div>
                <div class="stat-label">Total Assignments</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $averageGrade }}%</div>
                <div class="stat-label">Average Grade</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $highestGrade }}%</div>
                <div class="stat-label">Highest Grade</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $lowestGrade }}%</div>
                <div class="stat-label">Lowest Grade</div>
            </div>
        </div>

        <!-- Grade Distribution -->
        <div class="grade-distribution">
            <h3>Grade Distribution</h3>
            <div class="distribution-grid">
                <div class="distribution-item">
                    <div class="distribution-count">{{ $gradeDistribution['excellent'] }}</div>
                    <div class="distribution-label">Excellent (90-100%)</div>
                </div>
                <div class="distribution-item">
                    <div class="distribution-count">{{ $gradeDistribution['good'] }}</div>
                    <div class="distribution-label">Good (80-89%)</div>
                </div>
                <div class="distribution-item">
                    <div class="distribution-count">{{ $gradeDistribution['average'] }}</div>
                    <div class="distribution-label">Average (70-79%)</div>
                </div>
                <div class="distribution-item">
                    <div class="distribution-count">{{ $gradeDistribution['below_average'] }}</div>
                    <div class="distribution-label">Below Average (60-69%)</div>
                </div>
                <div class="distribution-item">
                    <div class="distribution-count">{{ $gradeDistribution['poor'] }}</div>
                    <div class="distribution-label">Poor (<60%)</div>
                </div>
            </div>
        </div>

        <!-- Assignments Table -->
        <table class="assignments-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student Name</th>
                    <th>Assignment Title</th>
                    <th>Subject</th>
                    <th>Topic</th>
                    <th>Grade Level</th>
                    <th>Term</th>
                    <th>Grade</th>
                    <th>Submitted</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assignments as $assignment)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $assignment->student->name }}</td>
                    <td>{{ $assignment->resource->title }}</td>
                    <td>{{ $assignment->resource->subject->name ?? 'N/A' }}</td>
                    <td>{{ $assignment->resource->topic->name ?? 'N/A' }}</td>
                    <td>{{ $assignment->resource->grade_level ?? 'N/A' }}</td>
                    <td>{{ $assignment->resource->term->name ?? 'N/A' }}</td>
                    <td class="grade-{{ $assignment->grade >= 90 ? 'excellent' : ($assignment->grade >= 80 ? 'good' : ($assignment->grade >= 70 ? 'average' : ($assignment->grade >= 60 ? 'below-average' : 'poor'))) }}">
                        {{ $assignment->grade }}%
                    </td>
                    <td>{{ $assignment->submitted_at->format('M d, Y') }}</td>
                    <td>{{ ucfirst($assignment->status) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <h3>No Graded Assignments Found</h3>
            <p>No student assignments with grades match your current filters.</p>
        </div>
    @endif

    <div class="footer">
        <p>Generated on {{ now()->format('M d, Y H:i A') }}</p>
        <p>Naf Academy - Student Scores Report</p>
    </div>
</body>
</html>
