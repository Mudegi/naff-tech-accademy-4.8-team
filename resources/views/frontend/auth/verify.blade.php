@extends('frontend.layouts.app')

@section('title', 'Verify Your Account')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Verify Your Account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Please verify your email address and phone number to continue.
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            A new verification link has been sent to your email address.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Email Verification
                </h3>
                <div class="mt-2 max-w-xl text-sm text-gray-500">
                    <p>
                        Please check your email for a verification link. If you did not receive the email,
                        you can request a new one.
                    </p>
                </div>
                <div class="mt-5">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Resend Verification Email
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Phone Verification
                </h3>
                <div class="mt-2 max-w-xl text-sm text-gray-500">
                    <p>
                        Please check your phone for a verification code. If you did not receive the SMS,
                        you can request a new one.
                    </p>
                </div>
                <div class="mt-5">
                    <form method="POST" action="{{ route('verification.phone.send') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Resend Verification SMS
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="text-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection 