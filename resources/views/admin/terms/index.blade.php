@extends('layouts.dashboard')

@section('content')
<div class="terms-container">
    <div class="terms-header">
        <h2><i class="fas fa-calendar-alt"></i> Terms</h2>
        @if(in_array('create_term', $userPermissions))
        <a href="{{ route('admin.terms.create') }}" class="terms-add-btn">
            <i class="fas fa-plus"></i> Add New Term
        </a>
        @endif
    </div>

    @if(session('success'))
        <div class="terms-alert-success">{{ session('success') }}</div>
    @endif

    <div class="terms-card">
        <div class="terms-card-body">
            <table class="terms-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> ID</th>
                        <th><i class="fas fa-font"></i> Name</th>
                        <th><i class="fas fa-play"></i> Start Date</th>
                        <th><i class="fas fa-stop"></i> End Date</th>
                        <th><i class="fas fa-toggle-on"></i> Active</th>
                        <th><i class="fas fa-clock"></i> Created At</th>
                        <th><i class="fas fa-cogs"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($terms as $term)
                        <tr>
                            <td>{{ $term->id }}</td>
                            <td>{{ $term->name }}</td>
                            <td>{{ $term->start_date }}</td>
                            <td>{{ $term->end_date }}</td>
                            <td>
                                @if($term->is_active)
                                    <span class="terms-badge-active"><i class="fas fa-check-circle"></i> Active</span>
                                @else
                                    <span class="terms-badge-inactive"><i class="fas fa-times-circle"></i> Inactive</span>
                                @endif
                            </td>
                            <td>{{ $term->created_at }}</td>
                            <td class="terms-actions">
                                @if(in_array('view_term', $userPermissions))
                                <a href="{{ route('admin.terms.show', $term->id) }}" class="terms-show-btn" title="Show"><i class="fas fa-eye"></i></a>
                                @endif
                                @if(in_array('edit_term', $userPermissions))
                                <a href="{{ route('admin.terms.edit', $term->id) }}" class="terms-edit-btn" title="Edit"><i class="fas fa-edit"></i></a>
                                @endif
                                @if(in_array('delete_term', $userPermissions))
                                <form action="{{ route('admin.terms.destroy', $term->id) }}" method="POST" style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="terms-delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this term?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="terms-empty">No terms found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.terms-container {
    max-width: 1100px;
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
.terms-alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    border-radius: 4px;
    padding: 12px 18px;
    margin-bottom: 18px;
    font-size: 1rem;
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
.terms-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 1rem;
    background: #fff;
}
.terms-table th, .terms-table td {
    padding: 12px 10px;
    border-bottom: 1px solid #e5e7eb;
    text-align: left;
}
.terms-table th {
    background: #f8fafc;
    color: #333;
    font-weight: 600;
}
.terms-table tr:last-child td {
    border-bottom: none;
}
.terms-badge-active {
    background: #28a745;
    color: #fff;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.95em;
}
.terms-badge-inactive {
    background: #adb5bd;
    color: #fff;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.95em;
}
.terms-edit-btn {
    background: #ffc107;
    color: #222;
    padding: 6px 14px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 0.97em;
    font-weight: 500;
    transition: background 0.2s;
}
.terms-edit-btn:hover {
    background: #e0a800;
    color: #fff;
}
.terms-empty {
    text-align: center;
    color: #888;
    font-size: 1.1em;
    padding: 30px 0;
}
.terms-actions a, .terms-actions button {
    margin-right: 6px;
    display: inline-block;
    border: none;
    background: none;
    color: #222;
    font-size: 1.1em;
    cursor: pointer;
    transition: color 0.2s;
}
.terms-actions a:hover, .terms-actions button:hover {
    color: #007bff;
}
.terms-delete-btn {
    color: #e74c3c;
}
.terms-delete-btn:hover {
    color: #c0392b;
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
    .terms-table th, .terms-table td {
        padding: 8px 6px;
        font-size: 0.95em;
    }
}
</style>
@endsection 