@extends('layouts.app')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <!-- Header -->
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 32px; margin-bottom: 32px; box-shadow: 0 8px 32px rgba(102, 126, 234, 0.2);">
        <h1 style="color: white; font-size: 32px; font-weight: 700; margin: 0 0 12px 0;">🎓 O-Level Course Recommendations</h1>
        <p style="color: rgba(255,255,255,0.9); font-size: 16px; margin: 0;">Select at least 3 subjects to see which university courses you qualify for</p>
    </div>

    @if(session('error'))
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
            ❌ {{ session('error') }}
        </div>
    @endif

    <!-- Instructions Card -->
    <div style="background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 12px; padding: 24px; margin-bottom: 32px;">
        <h3 style="margin: 0 0 12px 0; color: #0066cc; font-size: 18px; font-weight: 600;">📋 How It Works</h3>
        <ol style="margin: 0; padding-left: 24px; line-height: 1.8;">
            <li><strong>Select at least 3 subjects</strong> from your O-Level marks below</li>
            <li>We'll <strong>calculate your aggregate points</strong> using your school's grading scale</li>
            <li>See <strong>qualifying university courses</strong> based on cut-off points and subject requirements</li>
        </ol>
    </div>

    <!-- Grading Scale Reference -->
    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 24px; margin-bottom: 32px;">
        <h3 style="margin: 0 0 16px 0; color: #333; font-size: 20px; font-weight: 600;">📊 Current Grading Scale (O-Level)</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px;">
            @foreach($gradeScale as $scale)
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px; border-radius: 8px; text-align: center;">
                    <div style="font-size: 24px; font-weight: 700;">{{ $scale->grade }}</div>
                    <div style="font-size: 12px; opacity: 0.9;">{{ $scale->min_percentage }}-{{ $scale->max_percentage }}%</div>
                    <div style="font-size: 14px; font-weight: 600; margin-top: 4px;">{{ $scale->points }} points</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Subject Selection Form -->
    <form method="POST" action="{{ route('student.o-level-recommendations.show') }}" id="recommendationForm">
        @csrf
        
        <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 32px; margin-bottom: 24px;">
            <h3 style="margin: 0 0 20px 0; color: #333; font-size: 22px; font-weight: 600;">Select Your Subjects (Minimum 3)</h3>
            
            <div id="subjectsList" style="display: grid; gap: 12px;">
                @foreach($studentMarks as $mark)
                    <label style="background: #f8f9fa; border: 2px solid #dee2e6; border-radius: 10px; padding: 16px; cursor: pointer; display: flex; align-items: center; transition: all 0.3s ease;" class="subject-card">
                        <input type="checkbox" name="selected_subjects[]" value="{{ $mark->id }}" style="width: 20px; height: 20px; margin-right: 16px; cursor: pointer;" class="subject-checkbox">
                        
                        <div style="flex: 1;">
                            <div style="font-weight: 600; font-size: 16px; color: #333; margin-bottom: 4px;">{{ $mark->subject_name }}</div>
                            <div style="font-size: 14px; color: #666;">
                                @if($mark->numeric_mark)
                                    <span style="background: #667eea; color: white; padding: 2px 8px; border-radius: 4px; font-weight: 600;">
                                        {{ $mark->numeric_mark }}%
                                    </span>
                                    @php
                                        $gradeData = \App\Models\GradeScale::getGradeAndPoints($mark->numeric_mark, 'O-Level', Auth::user()->school_id);
                                    @endphp
                                    <span style="margin-left: 8px;">Grade: <strong>{{ $gradeData['grade'] }}</strong></span>
                                    <span style="margin-left: 8px;">Points: <strong>{{ $gradeData['points'] }}</strong></span>
                                @else
                                    <span>Grade: <strong>{{ $mark->grade }}</strong></span>
                                    @if($mark->points)
                                        <span style="margin-left: 8px;">Points: <strong>{{ $mark->points }}</strong></span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <div class="check-indicator" style="width: 24px; height: 24px; border-radius: 50%; border: 2px solid #667eea; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">
                            <span style="font-size: 16px; display: none;">✓</span>
                        </div>
                    </label>
                @endforeach
            </div>

            <!-- Selected Count Display -->
            <div style="margin-top: 24px; padding: 16px; background: #f8f9fa; border-radius: 8px; text-align: center;">
                <span style="font-size: 18px; font-weight: 600; color: #667eea;">
                    Selected: <span id="selectedCount">0</span> subject(s)
                </span>
                <span id="warningText" style="display: block; margin-top: 8px; color: #dc3545; font-size: 14px;">Please select at least 3 subjects</span>
                <span id="aggregatePreview" style="display: none; margin-top: 8px; color: #28a745; font-size: 16px; font-weight: 600;"></span>
            </div>
        </div>

        <!-- Submit Button -->
        <div style="text-align: center;">
            <button type="submit" id="submitBtn" disabled style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 16px 48px; border: none; border-radius: 12px; font-size: 18px; font-weight: 600; cursor: not-allowed; opacity: 0.5; transition: all 0.3s ease;">
                🔍 Find Qualifying Courses
            </button>
        </div>
    </form>
</div>

<style>
    .subject-card:hover {
        border-color: #667eea !important;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2) !important;
    }

    .subject-checkbox:checked + div ~ .check-indicator {
        background: #667eea;
        border-color: #667eea;
    }

    .subject-checkbox:checked + div ~ .check-indicator span {
        display: block !important;
        color: white;
    }

    .subject-checkbox:checked {
        accent-color: #667eea;
    }

    input[type="checkbox"]:checked ~ * {
        background: #e7f3ff !important;
        border-color: #667eea !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.subject-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    const warningText = document.getElementById('warningText');
    const aggregatePreview = document.getElementById('aggregatePreview');
    const submitBtn = document.getElementById('submitBtn');

    function updateSelectionCount() {
        const checked = Array.from(checkboxes).filter(cb => cb.checked);
        const count = checked.length;
        
        selectedCount.textContent = count;

        if (count >= 3) {
            warningText.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.style.cursor = 'pointer';
            submitBtn.style.opacity = '1';

            // Calculate aggregate preview
            let totalPoints = 0;
            checked.forEach(cb => {
                const card = cb.closest('.subject-card');
                const pointsText = card.textContent.match(/Points:\s*(\d+)/);
                if (pointsText) {
                    totalPoints += parseInt(pointsText[1]);
                }
            });

            aggregatePreview.textContent = `Aggregate Points: ${totalPoints}`;
            aggregatePreview.style.display = 'block';
        } else {
            warningText.style.display = 'block';
            aggregatePreview.style.display = 'none';
            submitBtn.disabled = true;
            submitBtn.style.cursor = 'not-allowed';
            submitBtn.style.opacity = '0.5';
        }
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateSelectionCount);
    });

    // Initial count
    updateSelectionCount();
});
</script>
@endsection
