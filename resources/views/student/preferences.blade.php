@extends('layouts.student-dashboard')

@section('content')
<div class="dashboard-content-inner">
    <div class="preferences-card">
        @if(session('success'))
            <div class="dashboard-alert dashboard-alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="dashboard-alert dashboard-alert-error">{{ session('error') }}</div>
        @endif
        <h2 class="preferences-title">Set Your Learning Preferences</h2>
        <form method="POST" action="{{ route('student.preferences.update') }}" class="preferences-form">
            @csrf
            <div class="preferences-group">
                <label for="class_id">Select Class</label>
                <select name="class_id" id="class_id" class="form-control" required onchange="window.location.href='{{ route('student.preferences.index') }}?class_id=' + this.value">
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ (request('class_id') == $class->id || (isset($preference) && $preference->class_id == $class->id)) ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
                @error('class_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            @if($subscriptionPackage->subscription_type === 'term')
            <div class="preferences-group">
                <label for="term_id">Select Term</label>
                <select name="term_id" id="term_id" required>
                    <option value="">Select a term</option>
                    @if(isset($terms))
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" {{ old('term_id', $preference->term_id ?? '') == $term->id ? 'selected' : '' }}>
                                {{ $term->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('term_id')
                    <p class="preferences-error">{{ $message }}</p>
                @enderror
            </div>
            @endif

            @if($subscriptionPackage->subscription_type === 'subject' || $subscriptionPackage->subscription_type === 'topic')
            <div class="preferences-group">
                <label for="subject_id">Select Subject</label>
                <select name="subject_id" id="subject_id" class="form-control" required onchange="window.location.href='{{ route('student.preferences.index') }}?class_id={{ request('class_id', isset($preference) ? $preference->class_id : '') }}&subject_id=' + this.value">
                    <option value="">Select Subject</option>
                    @if(isset($subjects))
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ (request('subject_id') == $subject->id || (isset($preference) && $preference->subject_id == $subject->id)) ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('subject_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            @endif

            @if($subscriptionPackage->subscription_type === 'topic')
            <div class="preferences-group">
                <label for="topic_id">Select Topic</label>
                <select name="topic_id" id="topic_id" class="form-control" required>
                    <option value="">Select Topic</option>
                    @if(isset($topics))
                        @foreach($topics as $topic)
                            <option value="{{ $topic->id }}" {{ (request('topic_id') == $topic->id || (isset($preference) && $preference->topic_id == $topic->id)) ? 'selected' : '' }}>
                                {{ $topic->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('topic_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            @endif

            <div class="preferences-actions">
                <button type="submit" class="dashboard-btn dashboard-btn-primary">Save Preferences</button>
            </div>
        </form>
    </div>
</div>

<style>
.preferences-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    max-width: 480px;
    margin: 40px auto;
    padding: 32px 24px;
}
.preferences-title {
    font-size: 1.7rem;
    font-weight: bold;
    color: #22223b;
    margin-bottom: 28px;
    text-align: center;
}
.preferences-form {
    display: flex;
    flex-direction: column;
    gap: 22px;
}
.preferences-group {
    display: flex;
    flex-direction: column;
    gap: 7px;
}
.preferences-group label {
    font-size: 1rem;
    color: #374151;
    font-weight: 500;
}
.preferences-group select {
    padding: 10px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 1rem;
    background: #f9fafb;
    color: #22223b;
    transition: border 0.2s;
}
.preferences-group select:focus {
    border-color: #2563eb;
    outline: none;
}
.preferences-error {
    color: #e11d48;
    font-size: 0.95rem;
    margin-top: 2px;
}
.preferences-actions {
    display: flex;
    justify-content: center;
    margin-top: 10px;
}
@media (max-width: 600px) {
    .preferences-card {
        padding: 18px 6px;
        margin: 18px 0;
    }
    .preferences-title {
        font-size: 1.2rem;
    }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class_id');
    const subjectSelect = document.getElementById('subject_id');
    const subscriptionType = '{{ $subscriptionPackage->subscription_type }}';

    if (subscriptionType === 'subject' && classSelect && subjectSelect) {
        classSelect.addEventListener('change', function() {
            const classId = this.value;
            subjectSelect.innerHTML = '<option value="">Select a subject</option>';
            
            if (classId) {
                fetch(`/api/subjects?class_id=${classId}`)
                    .then(response => response.json())
                    .then(subjects => {
                        subjects.forEach(subject => {
                            const option = document.createElement('option');
                            option.value = subject.id;
                            option.textContent = subject.name;
                            subjectSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error loading subjects:', error));
            }
        });
    }
});
</script>
@endpush
@endsection 