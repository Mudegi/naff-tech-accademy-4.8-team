@extends('frontend.layouts.app')

@section('title', 'New Message')

@section('styles')
<style>
    body { background: #f5f7fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
    .message-container { max-width: 800px; margin: 0 auto; padding: 2rem 1rem; }
    .page-header { margin-bottom: 2rem; }
    .page-title { font-size: 1.875rem; font-weight: 700; color: #1a202c; margin: 0 0 0.5rem 0; }
    .page-subtitle { color: #718096; margin: 0; }
    .back-link { color: #6366f1; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; }
    .back-link:hover { color: #4f46e5; }
    
    .form-card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 2rem; }
    .form-group { margin-bottom: 1.5rem; }
    .form-label { display: block; font-weight: 600; color: #2d3748; margin-bottom: 0.5rem; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-control { width: 100%; padding: 0.75rem 1rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem; transition: all 0.2s; }
    .form-control:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
    .form-select { width: 100%; padding: 0.75rem 1rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem; background: white; cursor: pointer; }
    .form-textarea { min-height: 200px; resize: vertical; font-family: inherit; }
    .form-help { font-size: 0.875rem; color: #718096; margin-top: 0.25rem; }
    .error-message { color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem; }
    
    .form-actions { display: flex; gap: 1rem; margin-top: 2rem; }
    .btn-primary { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; padding: 0.875rem 2rem; border: none; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: all 0.2s; }
    .btn-primary:hover { background: linear-gradient(135deg, #4f46e5, #7c3aed); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(99,102,241,0.4); }
    .btn-secondary { background: white; color: #6366f1; padding: 0.875rem 2rem; border: 2px solid #6366f1; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; text-decoration: none; transition: all 0.2s; }
    .btn-secondary:hover { background: #6366f1; color: white; }
    
    .teacher-loading { padding: 1rem; text-align: center; color: #718096; display: none; }
</style>
@endsection

@section('content')
<div class="message-container">
    <a href="{{ route('parent.messages.index') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Messages
    </a>
    
    <div class="page-header">
        <h1 class="page-title">New Message</h1>
        <p class="page-subtitle">Send a message to your child's teacher</p>
    </div>

    @if($errors->any())
        <div style="background: #fee2e2; border-left: 4px solid #dc2626; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; color: #991b1b;">
            <strong><i class="fas fa-exclamation-circle"></i> Please fix the following errors:</strong>
            <ul style="margin: 0.5rem 0 0 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-card">
        <form method="POST" action="{{ route('parent.messages.store') }}" id="messageForm">
            @csrf
            
            <div class="form-group">
                <label class="form-label" for="student_id">Select Your Child</label>
                <select name="student_id" id="student_id" class="form-select" required>
                    <option value="">-- Choose a child --</option>
                    @foreach($children as $child)
                        <option value="{{ $child->id }}" {{ old('student_id', $selectedStudentId) == $child->id ? 'selected' : '' }}>
                            {{ $child->name }}
                        </option>
                    @endforeach
                </select>
                <p class="form-help">Select which child this message is regarding</p>
            </div>

            <div class="form-group">
                <label class="form-label" for="teacher_id">Select Teacher</label>
                <div class="teacher-loading" id="teacherLoading">
                    <i class="fas fa-spinner fa-spin"></i> Loading teachers...
                </div>
                <select name="teacher_id" id="teacher_id" class="form-select" required>
                    <option value="">-- First select a child --</option>
                    @if($teachers && count($teachers) > 0)
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }}
                                @if($teacher->subjects) - {{ $teacher->subjects }}@endif
                                @if($teacher->classes) ({{ $teacher->classes }})@endif
                                @if($teacher->phone_number) | 📞 {{ $teacher->phone_number }}@endif
                            </option>
                        @endforeach
                    @endif
                </select>
                <p class="form-help">Select the teacher you want to contact</p>
            </div>

            <div class="form-group">
                <label class="form-label" for="message">Your Message</label>
                <textarea name="message" id="message" class="form-control form-textarea" required>{{ old('message') }}</textarea>
                <p class="form-help">Minimum 10 characters. Be clear and specific about your concerns or questions.</p>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
                <a href="{{ route('parent.messages.index') }}" class="btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const studentSelect = document.getElementById('student_id');
    const teacherSelect = document.getElementById('teacher_id');
    const teacherLoading = document.getElementById('teacherLoading');
    
    // Trigger on page load if student is pre-selected
    if (studentSelect.value) {
        loadTeachers(studentSelect.value);
    }
    
    studentSelect.addEventListener('change', function() {
        const studentId = this.value;
        
        if (!studentId) {
            teacherSelect.innerHTML = '<option value="">-- First select a child --</option>';
            teacherSelect.style.display = 'block';
            teacherLoading.style.display = 'none';
            return;
        }
        
        loadTeachers(studentId);
    });
    
    function loadTeachers(studentId) {
        console.log('Loading teachers for student:', studentId);
        
        // Show loading
        teacherLoading.style.display = 'block';
        teacherSelect.style.display = 'none';
        
        // Fetch teachers for this student
        fetch(`/parent/messages/teachers/${studentId}`)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received data:', data);
                
                teacherSelect.innerHTML = '<option value="">-- Choose a teacher --</option>';
                
                if (data.teachers && data.teachers.length > 0) {
                    data.teachers.forEach(teacher => {
                        const option = document.createElement('option');
                        option.value = teacher.id;
                        
                        // Build display text with subject and contact information
                        let displayText = teacher.name;
                        if (teacher.subjects) {
                            displayText += ` - ${teacher.subjects}`;
                        }
                        if (teacher.classes) {
                            displayText += ` (${teacher.classes})`;
                        }
                        if (teacher.phone_number) {
                            displayText += ` | 📞 ${teacher.phone_number}`;
                        }
                        
                        option.textContent = displayText;
                        teacherSelect.appendChild(option);
                    });
                    console.log(`Loaded ${data.teachers.length} teachers`);
                } else {
                    const message = data.message || 'No teachers found for this student';
                    teacherSelect.innerHTML = `<option value="">${message}</option>`;
                    console.log('No teachers found:', data);
                }
                
                // Hide loading
                teacherLoading.style.display = 'none';
                teacherSelect.style.display = 'block';
            })
            .catch(error => {
                console.error('Error fetching teachers:', error);
                teacherSelect.innerHTML = '<option value="">Error loading teachers - check console</option>';
                teacherLoading.style.display = 'none';
                teacherSelect.style.display = 'block';
            });
    }
});
</script>
@endsection
