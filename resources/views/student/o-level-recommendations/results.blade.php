@extends('layouts.app')

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 20px;">
    <!-- Header -->
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 32px; margin-bottom: 32px; box-shadow: 0 8px 32px rgba(102, 126, 234, 0.2);">
        <h1 style="color: white; font-size: 32px; font-weight: 700; margin: 0 0 12px 0;">🎓 Your Course Recommendations</h1>
        <p style="color: rgba(255,255,255,0.9); font-size: 16px; margin: 0;">Based on your O-Level marks and aggregate points</p>
    </div>

    <!-- Important Notice -->
    <div style="background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 8px; padding: 20px; margin-bottom: 32px;">
        <div style="display: flex; gap: 12px; align-items: start;">
            <div style="font-size: 24px;">ℹ️</div>
            <div>
                <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #856404;">Important Notice</h4>
                <p style="margin: 0; font-size: 14px; color: #856404; line-height: 1.6;">
                    These recommendations are based on <strong>aggregate points matching university cut-off points</strong>. 
                    While some courses show subject requirements as guidance, most universities publish only cut-off points. 
                    <strong>Always verify specific subject requirements with the university</strong> before applying, as they may have additional criteria.
                </p>
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 32px; margin-bottom: 32px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px;">
            <!-- Aggregate Points -->
            <div style="text-align: center;">
                <div style="font-size: 48px; font-weight: 700; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    {{ $aggregatePoints }}
                </div>
                <div style="color: #666; font-size: 14px; font-weight: 600; text-transform: uppercase; margin-top: 8px;">Aggregate Points</div>
            </div>

            <!-- Qualifying Courses -->
            <div style="text-align: center;">
                <div style="font-size: 48px; font-weight: 700; color: #28a745;">{{ $qualifyingCourses->count() }}</div>
                <div style="color: #666; font-size: 14px; font-weight: 600; text-transform: uppercase; margin-top: 8px;">Qualifying Courses</div>
            </div>

            <!-- Universities -->
            <div style="text-align: center;">
                <div style="font-size: 48px; font-weight: 700; color: #17a2b8;">{{ $groupedByUniversity->count() }}</div>
                <div style="color: #666; font-size: 14px; font-weight: 600; text-transform: uppercase; margin-top: 8px;">Universities</div>
            </div>

            <!-- Academic Year -->
            <div style="text-align: center;">
                <div style="font-size: 48px; font-weight: 700; color: #ffc107;">{{ $currentYear }}</div>
                <div style="color: #666; font-size: 14px; font-weight: 600; text-transform: uppercase; margin-top: 8px;">Academic Year</div>
            </div>
        </div>
    </div>

    <!-- Selected Subjects Summary -->
    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 32px; margin-bottom: 32px;">
        <h3 style="margin: 0 0 20px 0; font-size: 20px; font-weight: 600; color: #333;">📚 Your Selected Subjects</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
            @foreach($marksWithPoints as $mark)
                <div style="background: linear-gradient(135deg, #e7f3ff 0%, #f0e7ff 100%); border-radius: 10px; padding: 16px;">
                    <div style="font-weight: 600; font-size: 16px; color: #333; margin-bottom: 8px;">{{ $mark['subject_name'] }}</div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="background: #667eea; color: white; padding: 4px 12px; border-radius: 6px; font-weight: 600; font-size: 14px;">
                            Grade {{ $mark['grade'] }}
                        </span>
                        @if($mark['numeric_mark'])
                            <span style="color: #666; font-size: 14px;">{{ $mark['numeric_mark'] }}%</span>
                        @endif
                        <span style="background: #28a745; color: white; padding: 4px 12px; border-radius: 6px; font-weight: 600; font-size: 14px;">
                            {{ $mark['points'] }} pts
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Action Buttons -->
        <div style="margin-top: 24px; display: flex; gap: 16px; flex-wrap: wrap;">
            <a href="{{ route('student.o-level-course-recommendations.select') }}" style="background: #6c757d; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                ← Select Different Subjects
            </a>
            <form method="POST" action="{{ route('student.o-level-recommendations.download-pdf') }}" style="display: inline;">
                @csrf
                @foreach(request()->input('selected_subjects', []) as $subjectId)
                    <input type="hidden" name="selected_subjects[]" value="{{ $subjectId }}">
                @endforeach
                <button type="submit" style="background: #dc3545; color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    📄 Download PDF Report
                </button>
            </form>
        </div>
    </div>

    <!-- Results Section -->
    @if($qualifyingCourses->isEmpty())
        <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 12px; padding: 32px; text-align: center;">
            <div style="font-size: 48px; margin-bottom: 16px;">😔</div>
            <h3 style="margin: 0 0 12px 0; font-size: 24px; font-weight: 600; color: #856404;">No Qualifying Courses Found</h3>
            <p style="color: #856404; font-size: 16px; line-height: 1.6; margin: 0;">
                Based on your aggregate of <strong>{{ $aggregatePoints }} points</strong>, we couldn't find courses that match your current performance.<br>
                Try improving your grades or selecting higher-scoring subjects to increase your options.
            </p>
        </div>
    @else
        <!-- Courses by University -->
        @foreach($groupedByUniversity as $universityName => $courses)
            <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 32px; overflow: hidden;">
                <!-- University Header -->
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 24px;">
                    <h2 style="color: white; font-size: 24px; font-weight: 600; margin: 0 0 8px 0;">🏛️ {{ $universityName }}</h2>
                    <p style="color: rgba(255,255,255,0.9); font-size: 14px; margin: 0;">{{ $courses->count() }} course(s) available</p>
                </div>

                <!-- Courses List -->
                <div style="padding: 24px;">
                    <div style="display: grid; gap: 16px;">
                        @foreach($courses as $course)
                            <div style="border: 2px solid #e9ecef; border-radius: 10px; padding: 20px; transition: all 0.3s ease; position: relative;" onmouseover="this.style.borderColor='#667eea'; this.style.boxShadow='0 4px 12px rgba(102,126,234,0.2)'" onmouseout="this.style.borderColor='#e9ecef'; this.style.boxShadow='none'">
                                <!-- Course Name -->
                                <h3 style="margin: 0 0 12px 0; font-size: 20px; font-weight: 600; color: #333;">{{ $course->course_name }}</h3>

                                <!-- Course Details Grid -->
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px;">
                                    <!-- Cut-off Points -->
                                    <div>
                                        <div style="font-size: 12px; color: #666; text-transform: uppercase; margin-bottom: 4px;">Cut-off Points</div>
                                        <div style="font-size: 18px; font-weight: 700; color: #667eea;">{{ $course->effective_cut_off }}</div>
                                    </div>

                                    <!-- Your Points -->
                                    <div>
                                        <div style="font-size: 12px; color: #666; text-transform: uppercase; margin-bottom: 4px;">Your Points</div>
                                        <div style="font-size: 18px; font-weight: 700; color: #28a745;">{{ $aggregatePoints }}</div>
                                    </div>

                                    <!-- Points Above Cut-off -->
                                    <div>
                                        <div style="font-size: 12px; color: #666; text-transform: uppercase; margin-bottom: 4px;">Points Above</div>
                                        <div style="font-size: 18px; font-weight: 700; color: #17a2b8;">+{{ $course->points_difference }}</div>
                                    </div>

                                    <!-- Match Score -->
                                    <div>
                                        <div style="font-size: 12px; color: #666; text-transform: uppercase; margin-bottom: 4px;">Match Score</div>
                                        <div style="font-size: 18px; font-weight: 700; color: #ffc107;">{{ $course->match_score }}</div>
                                    </div>
                                </div>

                                <!-- Subject Requirements (Optional Guidance) -->
                                @if($course->essential_subjects || $course->relevant_subjects)
                                    <div style="background: #f0f9ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 16px; margin-top: 12px;">
                                        <div style="font-size: 12px; color: #1e40af; font-weight: 600; margin-bottom: 8px;">
                                            📋 Subject Guidance (verify with university)
                                        </div>
                                        @if($course->essential_subjects && count($course->essential_subjects) > 0)
                                            <div style="margin-bottom: 12px;">
                                                <strong style="color: #dc3545;">Suggested Essential:</strong>
                                                <div style="margin-top: 6px;">
                                                    @foreach($course->essential_subjects as $subject)
                                                        <span style="background: {{ $course->meets_essential_subjects ? '#28a745' : '#dc3545' }}; color: white; padding: 4px 10px; border-radius: 6px; font-size: 13px; margin-right: 6px; margin-bottom: 6px; display: inline-block;">
                                                            {{ $course->meets_essential_subjects ? '✓' : '✗' }} {{ $subject }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @if($course->relevant_subjects && count($course->relevant_subjects) > 0)
                                            <div>
                                                <strong style="color: #17a2b8;">Relevant Subjects:</strong>
                                                <div style="margin-top: 6px;">
                                                    @foreach($course->relevant_subjects as $subject)
                                                        <span style="background: #17a2b8; color: white; padding: 4px 10px; border-radius: 6px; font-size: 13px; margin-right: 6px; margin-bottom: 6px; display: inline-block;">{{ $subject }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px; margin-top: 12px; font-size: 13px; color: #15803d;">
                                        ✓ You qualify based on aggregate points. Specific subject requirements not specified by university.
                                    </div>
                                @endif

                                <!-- Course Description -->
                                @if($course->description)
                                    <div style="margin-top: 12px; color: #666; font-size: 14px; line-height: 1.6;">
                                        {{ $course->description }}
                                    </div>
                                @endif

                                <!-- Match Indicator Badge -->
                                @if($course->match_score >= 20)
                                    <div style="position: absolute; top: 20px; right: 20px; background: #28a745; color: white; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                                        🌟 Excellent Match
                                    </div>
                                @elseif($course->match_score >= 10)
                                    <div style="position: absolute; top: 20px; right: 20px; background: #17a2b8; color: white; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                                        ⭐ Good Match
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
