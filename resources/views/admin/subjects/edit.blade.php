@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs" style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="dashboard-title">Edit Subject</h1>
        <a href="{{ route('admin.subjects.index') }}" class="dashboard-btn dashboard-btn-secondary">Back to Subjects</a>
    </div>
    <div class="profile-card" style="max-width:900px;margin:0 auto;">
        @php
            $paperList = old('papers', $subject->papers ?? []);
            if (empty($paperList)) {
                $paperList = [
                    ['name' => '', 'code' => '', 'description' => ''],
                ];
            }
            $nextPaperIndex = count($paperList);
        @endphp
        <form method="POST" action="{{ route('admin.subjects.update', $subject->hash_id) }}" id="subjectForm" data-next-paper-index="{{ $nextPaperIndex }}">
            @csrf
            @method('PUT')
            <div class="profile-row">
                <div class="profile-col profile-col-details">
                    <div class="profile-form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $subject->name) }}" class="profile-input" required>
                        @error('name')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="profile-form-group">
                        <label for="slug">Slug</label>
                        <input type="text" id="slug" name="slug" value="{{ old('slug', $subject->slug) }}" class="profile-input">
                        @error('slug')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="profile-form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="profile-input" rows="3">{{ old('description', $subject->description) }}</textarea>
                        @error('description')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="profile-form-group">
                        <label for="level">Academic Level</label>
                        <select id="level" name="level" class="profile-input" required>
                            <option value="">Select Level</option>
                            @foreach($levels as $value => $label)
                                <option value="{{ $value }}" {{ old('level', $subject->level) === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('level')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="profile-form-group">
                        <label for="content">Content</label>
                        <textarea id="content" name="content" class="profile-input" rows="3">{{ old('content', $subject->content) }}</textarea>
                        @error('content')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="profile-form-group">
                        <label>Objectives</label>
                        <div id="objectives-list">
                            @php $objectives = old('objectives', $subject->objectives_array); @endphp
                            @if(empty($objectives))
                                <div class="dynamic-input-group">
                                    <input type="text" name="objectives[]" class="profile-input mb-1" value="">
                                    <button type="button" class="dashboard-btn dashboard-btn-xs dashboard-btn-primary add-objective">+</button>
                                    <button type="button" class="dashboard-btn dashboard-btn-xs dashboard-btn-danger remove-objective" style="display:none;">-</button>
                                </div>
                            @else
                                @foreach($objectives as $index => $objective)
                                    <div class="dynamic-input-group">
                                        <input type="text" name="objectives[]" class="profile-input mb-1" value="{{ $objective }}">
                                        <button type="button" class="dashboard-btn dashboard-btn-xs dashboard-btn-primary add-objective">+</button>
                                        <button type="button" class="dashboard-btn dashboard-btn-xs dashboard-btn-danger remove-objective" style="display:{{ $index === 0 && count($objectives) === 1 ? 'none' : '' }};">-</button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        @error('objectives')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="profile-form-group">
                        <label>Assessment Methods</label>
                        <div id="assessment-methods-list">
                            @php $assessment_methods = old('assessment_methods', $subject->assessment_methods_array); @endphp
                            @if(empty($assessment_methods))
                                <div class="dynamic-input-group">
                                    <input type="text" name="assessment_methods[]" class="profile-input mb-1" value="">
                                    <button type="button" class="dashboard-btn dashboard-btn-xs dashboard-btn-primary add-assessment-method">+</button>
                                    <button type="button" class="dashboard-btn dashboard-btn-xs dashboard-btn-danger remove-assessment-method" style="display:none;">-</button>
                                </div>
                            @else
                                @foreach($assessment_methods as $index => $method)
                                    <div class="dynamic-input-group">
                                        <input type="text" name="assessment_methods[]" class="profile-input mb-1" value="{{ $method }}">
                                        <button type="button" class="dashboard-btn dashboard-btn-xs dashboard-btn-primary add-assessment-method">+</button>
                                        <button type="button" class="dashboard-btn dashboard-btn-xs dashboard-btn-danger remove-assessment-method" style="display:{{ $index === 0 && count($assessment_methods) === 1 ? 'none' : '' }};">-</button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        @error('assessment_methods')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="profile-col profile-col-password">
                    <div class="profile-form-group">
                        <label for="duration">Duration</label>
                        <input type="text" id="duration" name="duration" value="{{ old('duration', $subject->duration) }}" class="profile-input">
                        @error('duration')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="profile-form-group">
                        <label for="total_topics">Total Topics</label>
                        <input type="number" id="total_topics" name="total_topics" value="{{ old('total_topics', $subject->total_topics) }}" class="profile-input">
                        @error('total_topics')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="profile-form-group">
                        <label for="total_resources">Total Resources</label>
                        <input type="number" id="total_resources" name="total_resources" value="{{ old('total_resources', $subject->total_resources) }}" class="profile-input">
                        @error('total_resources')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="profile-form-group">
                        <label for="paper_count">Number of Papers</label>
                        <input type="number"
                               min="1"
                               max="10"
                               id="paper_count"
                               name="paper_count"
                               value="{{ old('paper_count', $subject->paper_count ?? max(1, count(array_filter($paperList, fn($paper) => !empty($paper['name']))))) }}"
                               class="profile-input">
                        <p class="mt-1 text-sm text-gray-500">Ensure this matches the list of papers below.</p>
                        @error('paper_count')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="profile-form-group">
                        <label>Papers & Codes</label>
                        <div id="papers-list" class="papers-list" data-next-index="{{ $nextPaperIndex }}">
                            @foreach($paperList as $index => $paper)
                                <div class="paper-item">
                                    <div class="paper-item-header">
                                        <span>Paper {{ $index + 1 }}</span>
                                        <button type="button" class="dashboard-btn dashboard-btn-xs dashboard-btn-danger remove-paper" {{ $index === 0 && count($paperList) === 1 ? 'style=display:none;' : '' }}>Remove</button>
                                    </div>
                                    <div class="paper-item-body">
                                        <input type="text"
                                               name="papers[{{ $index }}][name]"
                                               value="{{ $paper['name'] ?? '' }}"
                                               class="profile-input"
                                               placeholder="Paper name (e.g., Paper 1)"
                                               required>
                                        <input type="text"
                                               name="papers[{{ $index }}][code]"
                                               value="{{ $paper['code'] ?? '' }}"
                                               class="profile-input"
                                               placeholder="Code (optional)">
                                        <textarea name="papers[{{ $index }}][description]"
                                                  class="profile-input"
                                                  rows="2"
                                                  placeholder="Description (optional)">{{ $paper['description'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="dashboard-btn dashboard-btn-secondary dashboard-btn-sm" id="add-paper-btn">
                            <i class="fas fa-plus mr-1"></i> Add Paper
                        </button>
                        @error('papers')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="profile-form-group">
                        <label>Learning Outcomes</label>
                        <div id="learning-outcomes-list">
                            @php $learning_outcomes = old('learning_outcomes', $subject->learning_outcomes_array); @endphp
                            @if(empty($learning_outcomes))
                                <div class="dynamic-input-group">
                                    <input type="text" name="learning_outcomes[]" class="profile-input mb-1" value="">
                                    <button type="button" class="dashboard-btn dashboard-btn-xs dashboard-btn-primary add-learning-outcome">+</button>
                                    <button type="button" class="dashboard-btn dashboard-btn-xs dashboard-btn-danger remove-learning-outcome" style="display:none;">-</button>
                                </div>
                            @else
                                @foreach($learning_outcomes as $index => $outcome)
                                    <div class="dynamic-input-group">
                                        <input type="text" name="learning_outcomes[]" class="profile-input mb-1" value="{{ $outcome }}">
                                        <button type="button" class="dashboard-btn dashboard-btn-xs dashboard-btn-primary add-learning-outcome">+</button>
                                        <button type="button" class="dashboard-btn dashboard-btn-xs dashboard-btn-danger remove-learning-outcome" style="display:{{ $index === 0 && count($learning_outcomes) === 1 ? 'none' : '' }};">-</button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        @error('learning_outcomes')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="profile-form-group">
                        <label>Prerequisites</label>
                        <div id="prerequisites-list">
                            @php $prerequisites = old('prerequisites', $subject->prerequisites_array); @endphp
                            @if(empty($prerequisites))
                                <div class="dynamic-input-group">
                                    <input type="text" name="prerequisites[]" class="profile-input mb-1" value="">
                                    <button type="button" class="dashboard-btn dashboard-btn-xs dashboard-btn-primary add-prerequisite">+</button>
                                    <button type="button" class="dashboard-btn dashboard-btn-xs dashboard-btn-danger remove-prerequisite" style="display:none;">-</button>
                                </div>
                            @else
                                @foreach($prerequisites as $index => $prerequisite)
                                    <div class="dynamic-input-group">
                                        <input type="text" name="prerequisites[]" class="profile-input mb-1" value="{{ $prerequisite }}">
                                        <button type="button" class="dashboard-btn dashboard-btn-xs dashboard-btn-primary add-prerequisite">+</button>
                                        <button type="button" class="dashboard-btn dashboard-btn-xs dashboard-btn-danger remove-prerequisite" style="display:{{ $index === 0 && count($prerequisites) === 1 ? 'none' : '' }};">-</button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        @error('prerequisites')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="profile-form-group">
                        <label for="passing_score">Passing Score (%)</label>
                        <input type="number" step="0.01" id="passing_score" name="passing_score" value="{{ old('passing_score', $subject->passing_score) }}" class="profile-input">
                        @error('passing_score')<div class="dashboard-alert dashboard-alert-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="profile-form-group">
                        <label for="is_active">
                            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $subject->is_active) ? 'checked' : '' }}> Active
                        </label>
                    </div>
                </div>
            </div>
            <button type="submit" class="dashboard-btn dashboard-btn-primary">Update Subject</button>
        </form>
    </div>
    <!-- Add Error Display Section -->
    <div id="errorDisplay" class="dashboard-alert" style="display: none; margin-bottom: 20px;"></div>
    <div id="successDisplay" class="dashboard-alert dashboard-alert-success" style="display: none; margin-bottom: 20px;"></div>
</div>
<style>
.papers-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.paper-item {
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1rem;
    background: #f9fafb;
}

.paper-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
    font-weight: 600;
}

.paper-item-body {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.75rem;
}

.paper-item-body textarea {
    grid-column: span 2;
}

@media (max-width: 640px) {
    .paper-item-body textarea {
        grid-column: span 1;
    }
}
</style>
<script>
// Dynamic add/remove for Objectives
function addDynamicInput(listId, inputName, addBtnClass, removeBtnClass) {
    const container = document.querySelector(listId);
    if (!container) {
        console.error(`Container not found: ${listId}`);
        return;
    }
    container.addEventListener('click', function(e) {
        if (e.target.classList.contains(addBtnClass)) {
            e.preventDefault();
            const group = e.target.closest('.dynamic-input-group');
            const clone = group.cloneNode(true);
            clone.querySelector('input').value = '';
            clone.querySelector('.remove-objective, .remove-assessment-method, .remove-learning-outcome, .remove-prerequisite').style.display = '';
            group.parentNode.appendChild(clone);
            updateRemoveButtons(listId, removeBtnClass);
        } else if (e.target.classList.contains(removeBtnClass)) {
            e.preventDefault();
            const group = e.target.closest('.dynamic-input-group');
            if (container.querySelectorAll('.dynamic-input-group').length > 1) {
                group.remove();
                updateRemoveButtons(listId, removeBtnClass);
            }
        }
    });
    updateRemoveButtons(listId, removeBtnClass);
}
function updateRemoveButtons(listId, removeBtnClass) {
    const container = document.querySelector(listId);
    const groups = container.querySelectorAll('.dynamic-input-group');
    groups.forEach((group, idx) => {
        const removeBtn = group.querySelector('.' + removeBtnClass);
        if (removeBtn) removeBtn.style.display = (groups.length > 1 && idx !== 0) ? '' : 'none';
    });
}
addDynamicInput('#objectives-list', 'objectives[]', 'add-objective', 'remove-objective');
addDynamicInput('#learning-outcomes-list', 'learning_outcomes[]', 'add-learning-outcome', 'remove-learning-outcome');
addDynamicInput('#prerequisites-list', 'prerequisites[]', 'add-prerequisite', 'remove-prerequisite');
addDynamicInput('#assessment-methods-list', 'assessment_methods[]', 'add-assessment-method', 'remove-assessment-method');

// Papers repeater
const papersList = document.getElementById('papers-list');
const addPaperBtn = document.getElementById('add-paper-btn');

function syncPaperCountInput() {
    const paperCountInput = document.getElementById('paper_count');
    if (paperCountInput && papersList) {
        paperCountInput.value = Math.max(1, papersList.children.length);
    }
}

function addPaperRow(data = { name: '', code: '', description: '' }) {
    if (!papersList) {
        return;
    }

    const nextIndex = parseInt(papersList.dataset.nextIndex || '0', 10);
    const wrapper = document.createElement('div');
    wrapper.classList.add('paper-item');
    wrapper.innerHTML = `
        <div class="paper-item-header">
            <span>Paper ${nextIndex + 1}</span>
            <button type="button" class="dashboard-btn dashboard-btn-xs dashboard-btn-danger remove-paper">Remove</button>
        </div>
        <div class="paper-item-body">
            <input type="text" name="papers[${nextIndex}][name]" value="${data.name || ''}" class="profile-input" placeholder="Paper name (e.g., Paper 1)" required>
            <input type="text" name="papers[${nextIndex}][code]" value="${data.code || ''}" class="profile-input" placeholder="Code (optional)">
            <textarea name="papers[${nextIndex}][description]" class="profile-input" rows="2" placeholder="Description (optional)">${data.description || ''}</textarea>
        </div>
    `;
    papersList.appendChild(wrapper);
    papersList.dataset.nextIndex = nextIndex + 1;
    syncPaperCountInput();
}

if (addPaperBtn && papersList) {
    addPaperBtn.addEventListener('click', function(e) {
        e.preventDefault();
        addPaperRow();
    });

    papersList.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-paper')) {
            e.preventDefault();
            const item = e.target.closest('.paper-item');
            if (item && papersList.children.length > 1) {
                item.remove();
                syncPaperCountInput();
            }
        }
    });

    syncPaperCountInput();
}

// Auto-generate slug from name
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

// Add error display functions
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

// Update form submission handler for AJAX
const subjectForm = document.getElementById('subjectForm');
subjectForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');

    // Ensure slug is set if name is provided
    const nameInput = form.querySelector('#name');
    const slugInput = form.querySelector('#slug');
    if (nameInput.value) {
        const slug = nameInput.value
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
        slugInput.value = slug;
        formData.set('slug', slug); // Explicitly set slug in formData
    }

    // Clear previous errors
    document.getElementById('errorDisplay').style.display = 'none';
    document.getElementById('successDisplay').style.display = 'none';

    // Disable submit button and show loading state
    submitButton.disabled = true;
    submitButton.textContent = 'Updating...';

    // Log form data for debugging
    const formDataObj = {};
    formData.forEach((value, key) => {
        formDataObj[key] = value;
    });
    console.log('Submitting form data:', formDataObj);

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
                    // Try to parse as JSON
                    const data = JSON.parse(text);
                    throw new Error(data.message || 'Server error occurred');
                } catch (e) {
                    // If not JSON, use the text as error message
                    throw new Error(text || 'Server error occurred');
                }
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showSuccess(data.message || 'Subject updated successfully!');
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1500);
        } else {
            // Handle validation errors
            if (data.errors) {
                let errorMessage = '';
                Object.keys(data.errors).forEach(key => {
                    errorMessage += `${key}: ${data.errors[key][0]}\n`;
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                        const feedback = input.nextElementSibling;
                        if (feedback && feedback.classList.contains('invalid-feedback')) {
                            feedback.textContent = data.errors[key][0];
                        }
                    }
                });
                showError(errorMessage);
            } else {
                showError(data.message || 'An error occurred while updating the subject.');
            }
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.textContent = 'Update Subject';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        let errorMessage = error.message;
        try {
            // Try to parse the error message as JSON
            const errorData = JSON.parse(error.message);
            if (errorData.message) {
                errorMessage = errorData.message;
            }
        } catch (e) {
            // If not JSON, use the original error message
        }
        showError(errorMessage || 'An error occurred while updating the subject. Please try again.');
        // Re-enable submit button
        submitButton.disabled = false;
        submitButton.textContent = 'Update Subject';
    });
});
</script>
@endsection 