@extends('layouts.student-dashboard')

@section('content')
<style>
.subscription-container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 2rem 1rem;
}
.subscription-header {
    font-size: 2rem;
    font-weight: bold;
    color: #1a1a1a;
    margin-bottom: 1.5rem;
}
.subscription-filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: flex-end;
    margin-bottom: 2rem;
    background: #fff;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.07);
    padding: 1rem 1.5rem;
}
.subscription-filter-form label {
    font-size: 0.95rem;
    color: #333;
    margin-bottom: 0.25rem;
    display: block;
}
.subscription-filter-form select {
    padding: 0.5rem 0.75rem;
    border-radius: 0.3rem;
    border: 1px solid #ccc;
    min-width: 160px;
}
.subscription-filter-form button {
    padding: 0.5rem 1.2rem;
    border-radius: 0.3rem;
    background: #4f46e5;
    color: #fff;
    border: none;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s;
}
.subscription-filter-form button:hover {
    background: #3730a3;
}
.subscription-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}
@media (min-width: 600px) {
    .subscription-grid {
        grid-template-columns: 1fr 1fr;
    }
}
@media (min-width: 900px) {
    .subscription-grid {
        grid-template-columns: 1fr 1fr 1fr;
    }
}
.subscription-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    box-shadow: 0 1px 4px rgba(0,0,0,0.07);
    padding: 1.5rem 1.25rem;
    display: flex;
    flex-direction: column;
    transition: box-shadow 0.2s, transform 0.2s;
}
.subscription-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.13);
    transform: translateY(-2px);
}
.subscription-card .card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}
.subscription-card .package-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #222;
}
.subscription-card .status {
    padding: 0.25rem 0.9rem;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-block;
}
.subscription-card .status-success {
    background: #d1fae5;
    color: #065f46;
}
.subscription-card .status-pending {
    background: #fef9c3;
    color: #92400e;
}
.subscription-card .status-failed {
    background: #fee2e2;
    color: #991b1b;
}
.subscription-card .card-row {
    margin-bottom: 0.5rem;
    color: #444;
    font-size: 0.97rem;
}
.subscription-card .card-row strong {
    font-weight: 600;
    color: #222;
}
.subscription-card .transaction-id {
    font-family: monospace;
    font-size: 0.95rem;
    color: #555;
}
.subscription-empty {
    grid-column: 1/-1;
    text-align: center;
    color: #888;
    padding: 2.5rem 0;
}
</style>
<div class="subscription-container">
    <div class="subscription-header">My Subscriptions</div>
    <form method="GET" class="subscription-filter-form">
        <div>
            <label for="package_id">Package</label>
            <select name="package_id" id="package_id">
                <option value="">All Packages</option>
                @foreach($packages as $package)
                    <option value="{{ $package->id }}" {{ request('package_id') == $package->id ? 'selected' : '' }}>{{ $package->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="status">Status</label>
            <select name="status" id="status">
                <option value="">All Statuses</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit">Filter</button>
    </form>
    <div class="subscription-grid">
        @forelse($subscriptions as $sub)
            <div class="subscription-card">
                <div class="card-header">
                    <span class="package-name">{{ optional($packages->where('id', $sub->subscription_package_id)->first())->name ?? 'N/A' }}</span>
                    <span class="status status-{{ $sub->payment_status }}">
                        {{ ucfirst($sub->payment_status) }}
                    </span>
                </div>
                <div class="card-row">Amount: <strong>UGX {{ number_format($sub->amount_paid, 0) }}</strong></div>
                <div class="card-row">Payment Method: <strong>{{ ucfirst($sub->payment_method) }}</strong></div>
                <div class="card-row">Start Date: <strong>{{ $sub->start_date ? \Carbon\Carbon::parse($sub->start_date)->format('M d, Y') : '-' }}</strong></div>
                <div class="card-row">End Date: <strong>{{ $sub->end_date ? \Carbon\Carbon::parse($sub->end_date)->format('M d, Y') : '-' }}</strong></div>
                <div class="card-row">Transaction ID: <span class="transaction-id">{{ $sub->transaction_id }}</span></div>
                @if($sub->payment_status == 'success')
                    <div style="margin-top:1rem;">
                        <a href="{{ route('student.subscription.receipt', $sub->id) }}" target="_blank" class="dashboard-btn dashboard-btn-primary" style="padding:0.5rem 1.2rem; font-size:0.95rem; border-radius:0.3rem; background:#2563eb; color:#fff; text-decoration:none;">Print Receipt</a>
                    </div>
                @endif
            </div>
        @empty
            <div class="subscription-empty">
                <i class="fas fa-receipt" style="font-size:2rem;margin-bottom:0.5rem;"></i>
                <div>No subscriptions found.</div>
            </div>
        @endforelse
    </div>
    <div style="margin-top:2rem;">
        {{ $subscriptions->withQueryString()->links() }}
    </div>
</div>
@endsection 