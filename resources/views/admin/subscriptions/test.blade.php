<!DOCTYPE html>
<html>
<head>
    <title>Subscriptions Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .subscription { border: 1px solid #ccc; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>User Subscriptions</h1>
    <p>Subscriptions count: {{ $subscriptions->count() }}</p>

    @if($subscriptions->count() > 0)
        @foreach($subscriptions as $subscription)
            <div class="subscription">
                <h3>{{ $subscription->user->name ?? 'Unknown User' }}</h3>
                <p>Package: {{ $subscription->subscriptionPackage->name ?? 'Unknown Package' }}</p>
                <p>Status: {{ $subscription->is_active ? 'Active' : 'Inactive' }}</p>
                <p>Payment: {{ $subscription->payment_status }} - {{ $subscription->payment_method }}</p>
            </div>
        @endforeach
    @else
        <p>No subscriptions found.</p>
    @endif
</body>
</html>
