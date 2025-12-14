@extends('layouts.dashboard')

@section('title', 'Edit Term')

@section('content')
<div class="terms-container">
    <div class="terms-header">
        <h2><i class="fas fa-calendar-edit"></i> Edit Term</h2>
        <a href="{{ route('admin.terms.index') }}" class="terms-add-btn"><i class="fas fa-arrow-left"></i> Back to Terms</a>
    </div>
    <div class="terms-card">
        <div class="terms-card-body">
            <form action="{{ route('admin.terms.update', $term->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="terms-form-group">
                    <label for="name"><i class="fas fa-font"></i> Term Name</label>
                    <input type="text" id="name" name="name" class="terms-form-input @error('name') is-invalid @enderror" value="{{ old('name', $term->name) }}" required>
                    @error('name')
                        <div class="terms-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="terms-form-row">
                    <div class="terms-form-group">
                        <label for="start_date"><i class="fas fa-play"></i> Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="terms-form-input @error('start_date') is-invalid @enderror" value="{{ old('start_date', $term->start_date) }}" required>
                        @error('start_date')
                            <div class="terms-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="terms-form-group">
                        <label for="end_date"><i class="fas fa-stop"></i> End Date</label>
                        <input type="date" id="end_date" name="end_date" class="terms-form-input @error('end_date') is-invalid @enderror" value="{{ old('end_date', $term->end_date) }}" required>
                        @error('end_date')
                            <div class="terms-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="terms-form-group">
                    <label for="description"><i class="fas fa-align-left"></i> Description</label>
                    <textarea id="description" name="description" class="terms-form-input @error('description') is-invalid @enderror" rows="3">{{ old('description', $term->description) }}</textarea>
                    @error('description')
                        <div class="terms-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="terms-form-row">
                    <div class="terms-form-group">
                        <label for="is_active"><i class="fas fa-toggle-on"></i> Status</label>
                        <select id="is_active" name="is_active" class="terms-form-input @error('is_active') is-invalid @enderror" required>
                            <option value="1" {{ old('is_active', $term->is_active) == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active', $term->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('is_active')
                            <div class="terms-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="terms-form-actions">
                    <button type="submit" class="terms-submit-btn">
                        <i class="fas fa-save"></i> Update Term
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
.terms-container {
    max-width: 600px;
    margin: 30px auto;
    padding: 0 20px;
}
.terms-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}
.terms-header h2 {
    margin: 0;
    font-size: 2rem;
    color: #222;
}
.terms-add-btn {
    background: #007bff;
    color: #fff;
    padding: 10px 18px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 500;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background 0.2s;
}
.terms-add-btn:hover {
    background: #0056b3;
}
.terms-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    overflow-x: auto;
}
.terms-card-body {
    padding: 24px;
}
.terms-form-group {
    margin-bottom: 18px;
    display: flex;
    flex-direction: column;
}
.terms-form-row {
    display: flex;
    gap: 18px;
}
.terms-form-row .terms-form-group {
    flex: 1;
}
.terms-form-input {
    padding: 10px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 4px;
    font-size: 1rem;
    margin-top: 6px;
    background: #f8fafc;
    transition: border 0.2s;
}
.terms-form-input:focus {
    border-color: #007bff;
    outline: none;
}
.terms-error {
    color: #dc3545;
    font-size: 0.97em;
    margin-top: 4px;
}
.terms-form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 24px;
}
.terms-submit-btn {
    background: #007bff;
    color: #fff;
    padding: 10px 24px;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: background 0.2s;
}
.terms-submit-btn:hover {
    background: #0056b3;
}
@media (max-width: 700px) {
    .terms-card-body {
        padding: 10px;
    }
    .terms-header h2 {
        font-size: 1.3rem;
    }
    .terms-add-btn {
        font-size: 0.95rem;
        padding: 8px 12px;
    }
    .terms-form-row {
        flex-direction: column;
        gap: 0;
    }
}
</style>
@endsection 