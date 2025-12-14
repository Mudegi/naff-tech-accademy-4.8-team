@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs" style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="dashboard-title">Edit Topic</h1>
        <a href="{{ route('admin.topics.index') }}" class="dashboard-btn dashboard-btn-secondary">Back to Topics</a>
    </div>
    <div class="profile-card" style="max-width:600px;margin:0 auto;">
        <form method="POST" action="{{ route('admin.topics.update', $topic->hash_id) }}" id="topicForm">
            @csrf
            @method('PUT')
            <div class="profile-form-group">
                <label for="subject_id">Subject</label>
                <select id="subject_id" name="subject_id" class="profile-input" required>
                    <option value="">Select Subject</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id', $topic->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                    @endforeach
                </select>
                @error('subject_id')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
            </div>
            <div class="profile-form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $topic->name) }}" class="profile-input" required>
                @error('name')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
            </div>
            <div class="profile-form-group">
                <label for="slug">Slug</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $topic->slug) }}" class="profile-input">
                @error('slug')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
            </div>
            <div class="profile-form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="profile-input" rows="3">{{ old('description', $topic->description) }}</textarea>
                @error('description')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
            </div>
            <div class="profile-form-group">
                <label for="order">Order</label>
                <input type="number" id="order" name="order" value="{{ old('order', $topic->order) }}" class="profile-input">
                @error('order')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
            </div>
            <div class="profile-form-group">
                <label for="is_active">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $topic->is_active) ? 'checked' : '' }}> Active
                </label>
            </div>
            <button type="submit" class="dashboard-btn dashboard-btn-primary">Update Topic</button>
        </form>
        <div id="errorDisplay" class="dashboard-alert" style="display: none; margin-top: 20px;"></div>
        <div id="successDisplay" class="dashboard-alert dashboard-alert-success" style="display: none; margin-top: 20px;"></div>
    </div>
</div>
<script>
const nameInput = document.getElementById('name');
const slugInput = document.getElementById('slug');
if (nameInput && slugInput) {
    nameInput.addEventListener('input', function() {
        let slug = nameInput.value
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
        slugInput.value = slug;
    });
}
const topicForm = document.getElementById('topicForm');
topicForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    document.getElementById('errorDisplay').style.display = 'none';
    document.getElementById('successDisplay').style.display = 'none';
    submitButton.disabled = true;
    submitButton.textContent = 'Updating...';
    fetch(form.action, {
        method: form.method,
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                try {
                    const data = JSON.parse(text);
                    throw new Error(data.message || 'Server error occurred');
                } catch (e) {
                    throw new Error(text || 'Server error occurred');
                }
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showSuccess(data.message || 'Topic updated successfully!');
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1200);
        } else {
            if (data.errors) {
                let errorMessage = '';
                Object.keys(data.errors).forEach(key => {
                    errorMessage += `${key}: ${data.errors[key][0]}\n`;
                });
                showError(errorMessage);
            } else {
                showError(data.message || 'An error occurred while updating the topic.');
            }
            submitButton.disabled = false;
            submitButton.textContent = 'Update Topic';
        }
    })
    .catch(error => {
        showError(error.message || 'An error occurred while updating the topic. Please try again.');
        submitButton.disabled = false;
        submitButton.textContent = 'Update Topic';
    });
});
function showError(message) {
    const errorDisplay = document.getElementById('errorDisplay');
    errorDisplay.textContent = message;
    errorDisplay.style.display = 'block';
    errorDisplay.className = 'dashboard-alert dashboard-alert-error';
    setTimeout(() => {
        errorDisplay.style.display = 'none';
    }, 5000);
}
function showSuccess(message) {
    const successDisplay = document.getElementById('successDisplay');
    successDisplay.textContent = message;
    successDisplay.style.display = 'block';
    setTimeout(() => {
        successDisplay.style.display = 'none';
    }, 5000);
}
</script>
@endsection 