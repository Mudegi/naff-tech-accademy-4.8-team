@extends('layouts.dashboard')

@section('content')
<div class="terms-container">
    <div class="terms-header">
        <h2><i class="fas fa-calendar-alt"></i> Term Details</h2>
        <a href="{{ route('admin.terms.index') }}" class="terms-add-btn"><i class="fas fa-arrow-left"></i> Back to Terms</a>
    </div>
    <div class="terms-card">
        <div class="terms-card-body">
            <table class="terms-table">
                <tr>
                    <th><i class="fas fa-hashtag"></i> ID</th>
                    <td>{{ $term->id }}</td>
                </tr>
                <tr>
                    <th><i class="fas fa-font"></i> Name</th>
                    <td>{{ $term->name }}</td>
                </tr>
                <tr>
                    <th><i class="fas fa-link"></i> Slug</th>
                    <td>{{ $term->slug }}</td>
                </tr>
                <tr>
                    <th><i class="fas fa-align-left"></i> Description</th>
                    <td>{{ $term->description }}</td>
                </tr>
                <tr>
                    <th><i class="fas fa-play"></i> Start Date</th>
                    <td>{{ $term->start_date }}</td>
                </tr>
                <tr>
                    <th><i class="fas fa-stop"></i> End Date</th>
                    <td>{{ $term->end_date }}</td>
                </tr>
                <tr>
                    <th><i class="fas fa-toggle-on"></i> Active</th>
                    <td>
                        @if($term->is_active)
                            <span class="terms-badge-active"><i class="fas fa-check-circle"></i> Active</span>
                        @else
                            <span class="terms-badge-inactive"><i class="fas fa-times-circle"></i> Inactive</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th><i class="fas fa-user"></i> Created By</th>
                    <td>{{ $term->created_by }}</td>
                </tr>
                <tr>
                    <th><i class="fas fa-clock"></i> Created At</th>
                    <td>{{ $term->created_at }}</td>
                </tr>
                <tr>
                    <th><i class="fas fa-clock"></i> Updated At</th>
                    <td>{{ $term->updated_at }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
<style>
.terms-container {
    max-width: 700px;
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
    width: 180px;
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