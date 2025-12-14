@extends('layouts.dashboard')

@section('title', 'Link Parent to Student')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-link me-2"></i>Link Parent to Student
            </h1>
            <p class="text-muted mb-0">Create a new relationship between a parent and student account</p>
        </div>
        <a href="{{ route('admin.parent-student.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Links
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Link Parent to Student</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.parent-student.store') }}" method="POST" id="linkForm">
                        @csrf

                        <!-- Select Parent -->
                        <div class="mb-4">
                            <label for="parent_id" class="form-label fw-bold">
                                <i class="fas fa-user-tie me-1"></i>Select Parent <span class="text-danger">*</span>
                            </label>
                            <select name="parent_id" id="parent_id" class="form-select select2" required>
                                <option value="">-- Search for parent by name, phone, or email --</option>
                            </select>
                            <small class="form-text text-muted">
                                Start typing to search for parent accounts
                            </small>
                        </div>

                        <!-- Select Student -->
                        <div class="mb-4">
                            <label for="student_id" class="form-label fw-bold">
                                <i class="fas fa-user-graduate me-1"></i>Select Student <span class="text-danger">*</span>
                            </label>
                            <select name="student_id" id="student_id" class="form-select select2" required>
                                <option value="">-- Search for student by name, phone, or class --</option>
                            </select>
                            <small class="form-text text-muted">
                                Start typing to search for student accounts
                            </small>
                        </div>

                        <!-- Relationship Type -->
                        <div class="mb-4">
                            <label for="relationship" class="form-label fw-bold">
                                <i class="fas fa-heart me-1"></i>Relationship Type <span class="text-danger">*</span>
                            </label>
                            <select name="relationship" id="relationship" class="form-select" required>
                                <option value="parent" selected>Parent</option>
                                <option value="guardian">Guardian</option>
                                <option value="sponsor">Sponsor</option>
                            </select>
                            <small class="form-text text-muted">
                                Specify the relationship between the parent and student
                            </small>
                        </div>

                        <!-- Primary Contact -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" name="is_primary" class="form-check-input" id="is_primary" checked>
                                <label class="form-check-label fw-bold" for="is_primary">
                                    <i class="fas fa-star text-warning me-1"></i>Primary Contact
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Designate this parent as the primary contact for school communications
                            </small>
                        </div>

                        <!-- Receive Notifications -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" name="receive_notifications" class="form-check-input" id="receive_notifications" checked>
                                <label class="form-check-label fw-bold" for="receive_notifications">
                                    <i class="fas fa-bell text-info me-1"></i>Receive Notifications
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Enable email notifications for performance alerts and weekly summaries
                            </small>
                        </div>

                        <hr class="my-4">

                        <!-- Submit Button -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.parent-student.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-link me-1"></i>Create Link
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Information Card -->
            <div class="card shadow-sm mt-4 border-info">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-info-circle text-info me-2"></i>Important Information
                    </h6>
                    <ul class="mb-0">
                        <li class="mb-2">A parent account can be linked to multiple students (e.g., siblings)</li>
                        <li class="mb-2">A student can have multiple parents/guardians linked</li>
                        <li class="mb-2">Primary contacts receive priority communications from the school</li>
                        <li class="mb-2">Parents with notifications enabled will receive weekly performance summaries</li>
                        <li class="mb-2">Both parent and student accounts must already exist in the system</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 for parent search
    $('#parent_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Search for parent...',
        allowClear: true,
        ajax: {
            url: '{{ route("admin.parent-student.search-parents") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    term: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    // Initialize Select2 for student search
    $('#student_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Search for student...',
        allowClear: true,
        ajax: {
            url: '{{ route("admin.parent-student.search-students") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    term: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });
});
</script>

<style>
.select2-container--bootstrap-5 .select2-selection {
    min-height: 38px;
}
</style>
@endsection
