@extends('layouts.dashboard')

@section('content')
<div style="max-width: 900px; margin: 0 auto; padding: 20px;">
    <!-- Header -->
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 32px; margin-bottom: 32px; box-shadow: 0 8px 32px rgba(102, 126, 234, 0.2);">
        <h1 style="color: white; font-size: 32px; font-weight: 700; margin: 0 0 12px 0;">✏️ Create Custom Grading Scale</h1>
        <p style="color: rgba(255,255,255,0.9); font-size: 16px; margin: 0;">Define your school's custom grading standards</p>
    </div>

    @if($errors->any())
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
            <strong>❌ Please fix the following errors:</strong>
            <ul style="margin: 8px 0 0 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 32px;">
        <form method="POST" action="{{ route('admin.grade-scales.store') }}">
            @csrf

            <!-- Academic Level Selection -->
            <div style="margin-bottom: 32px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Academic Level</label>
                <select name="academic_level" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px;">
                    <option value="O-Level" {{ request('level') === 'O-Level' ? 'selected' : '' }}>O-Level (UCE - Form 1-4)</option>
                    <option value="A-Level" {{ request('level') === 'A-Level' ? 'selected' : '' }}>A-Level (UACE)</option>
                </select>
            </div>

            <!-- Reference Default Scale -->
            <div style="background: #e7f3ff; border: 1px solid #b3d9ff; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
                <strong>📋 Default O-Level Scale Reference:</strong>
                <table style="width: 100%; margin-top: 12px; font-size: 14px;">
                    <tr>
                        <td><strong>A:</strong> 80-100% = 6 points</td>
                        <td><strong>B:</strong> 70-79% = 5 points</td>
                    </tr>
                    <tr>
                        <td><strong>C:</strong> 60-69% = 4 points</td>
                        <td><strong>D:</strong> 50-59% = 3 points</td>
                    </tr>
                    <tr>
                        <td><strong>E:</strong> 40-49% = 2 points</td>
                        <td><strong>O:</strong> 35-39% = 1 point</td>
                    </tr>
                    <tr>
                        <td><strong>F:</strong> 0-34% = 0 points</td>
                        <td></td>
                    </tr>
                </table>
            </div>

            <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 16px; color: #667eea;">Define Custom Grade Ranges</h3>

            <!-- Grade A -->
            <div style="border: 2px solid #667eea; border-radius: 12px; padding: 20px; margin-bottom: 16px;">
                <h4 style="margin: 0 0 16px 0; color: #667eea; font-size: 18px; font-weight: 600;">Grade A</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 14px; margin-bottom: 4px; color: #666;">Min %</label>
                        <input type="number" name="grades[0][min_percentage]" value="{{ old('grades.0.min_percentage', 80) }}" required step="0.01" min="0" max="100" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; margin-bottom: 4px; color: #666;">Max %</label>
                        <input type="number" name="grades[0][max_percentage]" value="{{ old('grades.0.max_percentage', 100) }}" required step="0.01" min="0" max="100" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; margin-bottom: 4px; color: #666;">Points</label>
                        <input type="number" name="grades[0][points]" value="{{ old('grades.0.points', 6) }}" required min="0" max="10" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                </div>
                <input type="hidden" name="grades[0][grade]" value="A">
            </div>

            <!-- Grade B -->
            <div style="border: 2px solid #28a745; border-radius: 12px; padding: 20px; margin-bottom: 16px;">
                <h4 style="margin: 0 0 16px 0; color: #28a745; font-size: 18px; font-weight: 600;">Grade B</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 14px; margin-bottom: 4px; color: #666;">Min %</label>
                        <input type="number" name="grades[1][min_percentage]" value="{{ old('grades.1.min_percentage', 70) }}" required step="0.01" min="0" max="100" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; margin-bottom: 4px; color: #666;">Max %</label>
                        <input type="number" name="grades[1][max_percentage]" value="{{ old('grades.1.max_percentage', 79.99) }}" required step="0.01" min="0" max="100" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; margin-bottom: 4px; color: #666;">Points</label>
                        <input type="number" name="grades[1][points]" value="{{ old('grades.1.points', 5) }}" required min="0" max="10" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                </div>
                <input type="hidden" name="grades[1][grade]" value="B">
            </div>

            <!-- Grade C -->
            <div style="border: 2px solid #17a2b8; border-radius: 12px; padding: 20px; margin-bottom: 16px;">
                <h4 style="margin: 0 0 16px 0; color: #17a2b8; font-size: 18px; font-weight: 600;">Grade C</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 14px; margin-bottom: 4px; color: #666;">Min %</label>
                        <input type="number" name="grades[2][min_percentage]" value="{{ old('grades.2.min_percentage', 60) }}" required step="0.01" min="0" max="100" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; margin-bottom: 4px; color: #666;">Max %</label>
                        <input type="number" name="grades[2][max_percentage]" value="{{ old('grades.2.max_percentage', 69.99) }}" required step="0.01" min="0" max="100" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; margin-bottom: 4px; color: #666;">Points</label>
                        <input type="number" name="grades[2][points]" value="{{ old('grades.2.points', 4) }}" required min="0" max="10" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                </div>
                <input type="hidden" name="grades[2][grade]" value="C">
            </div>

            <!-- Grade D -->
            <div style="border: 2px solid #ffc107; border-radius: 12px; padding: 20px; margin-bottom: 16px;">
                <h4 style="margin: 0 0 16px 0; color: #ffc107; font-size: 18px; font-weight: 600;">Grade D</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 14px; margin-bottom: 4px; color: #666;">Min %</label>
                        <input type="number" name="grades[3][min_percentage]" value="{{ old('grades.3.min_percentage', 50) }}" required step="0.01" min="0" max="100" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; margin-bottom: 4px; color: #666;">Max %</label>
                        <input type="number" name="grades[3][max_percentage]" value="{{ old('grades.3.max_percentage', 59.99) }}" required step="0.01" min="0" max="100" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; margin-bottom: 4px; color: #666;">Points</label>
                        <input type="number" name="grades[3][points]" value="{{ old('grades.3.points', 3) }}" required min="0" max="10" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                </div>
                <input type="hidden" name="grades[3][grade]" value="D">
            </div>

            <!-- Grade E -->
            <div style="border: 2px solid #fd7e14; border-radius: 12px; padding: 20px; margin-bottom: 16px;">
                <h4 style="margin: 0 0 16px 0; color: #fd7e14; font-size: 18px; font-weight: 600;">Grade E</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 14px; margin-bottom: 4px; color: #666;">Min %</label>
                        <input type="number" name="grades[4][min_percentage]" value="{{ old('grades.4.min_percentage', 40) }}" required step="0.01" min="0" max="100" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; margin-bottom: 4px; color: #666;">Max %</label>
                        <input type="number" name="grades[4][max_percentage]" value="{{ old('grades.4.max_percentage', 49.99) }}" required step="0.01" min="0" max="100" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; margin-bottom: 4px; color: #666;">Points</label>
                        <input type="number" name="grades[4][points]" value="{{ old('grades.4.points', 2) }}" required min="0" max="10" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                </div>
                <input type="hidden" name="grades[4][grade]" value="E">
            </div>

            <!-- Grade O -->
            <div style="border: 2px solid #dc3545; border-radius: 12px; padding: 20px; margin-bottom: 16px;">
                <h4 style="margin: 0 0 16px 0; color: #dc3545; font-size: 18px; font-weight: 600;">Grade O</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 14px; margin-bottom: 4px; color: #666;">Min %</label>
                        <input type="number" name="grades[5][min_percentage]" value="{{ old('grades.5.min_percentage', 35) }}" required step="0.01" min="0" max="100" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; margin-bottom: 4px; color: #666;">Max %</label>
                        <input type="number" name="grades[5][max_percentage]" value="{{ old('grades.5.max_percentage', 39.99) }}" required step="0.01" min="0" max="100" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; margin-bottom: 4px; color: #666;">Points</label>
                        <input type="number" name="grades[5][points]" value="{{ old('grades.5.points', 1) }}" required min="0" max="10" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                </div>
                <input type="hidden" name="grades[5][grade]" value="O">
            </div>

            <!-- Grade F -->
            <div style="border: 2px solid #6c757d; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                <h4 style="margin: 0 0 16px 0; color: #6c757d; font-size: 18px; font-weight: 600;">Grade F</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 14px; margin-bottom: 4px; color: #666;">Min %</label>
                        <input type="number" name="grades[6][min_percentage]" value="{{ old('grades.6.min_percentage', 0) }}" required step="0.01" min="0" max="100" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; margin-bottom: 4px; color: #666;">Max %</label>
                        <input type="number" name="grades[6][max_percentage]" value="{{ old('grades.6.max_percentage', 34.99) }}" required step="0.01" min="0" max="100" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; margin-bottom: 4px; color: #666;">Points</label>
                        <input type="number" name="grades[6][points]" value="{{ old('grades.6.points', 0) }}" required min="0" max="10" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                </div>
                <input type="hidden" name="grades[6][grade]" value="F">
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 16px; justify-content: flex-end;">
                <a href="{{ route('admin.grade-scales.index') }}" style="background: #6c757d; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    Cancel
                </a>
                <button type="submit" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 32px; border: none; border-radius: 8px; font-weight: 600; font-size: 16px; cursor: pointer;">
                    💾 Save Custom Scale
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
