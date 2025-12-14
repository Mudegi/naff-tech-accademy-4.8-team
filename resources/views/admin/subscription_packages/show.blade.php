@extends('layouts.dashboard')

@section('content')
<div class="sp-container">
    <div class="sp-header">
        <h2><i class="fas fa-box"></i> Package Details</h2>
        <a href="{{ route('admin.subscription-packages.index') }}" class="sp-add-btn"><i class="fas fa-arrow-left"></i> Back to Packages</a>
    </div>
    <div class="sp-card">
        <div class="sp-card-body">
            <table class="sp-table">
                <tr>
                    <th><i class="fas fa-hashtag"></i> ID</th>
                    <td>{{ $package->id }}</td>
                </tr>
                <tr>
                    <th><i class="fas fa-font"></i> Name</th>
                    <td>{{ $package->name }}</td>
                </tr>
                <tr>
                    <th><i class="fas fa-align-left"></i> Description</th>
                    <td>{{ $package->description }}</td>
                </tr>
                <tr>
                    <th><i class="fas fa-money-bill"></i> Price</th>
                    <td>{{ $package->price }}</td>
                </tr>
                <tr>
                    <th><i class="fas fa-clock"></i> Duration (days)</th>
                    <td>{{ $package->duration_days }}</td>
                </tr>
                <tr>
                    <th><i class="fas fa-list"></i> Features</th>
                    <td>
                        @if(count($features))
                        <ul class="sp-features-list">
                            @foreach($features as $feature)
                                <li><i class="fas fa-check"></i> {{ $feature }}</li>
                            @endforeach
                        </ul>
                        @else
                        <span class="sp-empty">No features listed.</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th><i class="fas fa-toggle-on"></i> Active</th>
                    <td>
                        @if($package->is_active)
                            <span class="sp-badge-active"><i class="fas fa-check-circle"></i> Active</span>
                        @else
                            <span class="sp-badge-inactive"><i class="fas fa-times-circle"></i> Inactive</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th><i class="fas fa-user"></i> Created By</th>
                    <td>{{ $package->created_by }}</td>
                </tr>
                <tr>
                    <th><i class="fas fa-clock"></i> Created At</th>
                    <td>{{ $package->created_at }}</td>
                </tr>
                <tr>
                    <th><i class="fas fa-clock"></i> Updated At</th>
                    <td>{{ $package->updated_at }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
<style>
.sp-container {
    max-width: 700px;
    margin: 30px auto;
    padding: 0 20px;
}
.sp-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}
.sp-header h2 {
    margin: 0;
    font-size: 2rem;
    color: #222;
}
.sp-add-btn {
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
.sp-add-btn:hover {
    background: #0056b3;
}
.sp-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    overflow-x: auto;
}
.sp-card-body {
    padding: 24px;
}
.sp-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 1rem;
    background: #fff;
}
.sp-table th, .sp-table td {
    padding: 12px 10px;
    border-bottom: 1px solid #e5e7eb;
    text-align: left;
}
.sp-table th {
    background: #f8fafc;
    color: #333;
    font-weight: 600;
    width: 180px;
}
.sp-table tr:last-child td {
    border-bottom: none;
}
.sp-badge-active {
    background: #28a745;
    color: #fff;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.95em;
}
.sp-badge-inactive {
    background: #adb5bd;
    color: #fff;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.95em;
}
.sp-features-list {
    margin: 0;
    padding-left: 20px;
}
.sp-features-list li {
    margin-bottom: 6px;
    color: #222;
    font-size: 1em;
    display: flex;
    align-items: center;
    gap: 8px;
}
.sp-empty {
    color: #888;
    font-size: 1em;
}
@media (max-width: 700px) {
    .sp-card-body {
        padding: 10px;
    }
    .sp-header h2 {
        font-size: 1.3rem;
    }
    .sp-add-btn {
        font-size: 0.95rem;
        padding: 8px 12px;
    }
    .sp-table th, .sp-table td {
        padding: 8px 6px;
        font-size: 0.95em;
    }
}
</style>
@endsection 