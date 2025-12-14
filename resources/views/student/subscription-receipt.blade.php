<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subscription Receipt</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f9f9f9; margin: 0; padding: 0; }
        .receipt-container {
            max-width: 370px;
            margin: 2rem auto 0 auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 2rem 1.5rem 1rem 1.5rem;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .receipt-title {
            font-size: 1.3rem;
            font-weight: bold;
            color: #222;
            margin-bottom: 0.2rem;
        }
        .receipt-divider {
            border: none;
            border-top: 1px dashed #bbb;
            margin: 1.2rem 0;
        }
        .receipt-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.7rem;
            font-size: 1.02rem;
        }
        .receipt-label { color: #555; }
        .receipt-value { color: #222; font-weight: 500; }
        .receipt-footer {
            text-align: center;
            margin-top: 1.2rem;
            font-size: 0.95rem;
            color: #888;
        }
        .site-footer {
            text-align: center;
            font-size: 0.97rem;
            color: #444;
            margin-top: 0.7rem;
            font-weight: 500;
        }
        .print-btn {
            display: block;
            width: 100%;
            margin: 1.2rem 0 0 0;
            padding: 0.7rem 0;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 0.3rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
        }
        @media print {
            .print-btn { display: none; }
            .receipt-container { box-shadow: none; margin: 0; padding-bottom: 0.5rem; }
            .site-footer { margin-bottom: 0; }
            body { background: #fff; }
        }
    </style>
</head>
<body>
<div class="receipt-container">
    <div class="receipt-header">
        <div class="receipt-title">{{ config('app.name') }}</div>
        <div style="font-size:1.05rem; color:#444;">Subscription Payment Receipt</div>
    </div>
    <hr class="receipt-divider">
    <div class="receipt-row"><span class="receipt-label">Name:</span><span class="receipt-value">{{ $user->name }}</span></div>
    <div class="receipt-row"><span class="receipt-label">Email:</span><span class="receipt-value">{{ $user->email }}</span></div>
    <div class="receipt-row"><span class="receipt-label">Package:</span><span class="receipt-value">{{ $package->name ?? 'N/A' }}</span></div>
    <div class="receipt-row"><span class="receipt-label">Amount Paid:</span><span class="receipt-value">UGX {{ number_format($sub->amount_paid, 0) }}</span></div>
    <div class="receipt-row"><span class="receipt-label">Payment Method:</span><span class="receipt-value">{{ ucfirst($sub->payment_method) }}</span></div>
    <div class="receipt-row"><span class="receipt-label">Start Date:</span><span class="receipt-value">{{ $sub->start_date ? \Carbon\Carbon::parse($sub->start_date)->format('M d, Y') : '-' }}</span></div>
    <div class="receipt-row"><span class="receipt-label">End Date:</span><span class="receipt-value">{{ $sub->end_date ? \Carbon\Carbon::parse($sub->end_date)->format('M d, Y') : '-' }}</span></div>
    <div class="receipt-row"><span class="receipt-label">Transaction ID:</span><span class="receipt-value">{{ $sub->transaction_id }}</span></div>
    <div class="receipt-row"><span class="receipt-label">Payment Phone:</span><span class="receipt-value">{{ $sub->payment_phone ?? '-' }}</span></div>
    <hr class="receipt-divider">
    <div class="receipt-footer">
        Thank you for your payment!<br>
        <span style="font-size:0.93rem;">Date: {{ now()->format('M d, Y H:i') }}</span>
    </div>
    <div class="site-footer">{{ config('app.url') }}</div>
    <button class="print-btn" onclick="window.print()">Download PDF / Print</button>
</div>
</body>
</html> 