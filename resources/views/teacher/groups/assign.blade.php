@extends('layouts.dashboard')

@section('content')
<div class="form-page">
    <div class="page-header">
        <a href="{{ route('teacher.groups.submissions', $group) }}" class="back-link"><i class="fas fa-arrow-left"></i> Back</a>
        <h1>Assign Resource to {{ $group->name }}</h1>
    </div>

    <div class="form-container">
        @if($resources->isEmpty())
        <div class="alert alert-warning">
            <p>You don't have any active resources to assign. <a href="/admin/resources/create">Create a resource first</a>.</p>
        </div>
        @else
        <form action="{{ route('teacher.groups.assign.submit', $group) }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="resource_id">Select Resource *</label>
                <select id="resource_id" name="resource_id" class="form-control @error('resource_id') is-invalid @enderror" required>
                    <option value="">-- Select a resource --</option>
                    @foreach($resources as $r)
                        <option value="{{ $r->id }}">{{ $r->title }}</option>
                    @endforeach
                </select>
                @error('resource_id') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="due_date">Due Date (optional)</label>
                <input type="date" id="due_date" name="due_date" class="form-control @error('due_date') is-invalid @enderror">
                @error('due_date') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Assign Resource</button>
                <a href="{{ route('teacher.groups.submissions', $group) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
        @endif
    </div>
</div>

<style>
    .form-page { padding: 20px; max-width: 600px; margin: 0 auto; }
    .page-header { margin-bottom: 30px; }
    .back-link { display: inline-block; margin-bottom: 10px; color: #007bff; text-decoration: none; }
    .back-link:hover { text-decoration: underline; }
    .page-header h1 { font-size: 24px; margin: 10px 0 0 0; }
    .form-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .alert { padding: 12px 16px; border-radius: 4px; margin-bottom: 20px; }
    .alert-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
    .alert a { color: #856404; }
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
