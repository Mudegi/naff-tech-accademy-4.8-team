@extends('layouts.dashboard')

@section('content')
<div class="form-page">
    <div class="page-header">
        <a href="{{ route('teacher.groups.index') }}" class="back-link"><i class="fas fa-arrow-left"></i> Back</a>
        <h1>Create New Group</h1>
    </div>

    <div class="form-container">
        <form action="{{ route('teacher.groups.store') }}" method="POST" class="form-horizontal">
            @csrf

            <div class="form-group">
                <label for="name">Group Name *</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" required value="{{ old('name') }}">
                @error('name') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description') }}</textarea>
                @error('description') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="max_members">Maximum Members *</label>
                <input type="number" id="max_members" name="max_members" class="form-control @error('max_members') is-invalid @enderror" min="2" max="50" value="{{ old('max_members', 5) }}" required>
                @error('max_members') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="class_id">Class (optional)</label>
                <select name="class_id" id="class_id" class="form-control @error('class_id') is-invalid @enderror">
                    <option value="">-- Select class --</option>
                    @if(!empty($classes))
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ old('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    @endif
                </select>
                @error('class_id') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create Group</button>
                <a href="{{ route('teacher.groups.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
    .form-page { padding: 20px; max-width: 600px; margin: 0 auto; }
    .page-header { margin-bottom: 30px; }
    .back-link { display: inline-block; margin-bottom: 10px; color: #007bff; text-decoration: none; }
    .back-link:hover { text-decoration: underline; }
    .page-header h1 { font-size: 24px; margin: 10px 0 0 0; }
    .form-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 6px; font-weight: 500; }
    .form-control { width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
    .form-control:focus { outline: none; border-color: #007bff; box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25); }
    .form-control.is-invalid { border-color: #dc3545; }
    .error { color: #dc3545; font-size: 13px; display: block; margin-top: 4px; }
    .form-actions { display: flex; gap: 10px; margin-top: 30px; }
    .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; font-weight: 500; }
    .btn-primary { background: #007bff; color: white; }
    .btn-primary:hover { background: #0056b3; }
    .btn-secondary { background: #6c757d; color: white; }
    .btn-secondary:hover { background: #545b62; }
</style>
@endsection
