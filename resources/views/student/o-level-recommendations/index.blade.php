@extends('layouts.student-dashboard')

@section('content')
<div class="recommendations-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div>
                <h1 class="page-title">A-Level Subject Combination Recommendations</h1>
                <p class="page-subtitle">Based on your academic performance, here are the best A-Level combinations for you</p>
            </div>
        </div>
    </div>

    @if(session('info'))
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> {{ session('info') }}
        </div>
    @endif
    
    <!-- Debug Info (temporary) -->
    <div class="info-card" style="margin-bottom: 1.5rem; background: #e0e7ff; border-left: 4px solid #4f46e5; padding: 1rem;">
        <strong>Debug Info:</strong><br>
        Available Exam Types Exists: {{ isset($availableExamTypes) ? 'Yes' : 'No' }}<br>
        Available Exam Types Count: {{ isset($availableExamTypes) ? $availableExamTypes->count() : 'N/A' }}<br>
        Available Types: {{ isset($availableExamTypes) ? $availableExamTypes->implode(', ') : 'None' }}<br>
        Selected Exam Type: {{ $selectedExamType ?? 'None' }}<br>
        Student Marks Count: {{ $studentMarks->count() }}<br>
        Performance Comparison: {{ isset($performanceComparison) ? 'Yes' : 'No' }}
    </div>

    <!-- Exam Type Selector -->
    @php
        $hasExamTypes = isset($availableExamTypes) && $availableExamTypes->count() > 0;
    @endphp
    
    @if(!$studentMarks->isEmpty())
        @if($hasExamTypes)
            <div class="info-card" style="margin-bottom: 1.5rem;">
                <form method="GET" action="{{ route('student.career-guidance.index') }}" id="examTypeForm">
                    <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                        <label for="exam_type" style="font-weight: 600; color: #374151; white-space: nowrap;">
                            <i class="fas fa-filter"></i> Select Exam Type:
                        </label>
                        <select name="exam_type" id="exam_type" onchange="document.getElementById('examTypeForm').submit()" style="flex: 1; min-width: 250px; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;">
                            <option value="">All Exams (Combined)</option>
                            @foreach($availableExamTypes as $type)
                                <option value="{{ $type }}" {{ $selectedExamType == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <small style="display: block; margin-top: 0.5rem; color: #6b7280;">
                        Choose which exam type's marks to use for A-Level combination recommendations. This helps you see which combinations suit your performance in specific exams.
                    </small>
                </form>
            </div>
        @else
            <div class="info-card" style="margin-bottom: 1.5rem; background: #fef3c7; border-left: 4px solid #f59e0b;">
                <div style="display: flex; align-items: start; gap: 0.75rem;">
                    <i class="fas fa-info-circle" style="color: #d97706; margin-top: 0.25rem;"></i>
                    <div>
                        <strong style="color: #92400e;">Exam Type Filtering Not Available</strong>
                        <p style="margin: 0.5rem 0 0 0; color: #78350f;">
                            Your marks don't have exam type information yet. Ask your teacher to specify the exam type (Beginning of Term, Mid Term, End of Term, Mock) when uploading new marks. This will allow you to filter recommendations by specific exam results and track your progress across different exams.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- Performance Comparison -->
    @if(isset($performanceComparison) && $performanceComparison)
        <div class="info-card" style="margin-bottom: 1.5rem; border-left: 4px solid {{ $performanceComparison['overall_status'] == 'improved' ? '#10b981' : ($performanceComparison['overall_status'] == 'declined' ? '#ef4444' : '#6b7280') }};">
            <h3 style="margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-chart-line"></i> Performance Comparison
            </h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                <div style="padding: 1rem; background: #f9fafb; border-radius: 0.375rem;">
                    <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;">Previous Exam</div>
                    <div style="font-weight: 600; color: #1f2937;">{{ $performanceComparison['previous_exam_type'] }}</div>
                    <div style="font-size: 1.25rem; font-weight: 700; color: #6b7280;">{{ number_format($performanceComparison['previous_avg_points'], 1) }} avg pts</div>
                </div>
                
                <div style="padding: 1rem; background: {{ $performanceComparison['overall_status'] == 'improved' ? '#d1fae5' : ($performanceComparison['overall_status'] == 'declined' ? '#fee2e2' : '#f3f4f6') }}; border-radius: 0.375rem;">
                    <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;">Current Exam</div>
                    <div style="font-weight: 600; color: #1f2937;">{{ $performanceComparison['current_exam_type'] }}</div>
                    <div style="font-size: 1.25rem; font-weight: 700; color: {{ $performanceComparison['overall_status'] == 'improved' ? '#065f46' : ($performanceComparison['overall_status'] == 'declined' ? '#991b1b' : '#374151') }};">
                        {{ number_format($performanceComparison['current_avg_points'], 1) }} avg pts
                    </div>
                </div>
                
                <div style="padding: 1rem; background: #f9fafb; border-radius: 0.375rem;">
                    <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;">Change</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: {{ $performanceComparison['avg_points_diff'] > 0 ? '#10b981' : ($performanceComparison['avg_points_diff'] < 0 ? '#ef4444' : '#6b7280') }};">
                        {{ $performanceComparison['avg_points_diff'] > 0 ? '+' : '' }}{{ number_format($performanceComparison['avg_points_diff'], 1) }}
                        <i class="fas fa-{{ $performanceComparison['avg_points_diff'] > 0 ? 'arrow-up' : ($performanceComparison['avg_points_diff'] < 0 ? 'arrow-down' : 'minus') }}"></i>
                    </div>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-bottom: 1rem; font-size: 0.875rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="display: inline-block; width: 12px; height: 12px; background: #10b981; border-radius: 50%;"></span>
                    <span>{{ $performanceComparison['improved_subjects'] }} improved</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="display: inline-block; width: 12px; height: 12px; background: #ef4444; border-radius: 50%;"></span>
                    <span>{{ $performanceComparison['declined_subjects'] }} declined</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="display: inline-block; width: 12px; height: 12px; background: #6b7280; border-radius: 50%;"></span>
                    <span>{{ $performanceComparison['stable_subjects'] }} stable</span>
                </div>
            </div>
            
            @if(count($performanceComparison['subjects']) > 0)
                <details style="margin-top: 1rem;">
                    <summary style="cursor: pointer; font-weight: 600; color: #374151; padding: 0.5rem 0;">
                        View Subject-by-Subject Comparison
                    </summary>
                    <div style="margin-top: 1rem; overflow-x: auto;">
                        <table style="width: 100%; font-size: 0.875rem;">
                            <thead>
                                <tr style="border-bottom: 2px solid #e5e7eb;">
                                    <th style="padding: 0.5rem; text-align: left;">Subject</th>
                                    <th style="padding: 0.5rem; text-align: center;">Previous</th>
                                    <th style="padding: 0.5rem; text-align: center;">Current</th>
                                    <th style="padding: 0.5rem; text-align: center;">Change</th>
                                    <th style="padding: 0.5rem; text-align: center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($performanceComparison['subjects'] as $subject => $data)
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                        <td style="padding: 0.5rem;">{{ $subject }}</td>
                                        <td style="padding: 0.5rem; text-align: center;">{{ $data['previous_grade'] }} ({{ $data['previous_points'] }})</td>
                                        <td style="padding: 0.5rem; text-align: center;">{{ $data['current_grade'] }} ({{ $data['current_points'] }})</td>
                                        <td style="padding: 0.5rem; text-align: center; color: {{ $data['points_diff'] > 0 ? '#10b981' : ($data['points_diff'] < 0 ? '#ef4444' : '#6b7280') }}; font-weight: 600;">
                                            {{ $data['points_diff'] > 0 ? '+' : '' }}{{ $data['points_diff'] }}
                                        </td>
                                        <td style="padding: 0.5rem; text-align: center;">
                                            <span style="display: inline-block; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600; background: {{ $data['status'] == 'improved' ? '#d1fae5' : ($data['status'] == 'declined' ? '#fee2e2' : '#f3f4f6') }}; color: {{ $data['status'] == 'improved' ? '#065f46' : ($data['status'] == 'declined' ? '#991b1b' : '#374151') }};">
                                                {{ ucfirst($data['status']) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>
            @endif
        </div>
    @endif

    @if($studentMarks->isEmpty())
        <!-- No Marks State -->
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h3>No Marks Found</h3>
            <p>To get personalized A-Level combination recommendations, you need to have your examination results uploaded.</p>
            <p class="mt-3">Please contact your teacher or school administrator to upload your marks (from any exam - beginning of term, mid-term, end of term, or UCE).</p>
            <a href="{{ route('student.marks.index') }}" class="btn btn-primary mt-4">
                <i class="fas fa-eye"></i> View My Marks
            </a>
        </div>
    @else
        <!-- Your Strong Subjects -->
        <div class="info-card">
            <h3><i class="fas fa-star"></i> Your Strong Subjects</h3>
            @if(count($strongSubjects) > 0)
                <div class="subject-badges">
                    @foreach($strongSubjects as $subject)
                        <span class="badge badge-success">{{ ucfirst($subject) }}</span>
                    @endforeach
                </div>
                <p class="mt-3 text-muted">
                    <i class="fas fa-info-circle"></i> 
                    These are subjects where you performed well in your exams (Division 1-2 or 70%+). Combinations featuring these subjects are recommended for you.
                </p>
            @else
                <p class="text-warning">
                    <i class="fas fa-exclamation-triangle"></i> 
                    No strong subjects identified. The recommendations below show all available combinations. Focus on improving your performance in subjects you're interested in.
                </p>
            @endif
        </div>

        <!-- Recommendations -->
        <div class="recommendations-section">
            <h2 class="section-title">
                <i class="fas fa-lightbulb"></i> Recommended A-Level Combinations
            </h2>
            
            @php
                $excellentMatches = collect($recommendations)->filter(fn($r) => $r['is_excellent_match']);
                $goodMatches = collect($recommendations)->filter(fn($r) => !$r['is_excellent_match'] && $r['is_good_match']);
                $possibleMatches = collect($recommendations)->filter(fn($r) => !$r['is_excellent_match'] && !$r['is_good_match'] && $r['is_possible_match']);
                $otherCombinations = collect($recommendations)->filter(fn($r) => !$r['is_possible_match']);
            @endphp

            <!-- Excellent Matches -->
            @if($excellentMatches->count() > 0)
                <div class="recommendation-group">
                    <div class="group-header excellent">
                        <i class="fas fa-check-circle"></i> Excellent Matches for You
                    </div>
                    <div class="combinations-grid">
                        @foreach($excellentMatches as $combo)
                            @include('student.o-level-recommendations.partials.combination-card', ['combo' => $combo, 'matchType' => 'excellent'])
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Good Matches -->
            @if($goodMatches->count() > 0)
                <div class="recommendation-group">
                    <div class="group-header good">
                        <i class="fas fa-thumbs-up"></i> Good Matches for You
                    </div>
                    <div class="combinations-grid">
                        @foreach($goodMatches as $combo)
                            @include('student.o-level-recommendations.partials.combination-card', ['combo' => $combo, 'matchType' => 'good'])
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Possible Matches -->
            @if($possibleMatches->count() > 0)
                <div class="recommendation-group">
                    <div class="group-header possible">
                        <i class="fas fa-question-circle"></i> Possible Combinations (May Require Extra Effort)
                    </div>
                    <div class="combinations-grid">
                        @foreach($possibleMatches as $combo)
                            @include('student.o-level-recommendations.partials.combination-card', ['combo' => $combo, 'matchType' => 'possible'])
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Other Combinations -->
            @if($otherCombinations->count() > 0 && count($strongSubjects) > 0)
                <div class="recommendation-group">
                    <div class="group-header other">
                        <i class="fas fa-list"></i> Other Available Combinations
                    </div>
                    <details>
                        <summary style="cursor: pointer; padding: 1rem; background: #f8f9fa; border-radius: 8px; margin-bottom: 1rem;">
                            <i class="fas fa-chevron-down"></i> View All Other Combinations ({{ $otherCombinations->count() }})
                        </summary>
                        <div class="combinations-grid">
                            @foreach($otherCombinations as $combo)
                                @include('student.o-level-recommendations.partials.combination-card', ['combo' => $combo, 'matchType' => 'other'])
                            @endforeach
                        </div>
                    </details>
                </div>
            @endif
        </div>

        <!-- Important Notes -->
        <div class="info-card mt-4">
            <h3><i class="fas fa-info-circle"></i> Important Notes</h3>
            <ul>
                <li><strong>Choose Wisely:</strong> Select a combination based on your interests, career goals, and strengths.</li>
                <li><strong>University Requirements:</strong> Different university programs require specific subject combinations. Check with your career counselor.</li>
                <li><strong>Future Career:</strong> Think about what you want to do after A-Level. Some careers require specific subjects.</li>
                <li><strong>Workload:</strong> Some combinations (like PCB/ICT) are more demanding. Be realistic about your capacity.</li>
                <li><strong>School Availability:</strong> Confirm that your school offers your chosen combination.</li>
                <li><strong>Seek Guidance:</strong> Discuss with your teachers, parents, and career counselors before making a final decision.</li>
            </ul>
        </div>
    @endif
</div>

<style>
.recommendations-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.header-content {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.header-icon {
    width: 80px;
    height: 80px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
}

.page-subtitle {
    margin: 0.5rem 0 0 0;
    opacity: 0.95;
    font-size: 1.1rem;
}

.alert {
    padding: 1rem 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-info {
    background: #e3f2fd;
    border: 1px solid #90caf9;
    color: #1976d2;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.empty-icon {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3.5rem;
    margin: 0 auto 1.5rem;
}

.info-card {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
}

.info-card h3 {
    color: #333;
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.subject-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

.badge-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.recommendations-section {
    margin-top: 2rem;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.recommendation-group {
    margin-bottom: 2.5rem;
}

.group-header {
    padding: 1rem 1.5rem;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.group-header.excellent {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
    border: 2px solid #28a745;
}

.group-header.good {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    color: #0c5460;
    border: 2px solid #17a2b8;
}

.group-header.possible {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    color: #856404;
    border: 2px solid #ffc107;
}

.group-header.other {
    background: linear-gradient(135deg, #e2e3e5 0%, #d6d8db 100%);
    color: #383d41;
    border: 2px solid #6c757d;
}

.combinations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 1.5rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.text-muted {
    color: #6c757d;
}

.text-warning {
    color: #856404;
}

.info-card ul {
    margin: 0;
    padding-left: 1.5rem;
}

.info-card li {
    margin-bottom: 0.75rem;
    line-height: 1.6;
}

@media (max-width: 768px) {
    .recommendations-container {
        padding: 1rem;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .combinations-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
