<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EasypayConfiguration;
use App\Models\FlutterwaveSetting;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SubscriptionPackage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PricingController extends Controller
{
    public function index()
    {
        $packages = DB::table('subscription_packages')
            ->where('is_active', true)
            ->orderBy('price')
            ->get()
            ->map(function ($package) {
                $package->features = json_decode($package->features, true) ?: [];
                return $package;
            });

        $easypay = EasypayConfiguration::where('is_active', true)->first();
        $flutterwave = FlutterwaveSetting::orderByDesc('id')->first();

        $activeSubscription = null;
        $currentPackage = null;
        if (Auth::check()) {
            $activeSubscription = Auth::user()->userSubscriptions()->where('is_active', true)->where('end_date', '>=', now())->first();
            if ($activeSubscription) {
                $currentPackage = DB::table('subscription_packages')->where('id', $activeSubscription->subscription_package_id)->first();
            }
        }

        return view('frontend.pages.pricing', compact('packages', 'easypay', 'flutterwave', 'activeSubscription', 'currentPackage'));
    }

    public function payWithEasypay(Request $request)
    {
        $user = $request->user();
        $packageId = $request->input('package_id');
        $amount = $request->input('amount');
        $phone = $request->input('phone') ?? $user->phone_number;
        $appName = config('app.name');
        $reference = uniqid(strtolower(str_replace(' ', '_', $appName)) . '_order_');
        $reason = $request->input('reason', 'Payment for subscription');
        $currency = $request->input('currency', 'UGX');

        $easypay = \App\Models\EasypayConfiguration::where('is_active', true)->first();
        if (!$easypay) {
            Log::error('Easypay config not found');
            return back()->with('error', 'Easypay configuration not found.');
        }

        $payload = [
            'username' => $easypay->client_id,
            'password' => $easypay->secret,
            'action' => 'mmdeposit',
            'amount' => $amount,
            'currency' => $currency,
            'phone' => $phone,
            'reference' => $reference,
            'reason' => $reason,
        ];
        $apiUrl = 'https://www.easypay.co.ug/api/';
        Log::info('Easypay API Request', ['url' => $apiUrl, 'payload' => $payload]);

        $response = Http::post($apiUrl, $payload);
        Log::info('Easypay API Response', ['response' => $response->body()]);
        $result = $response->json();

        if (isset($result['success']) && $result['success'] == 1) {
            // Store transaction in user_subscriptions
            UserSubscription::create([
                'user_id' => $user->id,
                'subscription_package_id' => $packageId,
                'start_date' => now(),
                'amount_paid' => $amount,
                'payment_status' => 'pending',
                'payment_method' => 'easypay',
                'payment_phone' => $phone,
                'transaction_id' => $result['data']['transactionId'] ?? $reference,
                'is_active' => false,
                'created_by' => $user->id,
            ]);
            return back()->with('success', 'Payment initiated successfully. Please complete the payment on your phone.');
        } else {
            $error = $result['errormsg'] ?? 'Payment failed. Please try again.';
            Log::error('Easypay payment failed', ['error' => $error, 'result' => $result]);
            return back()->with('error', $error);
        }
    }

    public function payWithFlutterwave(Request $request)
    {
        $user = $request->user();
        $packageId = $request->input('package_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency', 'UGX');
        $appName = config('app.name');
        $reference = uniqid(strtolower(str_replace(' ', '_', $appName)) . '_order_');
        $reason = $request->input('reason', 'Payment for subscription');
        $phone = $user->phone_number;
        $email = $user->email;
        $name = $user->name;

        $flutterwave = \App\Models\FlutterwaveSetting::orderByDesc('id')->first();
        if (!$flutterwave) {
            Log::error('Flutterwave config not found');
            return back()->with('error', 'Flutterwave configuration not found.');
        }

        $payload = [
            'tx_ref' => $reference,
            'amount' => $amount,
            'currency' => $currency,
            'redirect_url' => url('/payment/flutterwave/callback'),
            'payment_options' => 'mobilemoneyuganda,card',
            'customer' => [
                'email' => $email,
                'phonenumber' => $phone,
                'name' => $name,
            ],
            'customizations' => [
                'title' => 'Naf Academy Subscription',
                'description' => $reason,
            ],
        ];
        $apiUrl = 'https://api.flutterwave.com/v3/payments';
        Log::info('Flutterwave API Request', ['url' => $apiUrl, 'payload' => $payload]);

        $response = Http::withToken($flutterwave->secret_key)
            ->post($apiUrl, $payload);
        Log::info('Flutterwave API Response', ['response' => $response->body()]);
        $result = $response->json();

        if (isset($result['status']) && $result['status'] === 'success' && isset($result['data']['link'])) {
            // Store transaction in user_subscriptions
            UserSubscription::create([
                'user_id' => $user->id,
                'subscription_package_id' => $packageId,
                'start_date' => now(),
                'amount_paid' => $amount,
                'payment_status' => 'pending',
                'payment_method' => 'flutterwave',
                'payment_phone' => $phone,
                'transaction_id' => $reference,
                'is_active' => false,
                'created_by' => $user->id,
            ]);
            return redirect()->away($result['data']['link']);
        } else {
            $error = $result['message'] ?? 'Flutterwave payment failed. Please try again.';
            Log::error('Flutterwave payment failed', ['error' => $error, 'result' => $result]);
            return back()->with('error', $error);
        }
    }

    public function easypayCallback(Request $request)
    {
        Log::info('Easypay IPN Callback:', $request->all());
        $transactionId = $request->input('transactionId') ?? $request->input('transaction_id');
        $status = $request->input('status') ?? $request->input('success');

        if ($transactionId) {
            $subscription = \App\Models\UserSubscription::where('transaction_id', $transactionId)->first();
            if ($subscription) {
                if ($status == 1 || $status === 'success') {
                    // Find the user's current active subscription
                    $currentActiveSubscription = \App\Models\UserSubscription::where('user_id', $subscription->user_id)
                        ->where('is_active', true)
                        ->where('id', '!=', $subscription->id)
                        ->first();
                    
                    // Get the package to determine duration_days
                    $package = \App\Models\SubscriptionPackage::find($subscription->subscription_package_id);
                    $durationDays = $package ? $package->duration_days : 30; // Default to 30 if package not found
                    
                    // Determine start date and end date for the new subscription
                    if ($currentActiveSubscription && $currentActiveSubscription->end_date > now()) {
                        $startDate = $currentActiveSubscription->end_date;
                        if (is_string($startDate)) {
                            $startDate = Carbon::parse($startDate);
                        }
                        $endDate = $startDate->copy()->addDays($durationDays);
                    } else {
                        $startDate = now();
                        $endDate = now()->copy()->addDays($durationDays);
                    }
                    
                    // Deactivate the old subscription if it exists
                    if ($currentActiveSubscription) {
                        $currentActiveSubscription->is_active = false;
                        $currentActiveSubscription->save();
                    }
                    
                    // Update the new subscription
                    $subscription->payment_status = 'success';
                    $subscription->is_active = true;
                    $subscription->start_date = $startDate;
                    $subscription->end_date = $endDate;
                    $subscription->payment_phone = $subscription->payment_phone ?? (isset($subscription->user) ? $subscription->user->phone_number : null);
                    $subscription->save();
                } else {
                    $subscription->payment_status = 'failed';
                    $subscription->is_active = false;
                    $subscription->save();
                }
            }
        }
        return response()->json(['status' => 'received'], 200);
    }

    public function flutterwaveCallback(Request $request)
    {
        $tx_ref = $request->input('tx_ref');
        $status = $request->input('status');
        $transaction_id = $request->input('transaction_id');

        Log::info('Flutterwave Callback', $request->all());

        if (!$tx_ref) {
            return redirect()->route('pricing')->with('error', 'Missing transaction reference.');
        }

        $flutterwave = \App\Models\FlutterwaveSetting::orderByDesc('id')->first();
        if (!$flutterwave) {
            Log::error('Flutterwave config not found');
            return redirect()->route('pricing')->with('error', 'Flutterwave configuration not found.');
        }

        // Verify transaction with Flutterwave
        $verifyUrl = 'https://api.flutterwave.com/v3/transactions/' . $transaction_id . '/verify';
        $verifyResponse = \Illuminate\Support\Facades\Http::withToken($flutterwave->secret_key)
            ->get($verifyUrl);
        Log::info('Flutterwave Verify Response', ['response' => $verifyResponse->body()]);
        $verifyResult = $verifyResponse->json();

        $subscription = \App\Models\UserSubscription::where('transaction_id', $tx_ref)->first();
        if (!$subscription) {
            return redirect()->route('pricing')->with('error', 'Subscription record not found.');
        }

        if (isset($verifyResult['status']) && $verifyResult['status'] === 'success' &&
            isset($verifyResult['data']['status']) && $verifyResult['data']['status'] === 'successful') {
            // Find the user's current active subscription
            $currentActiveSubscription = \App\Models\UserSubscription::where('user_id', $subscription->user_id)
                ->where('is_active', true)
                ->where('id', '!=', $subscription->id)
                ->first();
            
            // Get the package to determine duration_days
            $package = \App\Models\SubscriptionPackage::find($subscription->subscription_package_id);
            $durationDays = $package ? $package->duration_days : 30; // Default to 30 if package not found
            
            // Determine start date and end date for the new subscription
            if ($currentActiveSubscription && $currentActiveSubscription->end_date > now()) {
                $startDate = $currentActiveSubscription->end_date;
                if (is_string($startDate)) {
                    $startDate = Carbon::parse($startDate);
                }
                $endDate = $startDate->copy()->addDays($durationDays);
            } else {
                $startDate = now();
                $endDate = now()->copy()->addDays($durationDays);
            }
            
            // Deactivate the old subscription if it exists
            if ($currentActiveSubscription) {
                $currentActiveSubscription->is_active = false;
                $currentActiveSubscription->save();
            }
            
            // Update the new subscription
            $subscription->payment_status = 'success';
            $subscription->is_active = true;
            $subscription->start_date = $startDate;
            $subscription->end_date = $endDate;
            $subscription->payment_phone = $subscription->payment_phone ?? (isset($subscription->user) ? $subscription->user->phone_number : null);
            $subscription->save();
            return redirect()->route('pricing')->with('success', 'Payment successful! Your subscription is now active.');
        } else {
            $subscription->payment_status = 'failed';
            $subscription->is_active = false;
            $subscription->save();
            $error = $verifyResult['message'] ?? 'Payment verification failed.';
            return redirect()->route('pricing')->with('error', $error);
        }
    }
} 