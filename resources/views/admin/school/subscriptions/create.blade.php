@extends('layouts.dashboard')

@section('content')
<style>
.subscription-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}
.page-header {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    border-radius: 16px;
    padding: 40px;
    margin-bottom: 30px;
    color: white;
    box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
}
.page-header-content {
    display: flex;
    align-items: center;
    gap: 20px;
}
.page-header-icon {
    width: 70px;
    height: 70px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
}
.page-header-text h2 {
    font-size: 28px;
    font-weight: bold;
    margin: 0 0 8px 0;
}
.page-header-text p {
    font-size: 16px;
    opacity: 0.9;
    margin: 0;
}
.package-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 3px solid #2563eb;
    overflow: hidden;
    position: relative;
    margin-bottom: 30px;
    transition: transform 0.3s, box-shadow 0.3s;
}
.package-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}
.recommended-badge {
    position: absolute;
    top: 25px;
    right: 25px;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: white;
    padding: 10px 20px;
    border-radius: 50px;
    font-weight: bold;
    font-size: 14px;
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
    z-index: 10;
    display: flex;
    align-items: center;
    gap: 8px;
}
.package-header {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    padding: 35px 40px;
    border-bottom: 2px solid #e5e7eb;
}
.package-header-content {
    display: flex;
    align-items: center;
    gap: 20px;
}
.package-icon {
    width: 60px;
    height: 60px;
    background: #2563eb;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}
.package-title {
    font-size: 32px;
    font-weight: bold;
    color: #1f2937;
    margin: 0 0 8px 0;
}
.package-description {
    color: #6b7280;
    font-size: 15px;
    margin: 0;
}
.package-body {
    padding: 40px;
}
.price-section {
    text-align: center;
    padding-bottom: 35px;
    margin-bottom: 35px;
    border-bottom: 2px solid #f3f4f6;
}
.price-amount {
    font-size: 56px;
    font-weight: 900;
    background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0 0 10px 0;
    line-height: 1;
}
.price-currency {
    font-size: 24px;
    font-weight: 600;
    color: #6b7280;
    margin: 0 0 15px 0;
}
.price-duration {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    color: #6b7280;
    font-size: 14px;
}
.features-section {
    margin-bottom: 35px;
}
.features-title {
    font-size: 22px;
    font-weight: bold;
    color: #1f2937;
    margin: 0 0 25px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.features-title i {
    color: #10b981;
    font-size: 24px;
}
.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 15px;
}
.feature-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 15px;
    border-radius: 12px;
    background: #f9fafb;
    transition: background 0.2s;
}
.feature-item:hover {
    background: #f3f4f6;
}
.feature-check {
    width: 28px;
    height: 28px;
    background: #d1fae5;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 2px;
}
.feature-check i {
    color: #10b981;
    font-size: 12px;
}
.feature-text {
    color: #374151;
    font-weight: 500;
    font-size: 15px;
    line-height: 1.5;
}
.cta-button {
    width: 100%;
    background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
    color: white;
    border: none;
    padding: 18px 30px;
    border-radius: 12px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
}
.cta-button:hover {
    background: linear-gradient(135deg, #1d4ed8 0%, #4338ca 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
}
.info-box {
    background: #eff6ff;
    border: 2px solid #bfdbfe;
    border-radius: 16px;
    padding: 25px;
    margin-top: 30px;
}
.info-box-content {
    display: flex;
    align-items: flex-start;
    gap: 20px;
}
.info-icon {
    width: 50px;
    height: 50px;
    background: #dbeafe;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.info-icon i {
    color: #2563eb;
    font-size: 24px;
}
.info-text h4 {
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 8px 0;
    font-size: 16px;
}
.info-text p {
    color: #6b7280;
    font-size: 14px;
    margin: 0 0 12px 0;
    line-height: 1.6;
}
.info-link {
    color: #2563eb;
    font-weight: 500;
    font-size: 14px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: color 0.2s;
}
.info-link:hover {
    color: #1d4ed8;
}
.empty-state {
    max-width: 600px;
    margin: 40px auto;
    background: #fef3c7;
    border: 2px solid #fbbf24;
    border-radius: 16px;
    padding: 40px;
    text-align: center;
}
.empty-icon {
    width: 80px;
    height: 80px;
    background: #fde68a;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}
.empty-icon i {
    color: #d97706;
    font-size: 40px;
}
.empty-state h3 {
    font-size: 22px;
    font-weight: bold;
    color: #92400e;
    margin: 0 0 12px 0;
}
.empty-state p {
    color: #78350f;
    margin: 0 0 25px 0;
    line-height: 1.6;
}
.empty-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #f59e0b;
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: background 0.2s;
}
.empty-button:hover {
    background: #d97706;
}
</style>

<div class="dashboard-content-inner">
    <div class="dashboard-breadcrumbs" style="margin-bottom: 30px;">
        <h1 class="dashboard-title" style="display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-credit-card" style="color: #2563eb;"></i>
            <span>Purchase Subscription</span>
        </h1>
        <div class="breadcrumbs" style="margin-top: 10px;">
            <span>Home</span> <span class="breadcrumb-sep">/</span> 
            <span><a href="{{ route('admin.school.subscriptions.index') }}" style="color: #2563eb; text-decoration: none;">Subscriptions</a></span> <span class="breadcrumb-sep">/</span> 
            <span class="breadcrumb-active">Purchase</span>
        </div>
    </div>

    @if($packages->count() > 0)
        <div class="subscription-page">
            <!-- Header Section -->
            <div class="page-header">
                <div class="page-header-content">
                    <div class="page-header-icon">
                        <i class="fas fa-school"></i>
                    </div>
                    <div class="page-header-text">
                        <h2>School Subscription Package</h2>
                        <p>Choose the perfect plan for your school</p>
                    </div>
                </div>
            </div>

            <!-- Package Card -->
            @foreach($packages as $package)
            <div class="package-card">
                <!-- Recommended Badge -->
                <div class="recommended-badge">
                    <i class="fas fa-star"></i>
                    <span>Recommended</span>
                </div>

                <!-- Package Header -->
                <div class="package-header">
                    <div class="package-header-content">
                        <div class="package-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div>
                            <h3 class="package-title">{{ $package->name }}</h3>
                            <p class="package-description">{{ $package->description }}</p>
                        </div>
                    </div>
                </div>

                <div class="package-body">
                    <!-- Price Section -->
                    <div class="price-section">
                        <div class="price-amount">{{ number_format($package->price, 0) }}</div>
                        <div class="price-currency">UGX</div>
                        <div class="price-duration">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Valid for {{ $package->duration_days }} days</span>
                            <span style="color: #d1d5db;">•</span>
                            <span>{{ round($package->duration_days / 30, 1) }} months</span>
                        </div>
                    </div>

                    <!-- Features Section -->
                    <div class="features-section">
                        <h4 class="features-title">
                            <i class="fas fa-check-circle"></i>
                            <span>What's Included</span>
                        </h4>
                        @if($package->features && is_array($package->features))
                        <div class="features-grid">
                            @foreach($package->features as $feature)
                                <div class="feature-item">
                                    <div class="feature-check">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <span class="feature-text">{{ $feature }}</span>
                                </div>
                            @endforeach
                        </div>
                        @else
                        <div style="text-align: center; color: #6b7280; font-style: italic; padding: 20px;">No features listed</div>
                        @endif
                    </div>

                    <!-- CTA Button -->
                    <form action="{{ route('admin.school.subscriptions.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="subscription_package_id" value="{{ $package->id }}">
                        <button type="submit" class="cta-button">
                            <i class="fas fa-arrow-right"></i>
                            <span>Select This Package</span>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach

            <!-- Info Section -->
            <div class="info-box">
                <div class="info-box-content">
                    <div class="info-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="info-text">
                        <h4>Need Help?</h4>
                        <p>If you have any questions about our subscription packages or need assistance with your purchase, please don't hesitate to contact our support team.</p>
                        <a href="#" class="info-link">
                            <span>Contact Support</span>
                            <i class="fas fa-arrow-right" style="font-size: 12px;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3>No Subscription Packages Available</h3>
            <p>There are currently no subscription packages available for schools. Please contact the administrator for assistance.</p>
            <a href="{{ route('admin.school.subscriptions.index') }}" class="empty-button">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Subscriptions</span>
            </a>
        </div>
    @endif
</div>
@endsection
